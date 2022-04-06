<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDict extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dict', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->string('dict_name', 32)->nullable(false)->default('')->comment('字典名称');
            $table->string('symbol', 32)->nullable(false)->default('')->comment('字典符号');
            $table->string('remark', 128)->nullable(false)->default('')->comment('备注');
            $table->tinyInteger('disable')->nullable(false)->default(0)->comment('为1不可编辑和删除');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->unique(['symbol'], 'udx_symbol');
        });
        DB::statement("alter table `dict` comment = '字典表'");
        DB::table('dict')->insert([
            'id' => 1,
            'dict_name' => '系统列表',
            'symbol' => 'system',
            'remark' => '参与权限管理的系统',
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
        Schema::dropIfExists('dict');
    }
}
