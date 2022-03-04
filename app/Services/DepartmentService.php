<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/3/1
 */

namespace App\Services;


use App\Models\Department;

class DepartmentService extends BaseService
{
    public const ROOT_ID = 1;
    
    public function getList(): array
    {
        $exist = Department::whereId(self::ROOT_ID)
            ->select([
                'id',
                'name',
                'parent_id as parentId',
            ])
            ->first();
        $ret = $exist ? $exist->toArray() : [];
        if (!$ret) {
            return [];
        }
        $ret['level'] = 1;
        $ret['parents'] = [];
        $ret['parent'] = '/';
        $ret['children'] = $this->getChildrenDepartment($ret);
        return [$ret];
    }
    
    protected function getChildrenDepartment(array $dept): array
    {
        $data = Department::whereParentId($dept['id'])
            ->get([
                'id',
                'name',
                'parent_id as parentId',
            ])
            ->toArray();
        if (!$data) {
            return [];
        }
        $ret = [];
        foreach ($data as $item) {
            $item['parents'] = $dept['parents'];
            $item['parents'][] = $dept['name'];
            $item['level'] = count($item['parents']) + 1;
            $tmp = $this->getChildrenDepartment($item);
            $item['parent'] = implode(' / ', $item['parents']);
            $tmp && $item['children'] = $tmp;
            $ret[] = $item;
        }
        return $ret;
    }
    
    public function save(string $name, int $parentId, int $id = 0): bool
    {
        $ret = false;
        if ($id > 0) {
            $exist = Department::whereId($id)->first();
            if ($exist) {
                $ret =$exist->update([
                    'parent_id' => $parentId,
                    'name' => $name,
                ]);
            }
        } else {
            $ret = Department::insertGetId([
                'ding_dept_id' => 0,
                'parent_id' => $parentId,
                'ding_parent_id' => 0,
                'name' => $name,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        return $ret;
    }
    
    public function remove(int $id):bool 
    {
        $ret = false;
        //check
        $exist = Department::whereId($id)->first();
        if ($exist) {
            $ret =$exist->delete();
        }
        return $ret;
    }
    
    public function saveDepartment(int $dingDeptId, int $dingParentId, string $name): bool
    {
        $parentId = Department::whereDingDeptId($dingParentId)->first()->id;
        $exist = Department::whereDingDeptId($dingDeptId)->first();
        if ($exist) {
            $exist->update([
                'parent_id' => $parentId,
                'ding_parent_id' => $dingParentId,
                'name' > $name,
            ]);
        } else {
            Department::insertGetId([
                'ding_dept_id' => $dingDeptId,
                'parent_id' => $parentId,
                'ding_parent_id' => $dingParentId,
                'name' => $name,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        return true;
    }
}
