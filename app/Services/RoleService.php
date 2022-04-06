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
            ]);
        $name && $query->where('r.role_name', 'like', "%{$name}%");
        $roleStatus !== '' && $query->where('r.role_status', '=', $roleStatus);
        $parentIds && $query->whereIn('r.parent_id', $parentIds);
        $menuIds && $query->whereIn('rhm.menu_id', $menuIds);
        
        $prop = 'id';
        $order = 'asc';
        if (isset($sort['prop'])) {
            $prop = Str::snake($sort['prop']);
        }
        if (isset($sort['order']) && $sort['order'] === 'descending') {
            $order = 'desc';
        }
        $query->groupBy(['r.id'])->orderBy($prop, $order);
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
        $systems = DictService::getInstance()->getSystemMap();
        foreach ($systems as $systemId => $val) {
            $menuTreeMap[$systemId] = MenuService::getInstance()->getList($systemId);
        }
        foreach ($resp['data'] as $item) {
            $item = json_decode(json_encode($item, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            $item['created_at'] = formatDateTime($item['created_at']);
            $item['updated_at'] = formatDateTime($item['updated_at']);
            $item['parent'] = $roleMap[$item['id']]['parent'] ?: '/';
            $menuMap = $roleHasMenuMap[$item['id']] ?? [];
            foreach ($systems as $systemId => $systemName) {
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
                $data = [];
                $parentId !== $exist->parent_id && $data['parent_id'] = $parentId;
                $name !== $exist->role_name && $data['role_name'] = $name;
                $desc !== $exist->desc && $data['desc'] = $desc;
                if ($data) {
                    $data['updated_at'] = date('Y-m-d H:i:s');
                    foreach ($data as $key => $val) {
                        $exist->setAttribute($key, $val);
                    }
                    $ret = $exist->save();
                    ActionLogService::getInstance()->insert(
                        ActionLogService::RESOURCE_ROLE,
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
                'role_name' => $name,
                'desc' => $desc,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $id = Role::insertGetId($data);
            $ret = $id > 0;
            ActionLogService::getInstance()->insert(
                ActionLogService::RESOURCE_ROLE,
                $id,
                $this->uid,
                '新增',
                $data
            );
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
            $delIds = [];
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
        $insertData && RoleHasMenu::insert($insertData);
        if ($delIds || $addIds) {
            $data['delIds'] = $delIds;
            $data['addIds'] = $addIds;
            ActionLogService::getInstance()->insert(
                ActionLogService::RESOURCE_ROLE,
                $roleId,
                $this->uid,
                '编辑菜单权限',
                $data
            );
        }
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
            $data['role_status'] = $roleStatus;
            $data['updated_at'] = date('Y-m-d H:i:s');
            foreach ($data as $key => $val) {
                $exists->setAttribute($key, $val);
            }
            $exists->save();
            ActionLogService::getInstance()->insert(
                ActionLogService::RESOURCE_ROLE,
                $roleId,
                $this->uid,
                '切换状态',
                $data
            );
        }
        return true;
    }
    
    public function getRoleMap(array $roleIds = []): array
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
            $item['parent'] = $parents ? implode(' / ', array_column($parents, 'role_name')) : '';
            $item['fullName'] = ($item['parent'] ? $item['parent'] . ' / ' : '') . $item['role_name'];
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
    
    protected function filterMenu(array &$menuTree, array $menuIds): void
    {
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
    
    public function getUserHasMenus(int $uid, int $systemId): array
    {
        $menuTb = (new Menu())->getTable();
        $userHasRoleTb = (new UserHasRole())->getTable();
        $roleHasMenuTb = (new RoleHasMenu())->getTable();
        $data = DB::table("{$userHasRoleTb} as uhr")
            ->join("{$roleHasMenuTb} as rhm", 'rhm.role_id', '=', 'uhr.role_id')
            ->join("{$menuTb} as m", 'm.id', '=', 'rhm.menu_id')
            ->where('m.system_id', '=', $systemId)
            ->where('uhr.uid', '=', $uid)
            ->get([
                DB::raw('DISTINCT m.id as menuId'),
                'm.menu_name as menuName',
                'm.title as menuTitle',
                'm.type as menuType'
            ])
            ->toArray();
        return $data ? json_decode(json_encode($data, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR) : [];
        
    }
}
