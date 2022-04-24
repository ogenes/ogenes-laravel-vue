<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->integer('uid')->nullable(false)->comment('用户ID');
            $table->string('label', 32)->nullable(false)->default('')->comment('设置项');
            $table->string('value', 32)->nullable(false)->default('')->comment('值');
            $table->string('desc', 255)->nullable(false)->default('')->comment('描述');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->unique(['uid', 'label'], 'udx_label');
        });
        DB::statement("alter table `setting` comment = '系统设置'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting');
    }
}
