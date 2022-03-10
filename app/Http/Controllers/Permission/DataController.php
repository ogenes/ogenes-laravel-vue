<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use App\Services\Permission\MenuService;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function options(Request $request)
    {
        $ret['system'] = MenuService::SYSTEM;
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
