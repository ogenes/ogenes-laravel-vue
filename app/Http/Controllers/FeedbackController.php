<?php

namespace App\Http\Controllers;

use App\Services\FeedbackService;
use Illuminate\Http\Request;
use function App\Helpers\getParams;

class FeedbackController extends Controller
{
    public function options()
    {
        $ret['typeMap'] = FeedbackService::TYPE_MAP;
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function add(Request $request)
    {
        $params = getParams($request);
        $content = $params['content'] ?: '';
        $type = $params['type'] ?? 1;
        $ret = FeedbackService::getInstance()->save($content, $type);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
