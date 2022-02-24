<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2021/12/29
 */

namespace App\Http\Controllers\User;


use App\Helpers\SmsHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\BindMobile;
use App\Http\Requests\User\SendBindCode;
use App\Services\User\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * 当前登录用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentUser(Request $request)
    {
        $ret = UserService::getInstance()->getCurrentUser();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }

    /**
     * 发送手机验证码
     *
     * @param SendBindCode $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendBindCode(SendBindCode $request)
    {
        $mobile = $request->input('mobile');
        $ret = SmsHelper::getInstance()->sendCode($mobile);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }

    /**
     * 绑定手机号
     *
     * @param BindMobile $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CommonException
     */
    public function bindMobile(BindMobile $request)
    {
        $mobile = $request->input('mobile');
        $code = $request->input('code');
        $ret = UserService::getInstance()->bindMobile($mobile, $code);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }

    /**
     * 手机号解绑
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unbindMobile(Request $request)
    {
        $ret = UserService::getInstance()->unbindMobile();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
