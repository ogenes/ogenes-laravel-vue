<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->integer('author_id')->nullable(false)->default(0)->comment('用户ID');
            $table->integer('cat_id')->nullable(false)->default(0)->comment('类别ID');
            $table->string('title', 255)->nullable(false)->default('')->comment('标题');
            $table->string('banner', 255)->nullable(false)->default('')->comment('图片');
            $table->text('desc')->nullable(false)->comment('简述');
            $table->longText('text')->nullable(false)->comment('内容');
            $table->tinyInteger('top')->nullable(false)->default(0)->comment('是否置顶：1是，0不隐藏');
            $table->tinyInteger('hidden')->nullable(false)->default(0)->comment('是否隐藏：1是，0不隐藏');
            $table->string('publisher', 32)->nullable(false)->default('')->comment('公布者');
            $table->timestamp('publish_time')->nullable(false)->default('2000-01-01 00:00:01')->comment('公布时间');
            $table->timestamp('created_at')->default('2000-01-01 00:00:01')->comment('创建时间');
            $table->timestamp('updated_at')->default('2000-01-01 00:00:01')->comment('更新时间');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
        DB::statement("alter table `message` comment = '系统公告'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message');
    }
}
