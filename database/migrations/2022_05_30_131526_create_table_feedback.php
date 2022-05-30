<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFeedback extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->integer('uid')->nullable(false)->default(0)->comment('用户ID');
            $table->longText('content')->nullable(false)->comment('内容');
            $table->tinyInteger('type')->nullable(false)->default(1)->comment('类型：1新需求，2系统BUG，3优化建议，其他见model');
            $table->tinyInteger('processed')->nullable(false)->default(0)->comment('是否已处理， 1已处理，0未处理');
            $table->string('comments')->nullable(false)->default('')->comment('处理备注');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('updated_at')->default('2000-01-01 00:00:01')->comment('更新时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
        DB::statement("alter table `feedback` comment = '意见反馈'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedback');
    }
}
