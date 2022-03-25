<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/2/8
 */

namespace App\Services;


use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleHasData;
use App\Models\RoleHasMenu;
use App\Models\UserHasRole;
use App\Services\Permission\MenuService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function App\Helpers\formatDateTime;

class RoleService extends BaseService
{
    public function getList(
        string $name,
        string $roleStatus,
        array $menuIds,
        array $parentIds,
        int $page,
        int $pageSize
    ): array
    {
        $ret = [
            'cnt' => 0,
            'list' => [],
            'page' => $page,
            'pageSize' => $pageSize,
        ];
        $roleTb = (new Role())->getTable();
        $roleHasMenuTb = (new RoleHasMenu())->getTable();
        $roleHasDataTb = (new RoleHasData())->getTable();
        $userHasRoleTb = (new UserHasRole())->getTable();
        $query = DB::table("{$roleTb} as r")
            ->leftJoin("{$roleHasMenuTb} as rhm", 'r.id', '=', 'rhm.role_id')
            ->leftJoin("{$roleHasDataTb} as rhd", 'r.id', '=', 'rhd.role_id')
            ->leftJoin("{$userHasRoleTb} as uhr", 'r.id', '=', 'uhr.role_id')
            ->select([
                'r.*',
//                DB::raw('GROUP_CONCAT(rhm.menu_id) as menus'),
//                DB::raw('GROUP_CONCAT(rhd.data_id) as datas'),
//                DB::raw('GROUP_CONCAT(uhr.uid) as users'),
            ]);
        $name && $query->where('r.role_name', 'like', "%{$name}%");
        $roleStatus !== '' && $query->where('r.role_status', '=', $roleStatus);
        $parentIds && $query->whereIn('r.parent_id', $parentIds);
        $menuIds && $query->whereIn('rhm.menu_id', $menuIds);
        
        $query->groupBy(['r.id'])->orderBy('r.id', 'asc');
        $resp = $query->paginate($pageSize, ['*'], 'page', $page)->toArray();
        if (empty($resp)) {
            return $ret;
        }
        
        $ret['cnt'] = $resp['total'];
        $ret['page'] = $resp['current_page'];
        $ret['pageSize'] = $resp['per_page'];
        
        $roleIds = array_column($resp['data'], 'id');
        $roleMap = $this->getRoleMap($roleIds);
        $roleHasMenuMap = $this->getRoleHasMenuMap($roleIds);
        $menuTreeMap = [];
        foreach (MenuService::SYSTEM as $systemId => $val) {
            $menuTreeMap[$systemId] = MenuService::getInstance()->getList($systemId);
        }
        foreach ($resp['data'] as $item) {
            $item = json_decode(json_encode($item, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            $item['created_at'] = formatDateTime($item['created_at']);
            $item['updated_at'] = formatDateTime($item['updated_at']);
            $item['parent'] = $roleMap[$item['id']]['parent'] ?? '';
            $menuMap = $roleHasMenuMap[$item['id']] ?? [];
            foreach (MenuService::SYSTEM  as $systemId => $systemName) {
                $systemInfo['systemId'] = $systemId;
                $systemInfo['systemName'] = $systemName;
                $map = $menuMap[$systemId] ?? [];
                $systemInfo['menuIds'] = array_column($map, 'menu_id');
                $menuTree = $menuTreeMap[$systemId] ?? [];
                $this->filterMenu($menuTree, $systemInfo['menuIds']);
                $systemInfo['menuTree'] = $menuTree;
                $item['system'][$systemId] = $systemInfo;
            }
            $item['dataIds'] = [];
            $item['dataTree'] = [];
            $tmp = [];
            foreach ($item as $key => $value) {
                $tmp[Str::camel($key)] = $value;
            }
            $ret['list'][] = $tmp;
        }
        return $ret;
    }
    
    public function getRoleTree(): array
    {
        $exists = Role::whereParentId(0)
            ->get()
            ->toArray();
        if (!$exists) {
            return [];
        }
        $ret = [];
        foreach ($exists as $item) {
            $item['parent'] = '';
            $item['parents'] = [];
            $tmp = $this->getChildrenRole($item);
            $tmp && $item['children'] = $tmp;
            $item['created_at'] = formatDateTime($item['created_at']);
            $item['updated_at'] = formatDateTime($item['updated_at']);
            $upperItem = [];
            foreach ($item as $key => $value) {
                $upperItem[Str::camel($key)] = $value;
            }
            $ret[] = $upperItem;
        }
        return $ret;
    }
    
    protected function getChildrenRole(array $role): array
    {
        $data = Role::whereParentId($role['id'])
            ->get()
            ->toArray();
        if (!$data) {
            return [];
        }
        $ret = [];
        foreach ($data as $item) {
            $item['parents'] = $role['parents'];
            $item['parents'][] = $role['role_name'];
            $item['level'] = count($item['parents']) + 1;
            $item['created_at'] = formatDateTime($item['created_at']);
            $item['updated_at'] = formatDateTime($item['updated_at']);
            $tmp = $this->getChildrenRole($item);
            $item['parent'] = implode(' / ', $item['parents']);
            $tmp && $item['children'] = $tmp;
            $upperItem = [];
            foreach ($item as $key => $value) {
                $upperItem[Str::camel($key)] = $value;
            }
            $ret[] = $upperItem;
        }
        return $ret;
    }
    
    public function save(string $name, string $desc, int $parentId, int $id = 0): bool
    {
        $exists = Role::whereRoleName($name)
            ->where('id', '!=', $id)
            ->first();
        if ($exists) {
            throw new CommonException(ErrorCode::RECORD_EXISTS);
        }
        $ret = false;
        if ($id > 0) {
            $exist = Role::whereId($id)->first();
            if ($exist) {
                $exist->setAttribute('parent_id', $parentId);
                $exist->setAttribute('role_name', $name);
                $exist->setAttribute('desc', $desc);
                $ret = $exist->save();
            }
        } else {
            $ret = Role::insertGetId([
                'parent_id' => $parentId,
                'role_name' => $name,
                'desc' => $desc,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        return $ret;
    }
    
    public function saveRoleHasMenu(int $roleId, int $systemId, array $menuIds): bool
    {
        $menuTb = (new Menu())->getTable();
        $roleHasMenuTb = (new RoleHasMenu())->getTable();
        $exists = DB::table("{$roleHasMenuTb} as rhm")
            ->join("{$menuTb} as m", 'm.id', '=', 'rhm.menu_id')
            ->where('m.system_id', '=', $systemId)
            ->where('rhm.role_id', '=', $roleId)
            ->get(['rhm.*'])
            ->toArray();
        if ($exists) {
            $existIds = array_column($exists, 'menu_id');
            $addIds = array_diff($menuIds, $existIds);
            $delIds = array_diff($existIds, $menuIds);
            RoleHasMenu::whereRoleId($roleId)
                ->whereIn('menu_id', $delIds)
                ->delete();
        } else {
            $addIds = $menuIds;
        }
        $insertData = [];
        foreach ($addIds as $menuId) {
            $insertData[] = [
                'role_id' => $roleId,
                'menu_id' => $menuId,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }
        RoleHasMenu::insert($insertData);
        return true;
    }
    
    public function saveRoleHasData(int $roleId, array $dataIds): bool
    {
        $exists = RoleHasData::whereRoleId($roleId)
            ->get()
            ->toArray();
        if ($exists) {
            $existIds = array_column($exists, 'data_id');
            $addIds = array_diff($dataIds, $existIds);
            $delIds = array_diff($existIds, $dataIds);
            RoleHasData::whereRoleId($roleId)
                ->whereIn('menu_id', $delIds)
                ->delete();
        } else {
            $addIds = $dataIds;
        }
        $insertData = [];
        foreach ($addIds as $dataId) {
            $insertData[] = [
                'role_id' => $roleId,
                'data_id' => $dataId,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }
        RoleHasData::insert($insertData);
        return true;
        
    }
    
    public function switchStatus(int $roleId, int $roleStatus): bool
    {
        $exists = Role::whereId($roleId)->first();
        if (!$exists) {
            throw new CommonException(ErrorCode::RECORD_EXCEPTION);
        }
        
        if (!array_key_exists($roleStatus, Role::STATUS_MAP)) {
            throw new CommonException(ErrorCode::INVALID_ARGUMENT);
        }
        
        if ($exists->role_status !== $roleStatus) {
            $exists->role_status = $roleStatus;
            $exists->save();
            //record log
        }
        return true;
    }
    
    public function getRoleMap(array $roleIds): array
    {
        $fields = [
            'id',
            'role_name',
            'desc',
            'role_status',
            'parent_id'
        ];
        $exists = $roleIds ? Role::whereIn('id', $roleIds)->get($fields)->toArray() : Role::get($fields)->toArray();
        if (!$exists) {
            return [];
        }
        $ret = [];
        $map = array_column($exists, null, 'id');
        foreach ($exists as $item) {
            $parents = array_filter($this->getParents($item['parent_id'], $map));
            $item['level'] = count($parents) + 1;
            $item['parent'] = $parents ? implode(' / ', array_column($parents, 'role_name')) : '/';
            $upperItem = [];
            foreach ($item as $key => $value) {
                $upperItem[Str::camel($key)] = $value;
            }
            $ret[$item['id']] = $upperItem;
        }
        return $ret;
    }
    
    protected function getParents(int $parentId, array $map): array
    {
        $parentData = $map[$parentId] ?? [];
        $parents = [];
        $parents[] = $parentData ?? '';
        if (isset($parentData['parent_id']) && $parentData['parent_id'] > 0) {
            $tmpParents = $this->getParents($parentData['parent_id'], $map);
            array_unshift($parents, ...$tmpParents);
        }
        return $parents;
    }
    
    public function getRoleHasMenuMap(array $roleIds): array
    {
        $menuTb = (new Menu())->getTable();
        $roleHasMenuTb = (new RoleHasMenu())->getTable();
        $exists = DB::table("{$roleHasMenuTb} as rhm")
            ->join("{$menuTb} as m", 'm.id', '=', 'rhm.menu_id')
            ->whereIn('rhm.role_id', $roleIds)
            ->select([
                'rhm.role_id',
                'rhm.menu_id',
                'm.system_id',
                'm.parent_id',
                'm.menu_name',
                'm.title',
            ])
            ->get()
            ->toArray();
        $ret = [];
        foreach ($exists as $item) {
            $item = json_decode(json_encode($item, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            $ret[$item['role_id']][$item['system_id']][] = $item;
        }
        return $ret;
    }
    
    protected function filterMenu(array &$menuTree, array $menuIds):void {
        foreach ($menuTree as $key => $item) {
            
            if (!in_array($item['id'], $menuIds, false)) {
                unset($menuTree[$key]);
            }
            if (isset($menuTree[$key]['children']) && $menuTree[$key]['children']) {
                $this->filterMenu($menuTree[$key]['children'], $menuIds);
            }
        }
        $menuTree = array_values($menuTree);
    }
}
