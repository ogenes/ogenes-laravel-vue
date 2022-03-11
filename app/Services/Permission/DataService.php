<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/2/8
 */

namespace App\Services\Permission;


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
        return true;
    }
}
