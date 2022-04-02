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
use App\Services\BaseService;

class ActionLogService extends BaseService
{
    public const
        RESOURCE_DEPARTMENT = 'department',
        RESOURCE_USER = 'user',
        RESOURCE_MENU = 'menu',
        RESOURCE_ROLE = 'role',
        RESOURCE_DICT = 'dict';
    
    public const RESOURCE_MAP = [
        self::RESOURCE_DEPARTMENT => '部门',
        self::RESOURCE_USER => '用户',
        self::RESOURCE_MENU => '菜单',
        self::RESOURCE_ROLE => '角色',
        self::RESOURCE_DICT => '字典',
    ];
    
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
