<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('uid')->comment('主键');
            $table->string('account', 32)->nullable(false)->default('')->comment('用户账号');
            $table->string('username', 32)->nullable(false)->default('')->comment('用户名');
            $table->string('password', 32)->nullable(false)->default('')->comment('密码');
            $table->tinyInteger('user_status')->nullable(false)->default(1)->comment('用户状态：0禁用，1启用');
            $table->string('mobile', 11)->nullable(false)->default('')->comment('用户手机号');
            $table->string('email', 32)->nullable(false)->default('')->comment('用户邮箱');
            $table->string('avatar', 128)->nullable(false)->default('')->comment('用户头像');
            $table->string('last_login_ip', 16)->nullable(false)->default('')->comment('最近一次登录IP');
            $table->timestamp('last_login_at')->default('2000-01-01 00:00:01')->comment('最近一次登录时间');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->unique(['account'], 'udx_account');
            $table->unique(['mobile'], 'udx_mobile');
            $table->index(['username'], 'idx_username');
            $table->index(['email'], 'idx_email');
        });
        DB::statement("alter table `user` comment = '用户基础信息表'");
        DB::table('user')->insert([
            'account' => config('common.admin.account', 'admin'),
            'username' => '系统管理员',
            'password' => md5(env('SALT', '') . config('common.admin.password', '123456')),
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
        Schema::dropIfExists('user');
    }
}
