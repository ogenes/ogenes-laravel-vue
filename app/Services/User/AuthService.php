<?php

/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2021/7/13
 */

namespace App\Services\User;


use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use App\Mail\UserActiveInvitation;
use App\Models\User\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use function App\Helpers\getRealIp;
use function App\Helpers\getUniqId;

class AuthService extends BaseService
{
    private const
        LOGIN_KEY_PREFIX = 'CYNIC:LOGIN:',
        LOGIN_EXP = 86400 * 30,
        REFRESH_EXP = 600;
    
    public function register(string $username, string $email, string $password): bool
    {
        if (!($username && $email && $password)) {
            throw new CommonException(ErrorCode::INVALID_ARGUMENT);
        }
        
        $exist = User::whereEmail($email)->first();
        if ($exist) {
            if ($exist->is_active === 1) {
                throw new CommonException(ErrorCode::EMAIL_EXISTS);
            }
            
            $this->sendActiveEmail($exist->email, $exist->id);
            return true;
        }
        
        $now = date('Y-m-d H:i:s');
        $userInfo = [
            'username' => $username,
            'email' => $email,
            'password' => md5(env('SALT', '') . $password),
            'created_at' => $now,
        ];
        $userId = User::insertGetId($userInfo);
        $this->sendActiveEmail($email, $userId);
        return true;
    }
    
    /**
     * 激活码前缀
     */
    private const ACTIVE_CODE_PREFIX = 'Osm';
    
    /**
     * @param string $email
     * @param int $userId
     */
    protected function sendActiveEmail(string $email, int $userId): void
    {
        $params['email'] = $email;
        $params['time'] = time();
        $params['userId'] = $userId;
        $code = self::ACTIVE_CODE_PREFIX . encrypt($params);
        $host = env('APP_URL', 'http://img.dev.com');
        $url = $host . '/Users/active?code=' . $code;
        Mail::to($email)->send(new UserActiveInvitation($email, $url));
    }
    
    public function activeUser(string $code): array
    {
        $data = decrypt(substr($code, strlen(self::ACTIVE_CODE_PREFIX)));
        $email = $data['email'];
        $time = $data['time'];
        $userId = $data['userId'];
        $ret['email'] = $email;
        //10分钟有效
        if (time() - $time > 600) {
            $ret['ret'] = 1;
            $ret['msg'] = '激活失败，链接已失效！';
            return $ret;
        }
        $exist = User::whereId($userId)->where('email', '=', $email)->first();
        if (empty($exist)) {
            $ret['ret'] = 1;
            $ret['msg'] = '激活失败，账户异常！';
            return $ret;
        }
        if ($exist->is_active !== 1) {
            $exist->is_active = 1;
            $exist->active_at = date('Y-m-d H:i:s');
            $exist->save();
        }
        $ret['ret'] = 0;
        $ret['msg'] = '激活成功';
        return $ret;
    }
    
    public function login(string $email, string $password): array
    {
        //1. 通过email查询到一条用户记录
        $exist = User::whereEmail($email)->first();
        if (empty($exist) || $exist->is_active !== 1) {
            throw new CommonException(ErrorCode::NO_USER_FOUND);
        }
        
        //2. 密码跟比对
        $plain = md5(env('SALT', '') . $password);
        if ($plain !== $exist->password) {
            throw new CommonException(ErrorCode::PASSWORD_ERROR);
        }
        
        //4. 保存登录信息
        $code = getUniqId(1);
        $loginInfo['last_login_at'] = time();
        $loginInfo['user_id'] = $exist->id;
        $loginInfo['code'] = $code;
        
        $loginInfoJson = json_encode($loginInfo, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        $token = md5($loginInfoJson);
        $loginKey = self::LOGIN_KEY_PREFIX . $token;
        $ret = $this->getRedis()->set(
            $loginKey,
            json_encode($loginInfo, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            self::LOGIN_EXP
        );
        if (!$ret) {
            throw new CommonException(ErrorCode::LOGIN_FAILED);
        }
        
        $userInfo = UserService::getInstance()->getInfoById($exist->id);
        $userInfo['code'] = $code;
        $userInfo['last_login_ip'] = getRealIp();
        UserService::getInstance()->cacheUserInfo($userInfo);
        unset($userInfo['code'], $userInfo['last_login_ip']);
        
        //5. 返回用户信息
        return [
            'token' => $token,
            'userInfo' => $userInfo
        ];
    }
    
    public function checkLogin(string $token): bool
    {
        try {
            $loginKey = self::LOGIN_KEY_PREFIX . $token;
            $cache = $this->getRedis()->get($loginKey);
            if (!$cache) {
                return false;
            }
            $loginInfo = json_decode($cache, true, 512, JSON_THROW_ON_ERROR);
            $userId = $loginInfo['user_id'] ?? 0;
            $lastLoginAt = $loginInfo['last_login_at'] ?? 0;
            $code = $loginInfo['code'] ?? 0;
            
            $diffTime = time() - $lastLoginAt;
            if ($diffTime > self::LOGIN_EXP) {
                $this->getRedis()->del($loginKey);
                return false;
            }
            $this->userId = $userId;
            $userInfo = UserService::getInstance()->getInfoFromCache($userId);
            if (empty($userInfo) || $userInfo['code'] !== $code) {
                return false;
            }
            
            if ($diffTime > self::REFRESH_EXP) {
                $loginInfo['last_login_at'] = time();
                $this->getRedis()->set(
                    $loginKey,
                    json_encode($loginInfo, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                    self::LOGIN_EXP
                );
            }
            return true;
        } catch (\Exception $e) {
            Log::warning('checkLogin', [
                'token' => $token,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return false;
        }
    }
}
