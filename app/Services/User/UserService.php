<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2021/12/29
 */

namespace App\Services\User;


use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use App\Helpers\SmsHelper;
use App\Models\User\User;
use App\Services\BaseService;

class UserService extends BaseService
{

    public function getCurrentUser(): array
    {
        $userInfo = $this->getInfoById($this->userId);
        unset($userInfo['code']);
        return $userInfo;
    }

    public function getInfoById(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }
        $data = $this->getInfoFromCache($userId);
        if ($data) {
            return $data;
        }
        $data = User::whereId($userId)
            ->select([
                'id',
                'username',
                'mobile',
                'email'
            ])
            ->first()
            ->attributesToArray();
        $data['code'] = '';
        $this->cacheUserInfo($data);
        return $data ?: [];
    }

    public const USER_KEY_PREFIX = 'CYNIC:USER:';

    public function cacheUserInfo(array $users): bool
    {
        $userId = $users['id'] ?? 0;
        if ($userId <= 0) {
            return false;
        }
        $cacheKey = self::USER_KEY_PREFIX . $userId;
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

    public function bindMobile(string $mobile, string $code): bool
    {
        if (!SmsHelper::getInstance()->checkCode($mobile, $code)) {
            throw new CommonException(ErrorCode::VERIFICATION_CODE_ERROR);
        }
        $user = User::whereId($this->userId)
            ->first();
        $user->mobile = $mobile;
        $user->save();
        $this->cacheUserField($this->userId, 'mobile', $mobile);
        return true;
    }

    protected function cacheUserField(int $uid, string $field, string $value): void
    {
        $userInfo = $this->getInfoFromCache($uid);
        !$userInfo && $userInfo['id'] = $uid;
        $userInfo[$field] = $value;
        $this->cacheUserInfo($userInfo);
    }

    public function unbindMobile(): bool
    {
        $user = User::whereId($this->userId)
            ->first();
        $user->mobile = '';
        $user->save();
        $this->cacheUserField($this->userId, 'mobile', '');
        return true;
    }
}
