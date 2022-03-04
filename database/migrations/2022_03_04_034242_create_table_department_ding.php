<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDepartmentDing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_ding', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->string('name', 32)->nullable(false)->default('')->comment('部门名字');
            $table->integer('dept_id')->nullable(false)->comment('部门ID');
            $table->integer('parent_id')->nullable(false)->comment('上级部门ID');
            $table->integer('ding_dept_id')->nullable(false)->comment('钉钉部门ID');
            $table->integer('ding_parent_id')->nullable(false)->comment('钉钉上级部门ID');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->unique(['ding_dept_id'], 'unx_ding_dept_id');
        });
        DB::statement("alter table `department_ding` comment = '钉钉部门表'");
        DB::table('department_ding')->insert([
            'name' => config('common.corp', '研发测试有限公司'),
            'dept_id' => 1,
            'ding_dept_id' => 1,
            'parent_id' => 0,
            'ding_parent_id' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('department_ding');
    }
}
