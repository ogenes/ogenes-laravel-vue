<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/3/1
 */

namespace App\Services;


use App\Models\Department;
use App\Models\UserHasDepartment;
use Illuminate\Support\Facades\DB;

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
        $cntMap = $this->getUserCount();
        $ret['cnt'] = $cntMap[self::ROOT_ID] ?? 0;
        $ret['children'] = $this->getChildrenDepartment($ret, $cntMap);
        return [$ret];
    }
    
    protected function getChildrenDepartment(array $dept, array $cntMap): array
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
            $item['cnt'] = $cntMap[$item['id']] ?? 0;
            $tmp = $this->getChildrenDepartment($item, $cntMap);
            $item['parent'] = implode(' / ', $item['parents']);
            $tmp && $item['children'] = $tmp;
            $ret[] = $item;
        }
        return $ret;
    }
    
    public function save(string $name, int $parentId, int $id = 0): bool
    {
        $ret = false;
        DB::beginTransaction();
        try {
            if ($id > 0) {
                $exist = Department::whereId($id)->first();
                if ($exist) {
                    $data = [];
                    $parentId !== $exist->parent_id && $data['parent_id'] = $parentId;
                    $name !== $exist->name && $data['name'] = $name;
                    
                    if ($data) {
                        $data['updated_at'] = date('Y-m-d H:i:s');
                        foreach ($data as $key => $val) {
                            $exist->setAttribute($key, $val);
                        }
                        $ret = $exist->save();
                        ActionLogService::getInstance()->insert(
                            ActionLogService::RESOURCE_DEPARTMENT,
                            $id,
                            $this->uid,
                            '编辑',
                            $data
                        );
                    }
                }
            } else {
                $data = [
                    'parent_id' => $parentId,
                    'name' => $name,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $id = Department::insertGetId($data);
                ActionLogService::getInstance()->insert(
                    ActionLogService::RESOURCE_DEPARTMENT,
                    $id,
                    $this->uid,
                    '新增',
                    $data
                );
                
                $ret = $id > 0;
            }
            DB::commit();
            return $ret;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        
    }
    
    public function remove(int $id): bool
    {
        $ret = false;
        DB::beginTransaction();
        try {
            $exist = Department::whereId($id)->first();
            if ($exist) {
                $ret = $exist->delete();
                ActionLogService::getInstance()->insert(
                    ActionLogService::RESOURCE_DEPARTMENT,
                    $id,
                    $this->uid,
                    '删除',
                    []
                );
            }
            DB::commit();
            return $ret;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        
    }
    
    protected function getUserCount(): array
    {
        $data = UserHasDepartment::select([
            'dept_id',
            DB::raw('count(1) as cnt'),
        ])
            ->groupBy(['dept_id'])
            ->get()
            ->toArray();
        return $data ? array_column($data, 'cnt', 'dept_id') : [];
    }
    
    public function getDepartmentMap(): array
    {
        $data = Department::get([
            'id',
            'name',
            'parent_id as parentId',
        ])
            ->toArray();
        $map = array_column($data, null, 'id');
        $ret = [];
        foreach ($data as $item) {
            $item['parents'] = array_filter($this->getParents($item['parentId'], $map));
            $item['parentName'] = $item['parents'] ? implode(' / ', array_column($item['parents'], 'name')) : '';
            $item['fullName'] = ($item['parentName'] ? $item['parentName'] . ' / ' : '') . $item['name'];
            $ret[$item['id']] = $item;
        }
        return $ret;
    }
    
    protected function getParents(int $parentId, array $map): array
    {
        $parentData = $map[$parentId] ?? [];
        $parents = [];
        $parents[] = $parentData ?? '';
        
        if (isset($parentData['parentId']) && $parentData['parentId'] > 0) {
            $tmpParents = $this->getParents($parentData['parentId'], $map);
            array_unshift($parents, ...$tmpParents);
        }
        return $parents;
    }
}
