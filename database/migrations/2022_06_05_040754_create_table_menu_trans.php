<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMenuTrans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_trans', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->integer('menu_id')->nullable(false)->comment('菜单ID');
            $table->string('language')->nullable(false)->default('')->comment('语言');
            $table->string('title')->nullable(false)->default('')->comment('菜单');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('updated_at')->default('2000-01-01 00:00:01')->comment('更新时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
        DB::statement("alter table `menu_trans` comment = '菜单语言包'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_trans');
    }
}
