<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDataPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_permission', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->integer('system_id')->nullable(false)->default(1)->comment('系统ID');
            $table->integer('menu_id')->nullable(false)->default(0)->comment('菜单ID');
            $table->string('resource', 32)->nullable(false)->default('')->comment('数据源');
            $table->string('data_mark', 128)->nullable(false)->default('')->comment('数据权限标识');
            $table->string('data_name', 32)->nullable(false)->default('')->comment('数据权限名称');
            $table->string('conditions', 255)->nullable(false)->default('')->comment('条件参数');
            $table->string('fields', 255)->nullable(false)->default('')->comment('结果字段');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
        DB::statement("alter table `data_permission` comment = '数据权限表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_permission');
    }
}
