<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cy_files', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->integer('uid')->default(0)->comment('用户ID');
            $table->string('original_name', 128)->nullable(false)->default('')->comment('文件名');
            $table->string('object_id', 128)->nullable(false)->default('')->comment('唯一标识');
            $table->string('path', 128)->nullable(false)->default('')->comment('路径');
            $table->integer('size')->nullable(false)->default(0)->comment('size');
            $table->string('ext', 10)->nullable(false)->default('')->comment('扩展名');
            $table->string('remark', 255)->nullable(false)->default('')->comment('备注');
            $table->string('ip_address', 32)->nullable(false)->default('')->comment('IP地址');
            $table->tinyInteger('state')->nullable(false)->default(0)->comment('0正常，1已删除');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->index('uid', 'idx_uid');
            $table->index('object_id', 'idx_object_id');
        });
        DB::statement("alter table `cy_files` comment = '文件记录'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cy_files');
    }
}
