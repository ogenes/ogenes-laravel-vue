<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/2/8
 */

namespace App\Services\Permission;


use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use App\Models\DataPermission;
use App\Models\User;
use App\Models\UserHasDepartment;
use App\Services\BaseService;
use App\Services\DepartmentService;
use Illuminate\Support\Str;
use function App\Helpers\formatDateTime;

class DataService extends BaseService
{
    public function getList(
        int $systemId,
        int $menuId,
        string $resource,
        string $dataMark,
        string $dataName,
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
        
        $query = DataPermission::whereSystemId($systemId);
        $menuId && $query->where('menu_id', '=', $menuId);
        $resource && $query->where('resource', 'like', "%{$resource}%");
        $dataMark && $query->where('date_mark', 'like', "%{$dataMark}%");
        $dataName && $query->where('data_name', 'like', "%{$dataName}%");
        $resp = $query->paginate($pageSize, ['*'], 'page', $page)->toArray();
        if (empty($resp)) {
            return $ret;
        }
        $ret['cnt'] = $resp['total'];
        $ret['page'] = $resp['current_page'];
        $ret['pageSize'] = $resp['per_page'];
        
        $menuMap = MenuService::getInstance()->getMenuMap($systemId);
        $menuMap = array_column($menuMap, null, 'id');
        foreach ($resp['data'] as $item) {
            $item['created_at'] = formatDateTime($item['created_at']);
            $item['updated_at'] = formatDateTime($item['updated_at']);
            $item['menu_name'] = $menuMap[$item['menu_id']]['fullName'] ?? '';
            $tmp = [];
            foreach ($item as $key => $value) {
                $tmp[Str::camel($key)] = $value;
            }
            $ret['list'][] = $tmp;
        }
        return $ret;
    }
    
    public function save(
        int $id,
        int $systemId,
        int $menuId,
        string $resource,
        string $dataMark,
        string $dataName,
        string $conditions,
        string $fields
    ): bool
    {
        $exists = DataPermission::whereDataMark($dataMark)
            ->where('id', '!=', $id)
            ->first();
        if ($exists) {
            throw new CommonException(ErrorCode::RECORD_EXISTS);
        }
        if ($id > 0) {
            $model = DataPermission::whereId($id)->first();
            if (empty($model)) {
                throw new CommonException(ErrorCode::RECORD_EXCEPTION);
            }
        } else {
            $model = new DataPermission();
            $model->created_at = date('Y-m-d H:i:s');
        }
        $model->system_id = $systemId;
        $model->menu_id = $menuId;
        $model->resource = $resource;
        $model->data_mark = $dataMark;
        $model->data_name = $dataName;
        $model->conditions = $conditions;
        $model->fields = $fields;
        $model->save();
        return true;
    }
}
