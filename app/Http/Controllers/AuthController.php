<?php

namespace App\Http\Controllers;

use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use App\Http\Requests\User\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
    
    public function getActionList(Request $request)
    {
        $params = getParams($request);
        $page = $params['page'] ?? 1;
        $pageSize = $params['pageSize'] ?? 20;
        $ret = AuthService::getInstance()->actionList($page, $pageSize);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function hasInfo(Request $request)
    {
        $ret['deptMap'] = AuthService::getInstance()->getDepartmentMap();
        $ret['roleMap'] = AuthService::getInstance()->getRoleMap();
        $ret['menuTree'] = AuthService::getInstance()->getMenuTree();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function updateAccount(Request $request)
    {
        $params = getParams($request);
        $validator = Validator::make($params, [
            'account' => 'required|min:6|max:20',
        ]);
        
        if ($validator->fails()) {
            throw new CommonException(ErrorCode::INVALID_ARGUMENT, $validator->errors()->first());
        }
        
        $account = $params['account'];
        $ret = AuthService::getInstance()->updateAccount($account);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function updateUsername(Request $request)
    {
        $params = getParams($request);
        $validator = Validator::make($params, [
            'name' => 'required',
        ]);
        
        if ($validator->fails()) {
            throw new CommonException(ErrorCode::INVALID_ARGUMENT, $validator->errors()->first());
        }
        $username = $params['name'] ?? '';
        $ret = AuthService::getInstance()->updateUsername($username);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function updateMobile(Request $request)
    {
        $params = getParams($request);
        $validator = Validator::make($params, [
            'mobile' => 'required|size:11',
        ]);
        
        if ($validator->fails()) {
            throw new CommonException(ErrorCode::INVALID_ARGUMENT, $validator->errors()->first());
        }
        $mobile = $params['mobile'] ?? '';
        $ret = AuthService::getInstance()->updateMobile($mobile);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function updateEmail(Request $request)
    {
        $params = getParams($request);
        $validator = Validator::make($params, [
            'email' => 'required|email',
        ]);
        
        if ($validator->fails()) {
            throw new CommonException(ErrorCode::INVALID_ARGUMENT, $validator->errors()->first());
        }
        $email = $params['email'] ?? '';
        $ret = AuthService::getInstance()->updateEmail($email);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function updateAvatar(Request $request)
    {
        $file = $request->file('file');
        $ret = AuthService::getInstance()->updateAvatar($file);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function updatePass(Request $request)
    {
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
