<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\SaveRequest;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Http\Request;
use function App\Helpers\getParams;

class MessageController extends Controller
{
    
    public function all(Request $request)
    {
        $params = getParams($request);
        $page = $params['page'] ?? 1;
        $pageSize = $params['pageSize'] ?? 30;
        $type = $params['type'] ?? 0;
        $ret = MessageService::getInstance()->getMessages($type, $page, $pageSize);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function options(Request $request)
    {
        $ret['catMap'] = MessageService::CAT_MAP;
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function list(Request $request)
    {
        $params = getParams($request);
        $keyword = $params['keyword'] ?? '';
        $page = $params['page'] ?? 1;
        $pageSize = $params['pageSize'] ?? 30;
        $sort = $params['sort'] ?? [];
        $ret = MessageService::getInstance()->getList($keyword, $sort, $page, $pageSize);
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
    
    public function save(SaveRequest $request)
    {
        $params = getParams($request);
        $obj = new Message();
        $obj->id = $params['id'] ?? 0;
        $obj->title = $params['title'];
        $obj->cat_id = $params['catId'];
        $obj->text = $params['text'];
        $obj->publisher = $params['publisher'];
        $obj->top = $params['top'] ?? false;
        $obj->desc = $params['desc'] ?? '';
        $obj->banner = $params['banner'] ?? '';
        $obj->publish_time = $params['publishTime'] ?: date('Y-m-d H:i:s');
        $ret = MessageService::getInstance()->save($obj);
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
    
    public function switchTop(Request $request)
    {
        $params = getParams($request);
        $id = $params['id'] ?? 0;
        $top = $params['top'] ?? 0;
        $ret = MessageService::getInstance()->switchTop($id, $top);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
