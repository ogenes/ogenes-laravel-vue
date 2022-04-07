<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use function App\Helpers\getParams;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
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
        $ret = AuthService::getInstance()->getCurrentUser();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function roleTree(Request $request) {
        $ret = AuthService::getInstance()->getRoleTree();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function menuTree(Request $request) {
        $ret = AuthService::getInstance()->getMenuTree();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function updateBasicInfo(Request $request) {
        $params = getParams($request);
        $username = $params['username'] ?? '';
        $mobile = $params['mobile'] ?? '';
        $email = $params['email'] ?? '';
        $ret = AuthService::getInstance()->updateBasicInfo($username, $mobile, $email);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function updateAvatar(Request $request) {
        $params = getParams($request);
        $avatar = $params['avatar'] ?? '';
        $ret = AuthService::getInstance()->updateAvatar($avatar);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function updatePass(Request $request) {
        $params = getParams($request);
        $password = $params['newPassword'] ?? '';
        $oldPassword = $params['oldPassword'] ?? '';
        $ret = AuthService::getInstance()->updatePass($password, $oldPassword);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
        
    }
}
