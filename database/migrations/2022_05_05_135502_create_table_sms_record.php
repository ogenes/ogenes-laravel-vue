<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSmsRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_record', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->integer('uid')->default(0)->comment('用户ID');
            $table->string('mobile', 11)->nullable(false)->default('')->comment('手机号');
            $table->string('sign_name', 128)->nullable(false)->default('')->comment('签名');
            $table->string('template_code', 32)->nullable(false)->default('')->comment('模板');
            $table->text('template_param')->nullable(false)->comment('模板参数');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('send_at')->default('2000-01-01 00:00:01')->comment('发送时间');
            $table->tinyInteger('result')->default(1)->comment('1成功，0失败');
            $table->text('reason')->nullable(false)->comment('失败原因');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->index('mobile', 'idx_mobile');
        });
        DB::statement("alter table `sms_record` comment = '短信记录表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_record');
    }
}
