<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableActionLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_log', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->string('resource', 32)->nullable(false)->default('')->comment('资源');
            $table->integer('resource_id')->nullable(false)->comment('资源ID');
            $table->integer('uid')->nullable(false)->comment('操作人');
            $table->string('type', 32)->nullable(false)->default('')->comment('操作类型');
            $table->longText('remark')->nullable(false)->comment('操作备注');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('操作时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->index(['resource', 'resource_id'], 'idx_resource');
            $table->index(['uid'], 'idx_uid');
        });
        DB::statement("alter table `action_log` comment = '操作日志表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_log');
    }
}
