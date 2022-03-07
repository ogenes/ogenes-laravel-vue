<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginRequest;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    public function login(LoginRequest $request) {
        $account = $request->input('account');
        $password = $request->input('password');
        $ret = AuthService::getInstance()->login($account, $password);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        AuthService::getInstance()->logout($token);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => [],
        ]);
    }
    
    public function info(Request $request)
    {
        $ret = UserService::getInstance()->getCurrentUser();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function list(Request $request)
    {
        $params = $this->getParams($request);
        $username = $params['username'] ?? '';
        $mobile = $params['mobile'] ?? '';
        $deptIds = $params['deptIds'] ?? [];
        $page = $params['page'] ?? 1;
        $pageSize = $params['pageSize'] ?? 100;
        $userStatus = $params['userStatus'] ?? '';
        
        $ret = UserService::getInstance()->getList(
            $username,
            $userStatus,
            $mobile,
            $deptIds,
            $page,
            $pageSize
        );
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
