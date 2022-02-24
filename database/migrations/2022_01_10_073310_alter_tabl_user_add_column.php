<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablUserAddColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cy_users', function (Blueprint $table) {
            //
            $table->string('mobile', 11)->after('email')->nullable(false)->default('')->comment('手机号');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cy_users', function (Blueprint $table) {
            //
            $table->dropColumn('mobile');
        });
    }
}
