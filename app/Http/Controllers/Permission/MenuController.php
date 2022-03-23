<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\SaveRequest;
use App\Services\Permission\MenuService;
use Illuminate\Http\Request;
use function App\Helpers\getParams;

class MenuController extends Controller
{
    public function options(Request $request)
    {
        $ret['system'] = MenuService::SYSTEM;
        $ret['menuTypeOption'] = MenuService::MENU_TYPE_OPTION;
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    
    public function list(Request $request)
    {
        $params = getParams($request);
        $systemId = $params['systemId'] ?? 1;
        $ret = MenuService::getInstance()->getList($systemId);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function save(SaveRequest $request)
    {
        $params = getParams($request);
        $id = $params['id'] ?? 0;
        $systemId = $params['systemId'] ?? 0;
        $menuName = $params['menuName'] ?? '';
        $type = $params['type'] ?? 1;
        $parentId = $params['parentId'] ?? 0;
        $title = $params['title'] ?? '';
        $icon = $params['icon'] ?? '';
        $roles = $params['roles'] ?? '';
        $ret = MenuService::getInstance()->save(
            $id,
            $systemId,
            $menuName,
            $type,
            $parentId,
            $title,
            $icon,
            $roles
        );
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function remove(Request $request)
    {
        $params = getParams($request);
        $id = $params['id'] ?? 0;
        $ret = MenuService::getInstance()->remove($id);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function menuMap(Request $request)
    {
        $params = getParams($request);
        $systemId = $params['systemId'] ?? 1;
        $ret = MenuService::getInstance()->getMenuMap($systemId);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
