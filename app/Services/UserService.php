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
use App\Models\UserHasDepartment;
use App\Models\UserHasRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function App\Helpers\formatDateTime;
use function App\Helpers\getRandStr;

class UserService extends BaseService
{
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
    
    public const USER_KEY_PREFIX = 'OG:USER:';
    
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
    
    public function getList(
        string $username,
        string $account,
        string $userStatus,
        string $mobile,
        array $deptIds,
        array $sort = [],
        int $page = 1,
        int $pageSize = 20
    ): array
    {
        $ret = [
            'cnt' => 0,
            'list' => [],
            'page' => $page,
            'pageSize' => $pageSize,
        ];
        $userTb = (new User())->getTable();
        $userHasDepartmentTb = (new UserHasDepartment())->getTable();
        $userHasRoleTb = (new UserHasRole())->getTable();
        $query = DB::table("{$userTb} as u")
            ->leftJoin("{$userHasDepartmentTb} as uhd", 'u.uid', '=', 'uhd.uid')
            ->leftJoin("{$userHasRoleTb} as uhr", 'u.uid', '=', 'uhr.uid')
            ->select([
                'u.*',
                DB::raw('GROUP_CONCAT(DISTINCT uhd.dept_id) as dept_ids'),
                DB::raw('GROUP_CONCAT(DISTINCT uhr.role_id) as role_ids'),
            ]);
        $username && $query->where('username', 'like', "%{$username}%");
        $account && $query->where('account', 'like', "%{$account}%");
        $mobile && $query->where('mobile', 'like', "%{$mobile}%");
        $deptIds && $query->whereIn('uhd.dept_id', $deptIds);
        $userStatus !== '' && $query->where('user_status', '=', $userStatus);
        
        $prop = 'uid';
        $order = 'desc';
        if (isset($sort['prop'])) {
            $prop = Str::snake($sort['prop']);
        }
        if (isset($sort['order']) && $sort['order'] === 'ascending') {
            $order = 'asc';
        }
        
        $query->groupBy(['u.uid'])->orderBy($prop, $order);
        $resp = $query->paginate($pageSize, ['*'], 'page', $page)->toArray();
        if (empty($resp)) {
            return $ret;
        }
        
        $ret['cnt'] = $resp['total'];
        $ret['page'] = $resp['current_page'];
        $ret['pageSize'] = $resp['per_page'];
        
        $departmentMap = DepartmentService::getInstance()->getDepartmentMap();
        $roleMap = RoleService::getInstance()->getRoleMap();
        foreach ($resp['data'] as $item) {
            $item = json_decode(json_encode($item, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            $item['last_login_at'] = formatDateTime($item['last_login_at']);
            $item['created_at'] = formatDateTime($item['created_at']);
            $item['updated_at'] = formatDateTime($item['updated_at']);
            $deptIds = $item['dept_ids'];
            $item['dept_id_arr'] = array_filter(explode(',', $deptIds));
            sort($item['dept_id_arr']);
            $item['departments'] = [];
            foreach ($item['dept_id_arr'] as $deptId) {
                $tmpDepartment = $departmentMap[$deptId] ?? [];
                $item['departments'][] = $tmpDepartment['fullName'] ?? '';
            }
            $roleIds = $item['role_ids'];
            $item['role_id_arr'] = array_filter(explode(',', $roleIds));
            sort($item['role_id_arr']);
            $item['roles'] = [];
            foreach ($item['role_id_arr'] as $roleId) {
                $tmpRole = $roleMap[$roleId] ?? [];
                $item['roles'][] = $tmpRole['fullName'] ?? '';
            }
            $tmp = [];
            foreach ($item as $key => $value) {
                $tmp[Str::camel($key)] = $value;
            }
            $ret['list'][] = $tmp;
        }
        return $ret;
        
    }
    
    
    /**
     * @param int $uid
     * @param string $avatar
     * @param string $username
     * @param string $mobile
     * @param string $email
     * @param array $deptIds
     * @return bool
     * @throws \Throwable
     */
    public function save(
        int $uid,
        string $avatar,
        string $username,
        string $mobile,
        string $email,
        array $deptIds
    ): bool
    {
        if (User::whereMobile($mobile)->where('uid', '!=', $uid)->exists()) {
            throw new CommonException(ErrorCode::MOBILE_EXISTS);
        }
        DB::beginTransaction();
        try {
            if ($uid > 0) {
                $exist = User::where('uid', '=', $uid)->first();
                if (!$exist) {
                    throw new CommonException(ErrorCode::NO_USER_FOUND);
                }
                $data = [];
                $avatar !== $exist->avatar && $data['avatar'] = $avatar;
                $mobile !== $exist->account && $data['account'] = $mobile;
                $username !== $exist->username && $data['username'] = $username;
                $mobile !== $exist->mobile && $data['mobile'] = $mobile;
                $email !== $exist->email && $data['email'] = $email;
                if ($data) {
                    $data['updated_at'] = date('Y-m-d H:i:s');
                    foreach ($data as $key => $val) {
                        $exist->setAttribute($key, $val);
                        $this->cacheUserField($uid, $key, $val);
                    }
                    $exist->save();
                    ActionLogService::getInstance()->insert(
                        ActionLogService::RESOURCE_USER,
                        $uid,
                        $this->uid,
                        '编辑',
                        $data
                    );
                }
            } else {
                $data = [
                    'avatar' => $avatar,
                    'username' => $username,
                    'account' => $mobile,
                    'mobile' => $mobile,
                    'email' => $email,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $uid = User::insertGetId($data);
                ActionLogService::getInstance()->insert(
                    ActionLogService::RESOURCE_USER,
                    $uid,
                    $this->uid,
                    '新增',
                    $data
                );
            }
            $ret = $this->saveUserHasDepartment($uid, $deptIds);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $ret;
    }
    
    protected function saveUserHasDepartment(int $uid, array $deptIds): bool
    {
        $exists = UserHasDepartment::whereUid($uid)
            ->get()
            ->toArray();
        if ($exists) {
            $existIds = array_column($exists, 'dept_id');
            $addIds = array_diff($deptIds, $existIds);
            $delIds = array_diff($existIds, $deptIds);
            UserHasDepartment::whereUid($uid)
                ->whereIn('dept_id', $delIds)
                ->delete();
        } else {
            $delIds = [];
            $addIds = $deptIds;
        }
        $insertData = [];
        foreach ($addIds as $deptId) {
            $insertData[] = [
                'uid' => $uid,
                'dept_id' => $deptId,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }
        $insertData && UserHasDepartment::insert($insertData);
        if ($delIds || $addIds) {
            $data['delIds'] = $delIds;
            $data['addIds'] = $addIds;
            ActionLogService::getInstance()->insert(
                ActionLogService::RESOURCE_USER,
                $uid,
                $this->uid,
                '修改部门',
                $data
            );
        }
        
        return true;
    }
    
    public function saveUserHasRole(int $uid, array $roleIds): bool
    {
        DB::beginTransaction();
        try {
            $exists = UserHasRole::whereUid($uid)
                ->get()
                ->toArray();
            $data = [];
            if ($exists) {
                $existIds = array_column($exists, 'role_id');
                $addIds = array_diff($roleIds, $existIds);
                $delIds = array_diff($existIds, $roleIds);
                UserHasRole::whereUid($uid)
                    ->whereIn('role_id', $delIds)
                    ->delete();
            } else {
                $delIds = [];
                $addIds = $roleIds;
            }
            $insertData = [];
            foreach ($addIds as $roleId) {
                $insertData[] = [
                    'role_id' => $roleId,
                    'uid' => $uid,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
            $insertData && UserHasRole::insert($insertData);
            if ($delIds || $addIds) {
                $data['delIds'] = $delIds;
                $data['addIds'] = $addIds;
                ActionLogService::getInstance()->insert(
                    ActionLogService::RESOURCE_USER,
                    $uid,
                    $this->uid,
                    '修改角色',
                    $data
                );
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function switchStatus(int $uid, int $userStatus): bool
    {
        $exists = User::whereUid($uid)->first();
        if (!$exists) {
            throw new CommonException(ErrorCode::NO_USER_FOUND);
        }
        
        if (!array_key_exists($userStatus, User::STATUS_MAP)) {
            throw new CommonException(ErrorCode::INVALID_ARGUMENT);
        }
        
        if ($exists->user_status !== $userStatus) {
            $data['user_status'] = $userStatus;
            $data['updated_at'] = date('Y-m-d H:i:s');
            foreach ($data as $key => $val) {
                $exists->setAttribute($key, $val);
            }
            $exists->save();
            ActionLogService::getInstance()->insert(
                ActionLogService::RESOURCE_USER,
                $uid,
                $this->uid,
                '切换状态',
                $data
            );
        }
        return true;
        
    }
    
    public function resetPassByUid(int $uid): array
    {
        $exists = User::whereUid($uid)->first();
        if (!$exists) {
            throw new CommonException(ErrorCode::NO_USER_FOUND);
        }
        $password = getRandStr(8, ['number', 'lower']);
        $plain = md5(env('SALT', '') . $password);
        if ($plain !== $exists->password) {
            $data['password'] = $plain;
            $data['updated_at'] = date('Y-m-d H:i:s');
            foreach ($data as $key => $val) {
                $exists->setAttribute($key, $val);
            }
            $exists->save();
            $data['password'] = '***……';
            ActionLogService::getInstance()->insert(
                ActionLogService::RESOURCE_USER,
                $uid,
                $this->uid,
                '重置密码',
                $data
            );
        }
        return [
            'password' => $password,
        ];
    }
    
    public function getAllUsers(): array
    {
        return User::get([
            'username',
            'user_status',
            'uid'
        ])->toArray();
    }
    
    public function getDepartmentHasUser(int $deptId): array
    {
        $userTb = (new User())->getTable();
        $userHasDepartmentTb = (new UserHasDepartment())->getTable();
        return DB::table("{$userHasDepartmentTb} as uhd")
            ->leftJoin("{$userTb} as u", 'u.uid', '=', 'uhd.uid')
            ->select([
                'u.uid',
                'u.account',
                'u.username',
            ])
            ->where('u.user_status', '=', 1)
            ->where('uhd.dept_id', '=', $deptId)
            ->get()
            ->toArray();
    }
}
