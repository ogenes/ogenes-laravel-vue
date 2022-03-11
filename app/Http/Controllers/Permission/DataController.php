<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\DataSaveRequest;
use App\Services\Permission\DataService;
use Illuminate\Http\Request;
use function App\Helpers\getParams;

class DataController extends Controller
{
    public function list(Request $request)
    {
        $params = getParams($request);
        $systemId = $params['systemId'] ?? 0;
        $menuId = $params['menuId'] ?? 0;
        $resource = $params['resource'] ?? '';
        $dataMark = $params['dataMark'] ?? '';
        $dataName = $params['dataName'] ?? '';
        $ret = DataService::getInstance()->getList(
            $systemId,
            $menuId,
            $resource,
            $dataMark,
            $dataName
        );
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    
    public function save(DataSaveRequest $request)
    {
        $params = getParams($request);
        $id = $params['id'] ?? 0;
        $systemId = $params['systemId'] ?? 0;
        $menuId = $params['menuId'] ?? 0;
        $resource = $params['resource'] ?? '';
        $dataMark = $params['dataMark'] ?? '';
        $dataName = $params['dataName'] ?? '';
        $conditions = $params['conditions'] ?? '';
        $fields = $params['fields'] ?? '';
        $ret = DataService::getInstance()->save(
            $id,
            $systemId,
            $menuId,
            $resource,
            $dataMark,
            $dataName,
            $conditions,
            $fields
        );
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
}
