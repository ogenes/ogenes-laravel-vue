<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/2/8
 */

namespace App\Services;


use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use App\Models\ActionLog;
use App\Models\Dict;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function App\Helpers\formatDateTime;

class ActionLogService extends BaseService
{
    public const
        RESOURCE_DEPARTMENT = 'department',
        RESOURCE_USER = 'user',
        RESOURCE_MENU = 'menu',
        RESOURCE_ROLE = 'role',
        RESOURCE_DICT = 'dict',
        RESOURCE_SETTING = 'setting',
        RESOURCE_MSG = 'message';
    
    public const RESOURCE_MAP = [
        self::RESOURCE_DEPARTMENT => '部门',
        self::RESOURCE_USER => '用户',
        self::RESOURCE_MENU => '菜单',
        self::RESOURCE_ROLE => '角色',
        self::RESOURCE_DICT => '字典',
        self::RESOURCE_SETTING => '系统设置',
        self::RESOURCE_MSG => '消息通知',
    ];
    
    public function getList(
        array $resourceArr,
        array $uidArr,
        string $type = '',
        string $remark = '',
        array $createdAt = [],
        array $sort = [],
        int $page = 1,
        int $pageSize = 30
    ): array
    {
        $ret = [
            'cnt' => 0,
            'list' => [],
            'page' => $page,
            'pageSize' => $pageSize,
        ];
        $userTb = (new User())->getTable();
        $actionTb = (new ActionLog())->getTable();
        $query = DB::table("{$actionTb} as a")
            ->leftJoin("{$userTb} as u", 'u.uid', '=', 'a.uid')
            ->select([
                'a.*',
                'u.username'
            ]);
        $resourceArr && $query->whereIn('a.resource', $resourceArr);
        $uidArr && $query->whereIn('a.uid', $uidArr);
        $type && $query->where('a.type', 'like', "%{$type}%");
        $remark && $query->where('a.remark', 'like', "%{$remark}%");
        if ($createdAt) {
            $start = date('Y-m-d 00:00:00', strtotime($createdAt[0]));
            $end = date('Y-m-d 23:59:59', strtotime($createdAt[1]));
            $query->whereBetween('a.created_at', [$start, $end]);
        }
        $prop = 'id';
        $order = 'desc';
        if (isset($sort['prop'])) {
            $prop = Str::snake($sort['prop']);
        }
        if (isset($sort['order']) && $sort['order'] === 'ascending') {
            $order = 'asc';
        }
        $resp = $query->orderBy($prop, $order)
            ->paginate($pageSize, ['*'], 'page', $page)
            ->toArray();
        if (empty($resp)) {
            return $ret;
        }
        
        $ret['cnt'] = $resp['total'];
        $ret['page'] = $resp['current_page'];
        $ret['pageSize'] = $resp['per_page'];
        foreach ($resp['data'] as $item) {
            $item = json_decode(json_encode($item, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            $item['created_at'] = formatDateTime($item['created_at']);
            $item['resourceName'] = self::RESOURCE_MAP[$item['resource']] ?? '';
            $tmp = [];
            foreach ($item as $key => $value) {
                $tmp[Str::camel($key)] = $value;
            }
            $ret['list'][] = $tmp;
        }
        return $ret;
    }
    
    public function insert(
        string $resource,
        int $resourceId,
        int $uid,
        string $type,
        array $remark
    ): bool
    {
        return ActionLog::insertGetId([
            'resource' => $resource,
            'resource_id' => $resourceId,
            'uid' => $uid,
            'type' => $type,
            'remark' => json_encode($remark, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
    
}
