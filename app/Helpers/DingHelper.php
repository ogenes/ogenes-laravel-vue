<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/3/1
 */

namespace App\Helpers;


use App\Models\Department;
use App\Models\DepartmentDing;
use App\Models\User;
use App\Models\UserDing;
use App\Models\UserHasDepartment;
use App\Services\BaseService;

class DingHelper extends BaseService
{
    protected string $appKey;
    protected string $appSecret;
    
    protected function __construct()
    {
        $config = config('param.ding');
        $this->appKey = $config['appKey'] ?? '';
        $this->appSecret = $config['appSecret'] ?? '';
    }
    
    protected function getToken(): string
    {
        $uri = 'https://oapi.dingtalk.com/gettoken';
        $params['appkey'] = $this->appKey;
        $params['appsecret'] = $this->appSecret;
        $resp = CurlHelper::get($uri, $params);
        $ret = json_decode($resp, true, 512, JSON_THROW_ON_ERROR);
        return $ret['access_token'] ?? '';
    }
    
    public function syncDepartment(int $deptId = 1): array
    {
        
        $token = $this->getToken();
        $uri = "https://oapi.dingtalk.com/topapi/v2/department/listsub?access_token={$token}";
        $params['dept_id'] = $deptId;
        $params['language'] = 'zh_CN';
        $resp = CurlHelper::post($uri, $params);
        $data = json_decode($resp, true, 512, JSON_THROW_ON_ERROR);
        $result = $data['result'] ?? [];
        if (empty($result)) {
            return [];
        }
        $ret = [];
        foreach ($result as $item) {
            $this->saveDepartmentDing($item['dept_id'], $item['parent_id'], $item['name']);
            echo $item['dept_id'] . ' ';
            echo $item['name'] . PHP_EOL;
            $tmp = $this->syncDepartment($item['dept_id']);
            $tmp && $item['children'] = $tmp;
            $ret[] = $item;
        }
        return $ret;
    }
    
    public function saveDepartmentDing(int $dingDeptId, int $dingParentId, string $name): bool
    {
        $parentId = DepartmentDing::whereDingDeptId($dingParentId)->first()->dept_id;
        
        $exist = DepartmentDing::whereDingDeptId($dingDeptId)->first();
        if ($exist) {
            $exist->setAttribute('parent_id', $parentId);
            $exist->setAttribute('ding_parent_id', $dingParentId);
            $exist->setAttribute('name', $name);
            $exist->save();
    
    
            $departmentModel = Department::whereId($exist->dept_id)->first();
            $departmentModel->setAttribute('parent_id', $parentId);
            $departmentModel->setAttribute('name', $name);
            $departmentModel->save();
        } else {
            $deptId = Department::insertGetId([
                'parent_id' => $parentId,
                'name' => $name,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            DepartmentDing::insertGetId([
                'dept_id' => $deptId,
                'ding_dept_id' => $dingDeptId,
                'parent_id' => $parentId,
                'ding_parent_id' => $dingParentId,
                'name' => $name,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        return true;
    }
    
    
    public function syncUser(int $deptId): bool
    {
        $token = $this->getToken();
        $uri = "https://oapi.dingtalk.com/topapi/v2/user/list?access_token={$token}";
        $params['dept_id'] = $deptId;
        $params['language'] = 'zh_CN';
        $params['cursor'] = 0;
        $params['size'] = 10;
        
        do {
            $resp = CurlHelper::post($uri, $params);
            $data = json_decode($resp, true, 512, JSON_THROW_ON_ERROR);
            $result = $data['result'] ?? [];
            if (empty($result)) {
                break;
            }
            foreach ($result['list'] as $item) {
                $this->saveUser($item, $deptId);
            }
            if (!$result['has_more']) {
                break;
            }
            $params['cursor'] = $result['next_cursor'];
        } while (true);
        
        return true;
    }
    
    protected function saveUser(array $user, int $dingDeptId): bool
    {
        $now = date('Y-m-d H:i:s');
        
        $hireTime = substr($user['hired_date'] ?? '', 0, 10);
        $data = [
            'userid' => $user['userid'] ?? '',
            'unionid' => $user['unionid'] ?? '',
            'name' => $user['name'] ?? '',
            'mobile' => $user['mobile'] ?? '',
            'email' => $user['email'] ?? '',
            'title' => $user['title'] ?? '',
            'boss' => $user['boss'] ?? '',
            'active' => $user['active'] ?? '',
            'leader' => $user['leader'] ?? '',
            'avatar' => $user['avatar'] ?? '',
        ];
        $hireTime && $data['hired_date'] = date('Y-m-d H:i:s', $hireTime);
        $deptId = DepartmentDing::whereDingDeptId($dingDeptId)->first()->dept_id;
        $exist = UserDing::whereUserid($user['userid'])->first();
        if ($exist) {
            $exist->update($data);
            $uid = $exist->uid;
        } else {
            $userExist = User::whereMobile($user['mobile'])->first();
            if ($userExist) {
                $uid = $userExist->uid;
            } else {
                $uid = User::insertGetId([
                    'account' => $user['mobile'] ?? '',
                    'username' => $user['name'] ?? '',
                    'mobile' => $user['mobile'] ?? '',
                    'email' => $user['email'] ?? '',
                    'avatar' => $user['avatar'] ?? '',
                    'created_at' => $now,
                ]);
            }
            $data['uid'] = $uid;
            $data['created_at'] = $now;
            UserDing::insertGetId($data);
        }
        //关联关系
        $relation = UserHasDepartment::whereUid($uid)
            ->where('dept_id', '=', $deptId)
            ->first();
        if (empty($relation)) {
            UserHasDepartment::insertGetId([
                'uid' => $uid,
                'dept_id' => $deptId,
                'created_at' => $now,
            ]);
        }
        
        return true;
    }
}
