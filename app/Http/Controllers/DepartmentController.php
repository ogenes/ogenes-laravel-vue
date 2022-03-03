<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/3/2
 */

namespace App\Http\Controllers;


use App\Http\Requests\Department\AddRequest;
use App\Services\DepartmentService;
use Illuminate\Http\Request;

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
    
    public function save(AddRequest $request) {
        $id = $request->input('id');
        $name = $request->input('name');
        $parentId = $request->input('parentId');
        $ret = DepartmentService::getInstance()->save($name, $parentId, $id);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function remove(Request $request) {
        $id = $request->input('id');
        $ret = DepartmentService::getInstance()->remove($id);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
