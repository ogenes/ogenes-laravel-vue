<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->string('role_name', 32)->nullable(false)->default('')->comment('角色名');
            $table->string('desc', 255)->nullable(false)->default('')->comment('描述');
            $table->integer('parent_id')->nullable(false)->comment('上级ID');
            $table->tinyInteger('role_status')->nullable(false)->default(1)->comment('角色状态：0禁用，1启用');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
        DB::statement("alter table `role` comment = '角色基础信息表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role');
    }
}
