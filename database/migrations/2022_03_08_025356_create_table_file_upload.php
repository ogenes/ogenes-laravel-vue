<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFileUpload extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_upload', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->integer('uid')->default(0)->comment('用户ID');
            $table->string('source', 64)->nullable(false)->default('')->comment('上传来源');
            $table->string('original_name', 128)->nullable(false)->default('')->comment('文件名');
            $table->string('object_id', 128)->nullable(false)->default('')->comment('唯一标识');
            $table->string('path', 128)->nullable(false)->default('')->comment('路径');
            $table->integer('size')->nullable(false)->default(0)->comment('size');
            $table->string('ext', 10)->nullable(false)->default('')->comment('扩展名');
            $table->tinyInteger('file_status')->nullable(false)->default(1)->comment('1正常，0已删除');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->index('source', 'idx_source');
            $table->unique(['uid', 'source', 'object_id'], 'udx_uso');
        });
        DB::statement("alter table `file_upload` comment = '文件上传记录'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_upload');
    }
}
