<?php

namespace App\Services\Permission;


use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use App\Models\Menu;
use App\Services\ActionLogService;
use App\Services\BaseService;
use Illuminate\Support\Str;
use function App\Helpers\formatDateTime;

/**
 * Class MenuService
 * @package App\Services
 */
class MenuService extends BaseService
{
    public const
        MENU_TYPE_DIR = 1,
        MENU_TYPE_PAGE = 2,
        MENU_TYPE_BTN = 3;
    
    public const MENU_TYPE_OPTION = [
        self::MENU_TYPE_DIR => '目录',
        self::MENU_TYPE_PAGE => '菜单',
        self::MENU_TYPE_BTN => '按钮',
    ];
    
    public function getList(int $systemId, array $types = []): array
    {
        $exists = Menu::whereParentId(0)
            ->where('system_id', '=', $systemId)
            ->get()
            ->toArray();
        if (!$exists) {
            return [];
        }
        $ret = [];
        foreach ($exists as $item) {
            $item['parent'] = '';
            $item['parents'] = [];
            $item['role_arr'] = explode(PHP_EOL, $item['roles']);
            $tmp = $this->getChildrenMenu($item, $types);
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
    
    protected function getChildrenMenu(array $menu, array $types = []): array
    {
        $data = Menu::whereParentId($menu['id'])
            ->when($types, function ($query) use ($types) {
                return $query->whereIn('type', $types);
            })
            ->get()
            ->toArray();
        if (!$data) {
            return [];
        }
        $ret = [];
        foreach ($data as $item) {
            $item['parents'] = $menu['parents'];
            $item['parents'][] = $menu['title'];
            $item['level'] = count($item['parents']) + 1;
            $item['created_at'] = formatDateTime($item['created_at']);
            $item['updated_at'] = formatDateTime($item['updated_at']);
            $item['role_arr'] = explode(PHP_EOL, $item['roles']);
            $tmp = $this->getChildrenMenu($item, $types);
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
    
    public function save(
        int $id,
        int $systemId,
        string $menuName,
        int $type,
        int $parentId,
        string $title,
        string $icon,
        string $roles
    ): bool
    {
        $exists = Menu::whereMenuName($menuName)
            ->where('system_id', '=', $systemId)
            ->where('id', '!=', $id)
            ->first();
        if ($exists) {
            throw new CommonException(ErrorCode::RECORD_EXISTS);
        }
        $ret = false;
        if ($id > 0) {
            $exist = Menu::whereId($id)->first();
            $data = [];
            $systemId !== $exist->system_id && $data['system_id'] = $systemId;
            $menuName !== $exist->menu_name && $data['menu_name'] = $menuName;
            $type !== $exist->type && $data['type'] = $type;
            $parentId !== $exist->parent_id && $data['parent_id'] = $parentId;
            $title !== $exist->title && $data['title'] = $title;
            $icon !== $exist->icon && $data['icon'] = $icon;
            $roles !== $exist->roles && $data['roles'] = $roles;
    
            if ($data) {
                $data['updated_at'] = date('Y-m-d H:i:s');
                foreach ($data as $key => $val) {
                    $exist->setAttribute($key, $val);
                }
                $ret = $exist->save();
                ActionLogService::getInstance()->insert(
                    ActionLogService::RESOURCE_MENU,
                    $id,
                    $this->uid,
                    '编辑',
                    $data
                );
            }
        } else {
            $data = [
                'system_id' => $systemId,
                'menu_name' => $menuName,
                'type' => $type,
                'parent_id' => $parentId,
                'title' => $title,
                'icon' => $icon,
                'roles' => $roles,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $id = Menu::insertGetId($data);
            $ret = $id > 0;
            ActionLogService::getInstance()->insert(
                ActionLogService::RESOURCE_MENU,
                $id,
                $this->uid,
                '新增',
                $data
            );
        }

        return $ret;
    }
    
    public function remove(int $id): bool
    {
        $ret = false;
        if (Menu::whereParentId($id)->exists()) {
            throw new CommonException(ErrorCode::UNKNOW);
        }
        $exist = Menu::whereId($id)->first();
        if ($exist) {
            ActionLogService::getInstance()->insert(
                ActionLogService::RESOURCE_MENU,
                $id,
                $this->uid,
                '删除',
                $exist->toArray(),
            );
            $ret = $exist->delete();
        }
        return $ret;
    }
    
    public function getMenuMap(int $systemId): array
    {
        $exists = Menu::where('system_id', '=', $systemId)
            ->whereIn('type', [1, 2])
            ->get([
                'id',
                'menu_name',
                'type',
                'title',
                'icon',
                'roles',
                'parent_id'
            ])
            ->toArray();
        if (!$exists) {
            return [];
        }
        $ret = [];
        $map = array_column($exists, null, 'id');
        foreach ($exists as $item) {
            $item['roles'] = explode(PHP_EOL, $item['roles']);
            $parents = array_filter($this->getParents($item['parent_id'], $map));
            $item['level'] = count($parents) + 1;
            $parentName = $parents ? implode(' / ', array_column($parents, 'title')) : '';
            $item['fullName'] = ($parentName ? $parentName . ' / ' : '') . $item['title'];
            $upperItem = [];
            foreach ($item as $key => $value) {
                $upperItem[Str::camel($key)] = $value;
            }
            $ret[$item['menu_name']] = $upperItem;
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
