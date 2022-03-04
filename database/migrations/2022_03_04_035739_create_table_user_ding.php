<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUserDing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_ding', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->integer('uid')->nullable(false)->comment('用户ID');
            $table->string('userid', 32)->nullable(false)->default('')->comment('');
            $table->string('unionid', 32)->nullable(false)->default('')->comment('');
            $table->string('name', 32)->nullable(false)->default('')->comment('用户名');
            $table->string('mobile', 11)->nullable(false)->default('')->comment('用户手机号');
            $table->string('email', 32)->nullable(false)->default('')->comment('');
            $table->string('title', 32)->nullable(false)->default('')->comment('');
            $table->string('boss', 32)->nullable(false)->default('')->comment('');
            $table->timestamp('hired_date')->default('2000-01-01 00:00:01')->comment('入职时间');
            $table->tinyInteger('active')->nullable(false)->default(1)->comment('是否激活');
            $table->tinyInteger('leader')->nullable(false)->default(1)->comment('');
            $table->string('avatar', 128)->nullable(false)->default('')->comment('用户头像');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
        DB::statement("alter table `user_ding` comment = '钉钉用户表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_ding');
    }
}
