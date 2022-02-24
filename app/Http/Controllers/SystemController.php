<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/2/7
 */

namespace App\Http\Controllers;


namespace App\Http\Controllers;


use App\Services\LanguageService;
use App\Services\SystemService;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function init(Request $request) {
        $ret = SystemService::getInstance()->init();
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function setLang(Request $request) {
        $lang = $request->input('lang', '') ?? '';
        $ret = LanguageService::getInstance()->setLang($lang);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }

}
