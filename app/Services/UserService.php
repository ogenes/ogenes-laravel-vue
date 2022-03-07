<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/2/28
 */

namespace App\Services;


use App\Models\User;
use App\Models\UserHasDepartment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function App\Helpers\formatDateTime;

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
    
    public function getList(string $username, string $userStatus, string $mobile, array $deptIds, int $page, int $pageSize): array
    {
        $ret = [
            'cnt' => 0,
            'list' => [],
            'page' => $page,
            'pageSize' => $pageSize,
        ];
        $userTb = (new User())->getTable();
        $userHasDepartmentTb = (new UserHasDepartment())->getTable();
        $query = DB::table("{$userTb} as u")
            ->leftJoin("{$userHasDepartmentTb} as uhd", 'u.uid', '=', 'uhd.uid')
            ->select([
                'u.*',
                DB::raw('GROUP_CONCAT(uhd.dept_id) as dept_ids')
            ]);
        $username && $query->where('username', 'like', "%{$username}%");
        $mobile && $query->where('mobile', 'like', "%{$mobile}%");
        $deptIds && $query->whereIn('uhd.dept_id', $deptIds);
        $userStatus !== '' && $query->where('user_status', '=', $userStatus);
        
        $query->groupBy(['u.uid'])->orderBy('u.uid', 'desc');
        $resp = $query->paginate($pageSize, ['*'], 'page', $page)->toArray();
        if (empty($resp)) {
            return $ret;
        }
        
        $ret['cnt'] = $resp['total'];
        $ret['page'] = $resp['current_page'];
        $ret['pageSize'] = $resp['per_page'];
        
        $departmentMap = DepartmentService::getInstance()->getDepartmentMap();
        foreach ($resp['data'] as $item) {
            $item = json_decode(json_encode($item, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            $item['last_login_at'] = formatDateTime($item['last_login_at']);
            $item['created_at'] = formatDateTime($item['created_at']);
            $item['updated_at'] = formatDateTime($item['updated_at']);
            $deptIds = $item['dept_ids'];
            $item['dept_id_arr'] = explode(',', $deptIds);
            sort($item['dept_id_arr']);
            foreach ($item['dept_id_arr'] as $deptId) {
                $tmpDepartment = $departmentMap[$deptId] ?? [];
                $item['departments'][] = $tmpDepartment['fullName'] ?? '';
            }
            $tmp = [];
            foreach ($item as $key => $value) {
                $tmp[Str::camel($key)] = $value;
            }
            $ret['list'][] = $tmp;
        }
        return $ret;
        
    }
    
    protected function cacheUserField(int $uid, string $field, string $value): void
    {
        $userInfo = $this->getInfoFromCache($uid);
        !$userInfo && $userInfo['id'] = $uid;
        $userInfo[$field] = $value;
        $this->cacheUserInfo($userInfo);
    }
    
    public function updateLoginInfo(int $uid, string $loginAt, string $loginIp): void
    {
        User::whereUid($uid)->update([
            'last_login_at' => $loginAt,
            'last_login_ip' => $loginIp,
            'updated_at' => DB::raw('`updated_at`')
        ]);
    }
    
}
