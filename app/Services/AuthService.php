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
use App\Services\Permission\MenuService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function App\Helpers\filterTree;
use function App\Helpers\getRealIp;
use function App\Helpers\getUniqId;
use function App\Helpers\transPass;

class AuthService extends BaseService
{
    private const
        LOGIN_KEY_PREFIX = 'CYNIC:LOGIN:',
        LOGIN_EXP = 86400 * 30,
        REFRESH_EXP = 300;
    
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
        $plain = transPass($password);
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
        $this->updateLoginInfo($exist->uid, $loginInfo['last_login_at'], $loginInfo['last_login_ip']);
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
            if (empty($userInfo)) {
                return false;
            }
            
            if ($userInfo['code'] !== $code && config('common.loginUnique')) {
                return false;
            }
            
            if ($diffTime > self::REFRESH_EXP) {
                $loginInfo['last_login_at'] = date('Y-m-d H:i:s');
                $loginInfo['last_login_ip'] = getRealIp();
                $this->updateLoginInfo($uid, $loginInfo['last_login_at'], $loginInfo['last_login_ip']);
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
    
    public function updateLoginInfo(int $uid, string $loginAt, string $loginIp): void
    {
        User::whereUid($uid)->update([
            'last_login_at' => $loginAt,
            'last_login_ip' => $loginIp,
            'updated_at' => DB::raw('`updated_at`')
        ]);
    }
    
    public function getCurrentUser(): array
    {
        $userInfo = UserService::getInstance()->getInfoById($this->uid);
        $menus = MenuService::getInstance()->getUserHasMenus($this->uid, 1);
        $userInfo['roles'] = array_column($menus, 'menuName');
        unset($userInfo['code'], $userInfo['last_login_ip']);
        return $userInfo;
    }
    
    public function getRoleTree(): array
    {
        $roleTree = RoleService::getInstance()->getRoleTree();
        $roles = RoleService::getInstance()->getUserHasRoles($this->uid);
        $roleIds = array_column($roles, 'roleId');
        filterTree($roleTree, $roleIds);
        return $roleTree;
    }
    
    public function getMenuTree(): array
    {
        $menuTree = MenuService::getInstance()->getList(1);
        $menus = MenuService::getInstance()->getUserHasMenus($this->uid, 1);
        $menuIds = array_column($menus, 'menuId');
        filterTree($menuTree, $menuIds);
        return $menuTree;
    }
    
    public function updateBasicInfo(string $username, string $mobile, string $email): bool 
    {
        return true;
    }
    public function updateAvatar(string $avatar): bool
    {
        return true;
    }
    public function updatePass(string $password, string $oldPassword): bool
    {
        $exist = User::whereUid($this->uid)->first();
        if ($exist->password !== transPass($oldPassword)) {
            throw new CommonException(ErrorCode::PASSWORD_ERROR);
        }
    
        $newPlain = transPass($password);
        if ($newPlain === $exist->password) {
            return true;
        }
        $data['password'] = $newPlain;
        $data['updated_at'] = date('Y-m-d H:i:s');
        foreach ($data as $key => $val) {
            $exist->setAttribute($key, $val);
        }
        $exist->save();
        $data['password'] = '***……';
        ActionLogService::getInstance()->insert(
            ActionLogService::RESOURCE_USER,
            $this->uid,
            $this->uid,
            '用户修改密码',
            $data
        );
        return true;
    }
}
