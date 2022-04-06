<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDictData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dict_data', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->integer('dict_id')->nullable(false)->comment('字典ID');
            $table->integer('sort')->nullable(false)->comment('排序');
            $table->string('label', 32)->nullable(false)->default('')->comment('标签');
            $table->string('value', 32)->nullable(false)->default('')->comment('值');
            $table->string('remark', 128)->nullable(false)->default('')->comment('备注');
            $table->tinyInteger('disable')->nullable(false)->default(0)->comment('为1不可编辑和删除');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->index(['dict_id'], 'idx_dict_id');
        });
        DB::statement("alter table `dict_data` comment = '字典数据表'");
        DB::table('dict_data')->insert([
            'dict_id' => 1,
            'sort' => '1',
            'label' => '权限管理系统',
            'value' => '1',
            'remark' => '当前系统，为系统默认配置',
            'disable' => 1,
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
        Schema::dropIfExists('dict_data');
    }
}
