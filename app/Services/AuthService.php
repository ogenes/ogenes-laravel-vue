<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/2/28
 */

namespace App\Services;


use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use function App\Helpers\getRealIp;
use function App\Helpers\getUniqId;

class AuthService extends BaseService
{
    private const
        LOGIN_KEY_PREFIX = 'CYNIC:LOGIN:',
        LOGIN_EXP = 86400 * 30,
        REFRESH_EXP = 600;
    
    /**
     * @param string $account
     * @param string $password
     * @return array
     * @throws CommonException
     */
    public function login(string $account, string $password): array
    {
        //1. 通过account查询到一条用户记录
        $exist = User::whereAccount($account)->first();
        if (empty($exist) || $exist->user_status !== 1) {
            throw new CommonException(ErrorCode::NO_USER_FOUND);
        }
        
        //2. 密码跟比对
        $plain = md5(env('SALT', '') . $password);
        if ($plain !== $exist->password) {
            throw new CommonException(ErrorCode::PASSWORD_ERROR);
        }
        
        $now = date('Y-m-d H:i:s');
        //4. 保存登录信息
        $code = getUniqId(1);
        $loginInfo['last_login_ip'] = getRealIp();
        $loginInfo['last_login_at'] = $now;
        $loginInfo['uid'] = $exist->uid;
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
        
        $userInfo = UserService::getInstance()->getInfoById($exist->uid);
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
    
    /**
     * @param string $token
     * @return bool
     */
    public function checkLogin(string $token): bool
    {
        try {
            $loginKey = self::LOGIN_KEY_PREFIX . $token;
            $cache = $this->getRedis()->get($loginKey);
            if (!$cache) {
                return false;
            }
            $loginInfo = json_decode($cache, true, 512, JSON_THROW_ON_ERROR);
            $uid = $loginInfo['uid'] ?? 0;
            $lastLoginAt = $loginInfo['last_login_at'] ?? 0;
            $code = $loginInfo['code'] ?? 0;
            
            $diffTime = time() - strtotime($lastLoginAt);
            if ($diffTime > self::LOGIN_EXP) {
                $this->getRedis()->del($loginKey);
                return false;
            }
            $this->uid = $uid;
            $userInfo = UserService::getInstance()->getInfoFromCache($uid);
            if (empty($userInfo) || $userInfo['code'] !== $code) {
                return false;
            }
            
            if ($diffTime > self::REFRESH_EXP) {
                $loginInfo['last_login_at'] = date('Y-m-d H:i:s');
                $loginInfo['last_login_ip'] = getRealIp();
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
    
    /**
     * @param string $token
     * @return bool
     */
    public function logout(string $token): bool
    {
        $loginKey = self::LOGIN_KEY_PREFIX . $token;
        $this->getRedis()->del($loginKey);
        return true;
    }
}
