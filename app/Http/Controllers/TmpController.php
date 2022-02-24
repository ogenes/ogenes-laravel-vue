<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/2/24
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class TmpController extends Controller
{
    public function userInfo(Request $request)
    {
        //{"code":20000,"data":{"roles":["admin"],"introduction":"I am a super administrator","avatar":"https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif","name":"Super Admin"}}
        return response()->json([
            'code' => 20000,
            'data' => [
                'roles' => ["admin"],
                "introduction" => 'I am a super administrator',
                'avatar' => 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif',
                'name' => 'Ogenes',
            ],
        ]);
    }
    public function login(Request $request)
    {
        //{"code":20000,"data":{"roles":["admin"],"introduction":"I am a super administrator","avatar":"https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif","name":"Super Admin"}}
        return response()->json([
            'code' => 20000,
            'data' => [
                'token' => "admin-token",
            ],
        ]);
    }
    
    public function logout(Request $request)
    {
        //{"code":20000,"data":{"roles":["admin"],"introduction":"I am a super administrator","avatar":"https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif","name":"Super Admin"}}
        return response()->json([
            'code' => 20000,
            'data' => "success",
        ]);
    }
}
