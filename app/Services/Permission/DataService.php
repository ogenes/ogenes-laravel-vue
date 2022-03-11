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
use App\Services\BaseService;

class DataService extends BaseService
{
    public function getList(
        int $systemId,
        int $menuId,
        string $resource,
        string $dataMark,
        string $dataName
    ): array
    {
        return [];
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
