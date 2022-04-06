<?php

namespace App\Http\Controllers;

use App\Services\DictService;
use Illuminate\Http\Request;
use function App\Helpers\getParams;

class DictController extends Controller
{
    public function list(Request $request)
    {
        $params = getParams($request);
        $dictName = $params['dictName'] ?: '';
        $symbol = $params['symbol'] ?? '';
        $remark = $params['remark'] ?? '';
        $createdAt = $params['createdAt'] ?? [];
        $page = $params['page'] ?? 1;
        $pageSize = $params['pageSize'] ?? 30;
        $sort = $params['sort'] ?? [];
        $ret = DictService::getInstance()->getList(
            $dictName,
            $symbol,
            $remark,
            $createdAt,
            $sort,
            $page,
            $pageSize
        );
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function save(Request $request)
    {
        $params = getParams($request);
        $dictName = $params['dictName'] ?: '';
        $symbol = $params['symbol'] ?? '';
        $remark = $params['remark'] ?? '';
        $id = $params['id'] ?? 0;
        $ret = DictService::getInstance()->save($dictName, $symbol, $remark, $id);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function dataList(Request $request)
    {
        $params = getParams($request);
        $symbol = $params['symbol'] ?? '';
        $ret = DictService::getInstance()->getDictDataBySymbol($symbol);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]); 
    }
    
    public function saveData(Request $request)
    {
        $params = getParams($request);
        $dictId = $params['dictId'] ?: 0;
        $sort = $params['sort'] ?? '';
        $label = $params['label'] ?? '';
        $value = $params['value'] ?? '';
        $remark = $params['remark'] ?? '';
        $id = $params['id'] ?? 0;
        $ret = DictService::getInstance()->saveData($dictId, $sort, $label, $value, $remark, $id);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function remove(Request $request)
    {
        $params = getParams($request);
        $id = $params['id'] ?? 0;
        $ret = DictService::getInstance()->remove($id);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function removeData(Request $request)
    {
        $params = getParams($request);
        $dictId = $params['dictId'] ?? 0;
        $id = $params['id'] ?? -1;
        $ret = DictService::getInstance()->removeData($dictId, $id);
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
