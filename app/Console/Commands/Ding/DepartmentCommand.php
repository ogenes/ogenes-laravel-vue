<?php

namespace App\Console\Commands\Ding;

use App\Helpers\DingHelper;
use Illuminate\Console\Command;

class DepartmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'department:sync';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '从钉钉同步部门';
    
    public function handle()
    {
        $this->line($this->now() . ': Begin sync...');
        $data = DingHelper::getInstance()->syncDepartment(1);
        print_r($data);
        return 0;
    }
    
    private function now()
    {
        return date('Y-m-d H:i:s');
    }
}
