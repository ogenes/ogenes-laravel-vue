<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/3/2
 */

namespace App\Http\Controllers;


use App\Http\Requests\Department\SaveRequest;
use App\Services\DepartmentService;
use Illuminate\Http\Request;
use function App\Helpers\getParams;

class DepartmentController extends Controller
{
    public function list(Request $request) {
        $ret = DepartmentService::getInstance()->getList();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    public function user(Request $request) {
        $params = getParams($request);
        $deptId = $params['id'] ?? 0;
        $ret = DepartmentService::getInstance()->getDepartmentHasUser($deptId);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function save(SaveRequest $request) {
        $params = getParams($request);
        $id = $params['id'] ?? 0;
        $name = $params['name'] ?? '';
        $parentId = $params['parentId'] ?? 0;
        $ret = DepartmentService::getInstance()->save($name, $parentId, $id);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function remove(Request $request) {
        $params = getParams($request);
        $id = $params['id'] ?? 0;
        $ret = DepartmentService::getInstance()->remove($id);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
