<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMessageRead extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_read', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->integer('mid')->nullable(false)->comment('消息id');
            $table->integer('uid')->nullable(false)->comment('用户ID');
            $table->integer('times')->nullable(false)->comment('浏览次数');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('首次浏览时间');
            $table->timestamp('updated_at')->default('2000-01-01 00:00:01')->comment('最近一次浏览时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
        DB::statement("alter table `message_read` comment = '消息浏览记录表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_read');
    }
}
