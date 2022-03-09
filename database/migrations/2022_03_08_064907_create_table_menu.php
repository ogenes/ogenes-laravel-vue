<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->integer('system_id')->nullable(false)->default(1)->comment('系统ID');
            $table->string('menu_name', 32)->nullable(false)->default('')->comment('menu_name');
            $table->tinyInteger('type')->nullable(false)->default(1)->comment('类型：1dir,2menu,3btn');
            $table->integer('parent_id')->nullable(false)->comment('上级ID');
            $table->string('title', 32)->nullable(false)->default('')->comment('title');
            $table->string('icon', 32)->nullable(false)->default('')->comment('icon');
            $table->string('roles', 128)->nullable(false)->default('')->comment('权限标识');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->unique(['menu_name'], 'udx_menu_name');
        });
        DB::statement("alter table `menu` comment = '目录配置表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu');
    }
}
