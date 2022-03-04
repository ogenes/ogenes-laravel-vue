<?php

namespace App\Console\Commands\Ding;

use App\Helpers\DingHelper;
use App\Models\DepartmentDing;
use Illuminate\Console\Command;

class UserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:sync';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '从钉钉同步用户信息';
    
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line($this->now() . ': Begin sync...');
        $depts = DepartmentDing::all()->toArray();
        foreach ($depts as $dept) {
            
            $deptId = $dept['ding_dept_id'];
            var_dump($deptId);
            if ($deptId === 1) {
                continue;
            }
            DingHelper::getInstance()->syncUser($deptId);
        }
        return 0;
    }
    
    private function now()
    {
        return date('Y-m-d H:i:s');
    }
}
