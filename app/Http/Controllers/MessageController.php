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
        $page = $params['page'] ?? 1;
        $pageSize = $params['pageSize'] ?? 30;
        $ret = MessageService::getInstance()->getList($keyword, $page, $pageSize);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function detail(Request $request)
    {
        $params = getParams($request);
        $id = $params['id'] ?? 0;
        $ret = MessageService::getInstance()->getDetail($id);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function save(Request $request)
    {
        $params = getParams($request);
        $id = $params['id'] ?? 0;
        $title = $params['title'] ?? '';
        $desc = $params['desc'] ?? '';
        $banner = $params['banner'] ?? '';
        $text = $params['text'] ?? '';
        $releaseAt = $params['releaseAt'] ?? '';
        $ret = MessageService::getInstance()->save($id, $title, $banner, $desc, $text, $releaseAt);
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
