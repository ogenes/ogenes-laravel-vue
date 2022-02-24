<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AuthLogin;
use App\Http\Requests\User\AuthRegister;
use App\Services\User\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * 用户注册
     *
     * @param AuthRegister $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CommonException
     */
    public function register(AuthRegister $request)
    {
        $username = $request->input('username');
        $email = $request->input('email');
        $password = $request->input('password');
        AuthService::getInstance()->register($username, $email, $password);
        return response()->json([
            'code' => 0,
            'msg' => 'success'
        ]);
    }
    
    /**
     * 激活邮箱
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeUser(Request $request)
    {
        $code = $request->input('code') ?? '';
        $ret = AuthService::getInstance()->activeUser($code);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    /**
     * 登录接口
     *
     * @param AuthLogin $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CommonException
     */
    public function login(AuthLogin $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $ret = AuthService::getInstance()->login($email, $password);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    /**
     * 后端只是加密和解密，不需要做退出逻辑
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => [],
        ]);
    }
}
