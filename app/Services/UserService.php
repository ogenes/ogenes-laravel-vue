<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/2/28
 */

namespace App\Services;


use App\Models\User;

class UserService extends BaseService
{
    public function getCurrentUser(): array
    {
        $userInfo = $this->getInfoById($this->uid);
        $userInfo['roles'] = ['admin'];
        unset($userInfo['code'], $userInfo['last_login_ip']);
        return $userInfo;
    }
    
    public function getInfoById(int $uid): array
    {
        if ($uid <= 0) {
            return [];
        }
        $data = $this->getInfoFromCache($uid);
        if ($data) {
            return $data;
        }
        $data = User::whereUid($uid)
            ->where('user_status', '=', 1)
            ->select([
                'uid',
                'account',
                'username',
                'mobile',
                'email',
                'avatar',
            ])
            ->first()
            ->toArray();
        $data['code'] = '';
        $this->cacheUserInfo($data);
        return $data ?: [];
    }
    
    public const USER_KEY_PREFIX = 'CYNIC:USER:';
    
    public function cacheUserInfo(array $users): bool
    {
        $uid = $users['uid'] ?? 0;
        if ($uid <= 0) {
            return false;
        }
        $cacheKey = self::USER_KEY_PREFIX . $uid;
        return $this->getRedis()->set(
            $cacheKey,
            json_encode($users, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            86400 * 7
        );
    }
    
    public function getInfoFromCache(int $uid)
    {
        $cacheKey = self::USER_KEY_PREFIX . $uid;
        $cache = $this->getRedis()->get($cacheKey);
        if (empty($cache)) {
            return [];
        }
        return json_decode($cache, true, 512, JSON_THROW_ON_ERROR);
    }
    
    protected function cacheUserField(int $uid, string $field, string $value): void
    {
        $userInfo = $this->getInfoFromCache($uid);
        !$userInfo && $userInfo['id'] = $uid;
        $userInfo[$field] = $value;
        $this->cacheUserInfo($userInfo);
    }
    
}
