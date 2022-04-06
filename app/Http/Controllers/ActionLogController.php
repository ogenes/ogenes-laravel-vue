<?php

namespace App\Http\Controllers;

use App\Services\ActionLogService;
use App\Services\UserService;
use Illuminate\Http\Request;
use function App\Helpers\getParams;

class ActionLogController extends Controller
{
    public function options(Request $request)
    {
        $ret['users'] = UserService::getInstance()->getAllUsers();
        $ret['resourceMap'] = ActionLogService::RESOURCE_MAP;
        return response()->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $ret,
        ]);
    }
    public function list(Request $request)
    {
        $params = getParams($request);
        $resourceArr = $params['resourceArr'] ?: [];
        $uidArr = $params['uidArr'] ?: [];
        $type = $params['type'] ?? '';
        $remark = $params['remark'] ?? '';
        $createdAt = $params['createdAt'] ?? [];
        $page = $params['page'] ?? 1;
        $pageSize = $params['pageSize'] ?? 30;
        $sort = $params['sort'] ?? [];
        $ret = ActionLogService::getInstance()->getList(
            $resourceArr,
            $uidArr,
            $type,
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
}
