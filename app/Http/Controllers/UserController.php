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
    
    public function logout(Request $request) {
        $token = $request->header('Authorization');
        AuthService::getInstance()->logout($token);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => [],
        ]);
    }
    
    public function info(Request $request) {
        $ret = UserService::getInstance()->getCurrentUser();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
