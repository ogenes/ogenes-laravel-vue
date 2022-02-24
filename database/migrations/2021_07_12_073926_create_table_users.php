<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cy_users', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->string('username', 32)->nullable(false)->default('')->comment('用户名');
            $table->string('email', 32)->nullable(false)->default('')->comment('邮箱');
            $table->string('password', 32)->nullable(false)->default('')->comment('密码');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->tinyInteger('is_active')->nullable(false)->default(0)->comment('是否已激活');
            $table->timestamp('active_at')->default('2000-01-01 00:00:01')->comment('激活时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->unique(['email']);
        });
        DB::statement("alter table `cy_users` comment = '用户表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cy_users');
    }
}
