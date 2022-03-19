<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/2/8
 */

namespace App\Services;


use App\Services\BaseService;

class RoleService extends BaseService
{
    public function getOptions(): array
    {
        return [];
    }
    
    public function getList(): array
    {
        return [];
    }
    
    public function save(): bool
    {
        return true;
    }
    
    public function saveRoleHasMenu(): bool
    {
        return true;
        
    }
    
    public function saveRoleHasData(): bool
    {
        return true;
        
    }
    
    public function switchStatus(): bool
    {
        return true;
        
    }
}
