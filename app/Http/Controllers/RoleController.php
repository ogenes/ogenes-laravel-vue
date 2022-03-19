<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\Request;
use function App\Helpers\getParams;

class RoleController extends Controller
{
    public function options(Request $request)
    {
        $params = getParams($request);
        $ret = RoleService::getInstance()->getOptions();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function list(Request $request)
    {
        $params = getParams($request);
        $ret = RoleService::getInstance()->getList();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function save(Request $request)
    {
        $params = getParams($request);
        $ret = RoleService::getInstance()->save();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function saveRoleHasData(Request $request)
    {
        $params = getParams($request);
        $ret = RoleService::getInstance()->saveRoleHasData();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function saveRoleHasMenu(Request $request)
    {
        $params = getParams($request);
        $ret = RoleService::getInstance()->saveRoleHasMenu();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function switchStatus(Request $request)
    {
        $params = getParams($request);
        $ret = RoleService::getInstance()->switchStatus();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
