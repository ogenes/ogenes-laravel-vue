<?php

namespace App\Http\Controllers;

use App\Services\SettingService;
use Illuminate\Http\Request;
use function App\Helpers\getParams;

class SettingController extends Controller
{
    public function getAll(Request $request)
    {
        $ret = SettingService::getInstance()->getAll();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function getOne(Request $request)
    {
        $params = getParams($request);
        $label = $params['label'] ?? '';
        $ret = SettingService::getInstance()->getOne($label);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function save(Request $request)
    {
        $params = getParams($request);
        $label = $params['label'] ?? '';
        $value = $params['value'] ?? '';
        $ret = SettingService::getInstance()->save($label, $value);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
