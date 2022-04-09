<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\SaveRequest;
use App\Http\Requests\User\LoginRequest;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\Request;
use function App\Helpers\getParams;

class UserController extends Controller
{
    public function list(Request $request)
    {
        $params = getParams($request);
        $username = $params['username'] ?? '';
        $mobile = $params['mobile'] ?? '';
        $account = $params['account'] ?? '';
        $deptIds = $params['deptIds'] ?? [];
        $page = $params['page'] ?? 1;
        $pageSize = $params['pageSize'] ?? 100;
        $userStatus = $params['userStatus'] ?? '';
        $sort = $params['sort'] ?? [];
        
        $ret = UserService::getInstance()->getList(
            $username,
            $account,
            $userStatus,
            $mobile,
            $deptIds,
            $sort,
            $page,
            $pageSize
        );
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function save(SaveRequest $request)
    {
        $params = getParams($request);
        $uid = $params['id'] ?? 0;
        $avatar = $params['avatar'] ?? '';
        $account = $params['account'] ?? '';
        $username = $params['username'] ?? '';
        $mobile = $params['mobile'] ?? '';
        $email = $params['email'] ?? '';
        $deptIds = $params['deptIds'] ?? [];
        
        $ret = UserService::getInstance()->save(
            $uid,
            $avatar,
            $account,
            $username,
            $mobile,
            $email,
            $deptIds
        );
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function switchStatus(Request $request)
    {
        $params = getParams($request);
        $uid = $params['id'] ?? '';
        $userStatus = $params['userStatus'] ?? '';
        $ret = UserService::getInstance()->switchStatus(
            $uid,
            $userStatus
        );
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function resetPassByUid(Request $request)
    {
        $params = getParams($request);
        $uid = $params['id'] ?? '';
        $ret = UserService::getInstance()->resetPassByUid($uid);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function saveUserHasRole(Request $request)
    {
        $params = getParams($request);
        $uid = $params['uid'] ?? 0;
        $roleIds = $params['roleIds'] ?? [];
        $ret = UserService::getInstance()->saveUserHasRole($uid, $roleIds);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
