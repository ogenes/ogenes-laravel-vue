<?php

namespace App\Http\Controllers;

use App\Services\MessageService;
use Illuminate\Http\Request;
use function App\Helpers\getParams;

class MessageController extends Controller
{
    public function list(Request $request)
    {
        $params = getParams($request);
        $keyword = $params['keyword'] ?? '';
        $ret = MessageService::getInstance()->getList($keyword);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function add(Request $request)
    {
        $params = getParams($request);
        $title = $params['title'] ?? '';
        $desc = $params['desc'] ?? '';
        $banner = $params['banner'] ?? '';
        $text = $params['text'] ?? '';
        $releaseAt = $params['releaseAt'] ?? '';
        $ret = MessageService::getInstance()->add($title, $banner, $desc, $text, $releaseAt);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function edit(Request $request)
    {
        $params = getParams($request);
        $id = $params['id'] ?? 0;
        $title = $params['title'] ?? '';
        $desc = $params['desc'] ?? '';
        $banner = $params['banner'] ?? '';
        $text = $params['text'] ?? '';
        $releaseAt = $params['releaseAt'] ?? '';
        $ret = MessageService::getInstance()->edit($id, $title, $banner, $desc, $text, $releaseAt);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function switchHidden(Request $request)
    {
        $params = getParams($request);
        $id = $params['id'] ?? 0;
        $hidden = $params['hidden'] ?? 0;
        $ret = MessageService::getInstance()->switchHidden($id, $hidden);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
