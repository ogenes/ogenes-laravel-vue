<?php

namespace App\Http\Controllers;

use App\Services\Permission\MenuService;
use App\Services\RoleService;
use Illuminate\Http\Request;
use function App\Helpers\getParams;

class RoleController extends Controller
{
    public function options(Request $request)
    {
        $ret['system'] = MenuService::SYSTEM;
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function menuTree(Request $request)
    {
        $params = getParams($request);
        $systemId = $params['systemId'] ?? 1;
        $ret['menuTree'] = MenuService::getInstance()->getList($systemId);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function dataTree(Request $request)
    {
        $params = getParams($request);
        $systemId = $params['systemId'] ?? 1;
        $ret['dataTree'] = [];
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function roleTree(Request $request)
    {
        $ret = RoleService::getInstance()->getRoleTree();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function list(Request $request)
    {
        $params = getParams($request);
        $name = $params['roleName'] ?: '';
        $roleStatus = $params['roleStatus'] ?? '';
        $parentIds = $params['parentIds'] ?? [];
        $page = $params['page'] ?? 1;
        $pageSize = $params['pageSize'] ?? 30;
        $ret = RoleService::getInstance()->getList(
            $name,
            $roleStatus,
            $parentIds,
            $page,
            $pageSize
        );
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function save(Request $request)
    {
        $params = getParams($request);
        $name = $params['roleName'] ?? '';
        $desc = $params['desc'] ?? '';
        $parentId = $params['parentId'] ?? 0;
        $id = $params['id'] ?? 0;
        $ret = RoleService::getInstance()->save($name, $desc, $parentId, $id);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function saveRoleHasData(Request $request)
    {
        $params = getParams($request);
        $roleId = $params['roleId'] ?? 0;
        $dataIds = $params['dataIds'] ?? [];
        $ret = RoleService::getInstance()->saveRoleHasData($roleId, $dataIds);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function saveRoleHasMenu(Request $request)
    {
        $params = getParams($request);
        $roleId = $params['roleId'] ?? 0;
        $menuIds = $params['menuIds'] ?? [];
        $ret = RoleService::getInstance()->saveRoleHasMenu($roleId, $menuIds);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function switchStatus(Request $request)
    {
        $params = getParams($request);
        $roleId = $params['roleId'] ?? 0;
        $roleStatus = $params['roleStatus'] ?? 0;
        $ret = RoleService::getInstance()->switchStatus($roleId, $roleStatus);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
