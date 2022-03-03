<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/3/2
 */

namespace App\Console\Commands;


use App\Helpers\DingHelper;
use Illuminate\Console\Command;

class DepartmentCommand extends Command
{
    protected $signature = 'department:sync';
    
    protected $description = '同步部门';
    
    public function handle() {
        $this->line($this->now() . ': Begin sync...');
        $data = DingHelper::getInstance()->syncDepartment(1);
        print_r($data);
        exit;
    }
    
    private function now()
    {
        return date('Y-m-d H:i:s');
    }
}
