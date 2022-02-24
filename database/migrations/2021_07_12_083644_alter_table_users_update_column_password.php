<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUsersUpdateColumnPassword extends Migration
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
            $table->string('password', 60)->after('email')->nullable(false)->default('')->comment('密码')->change();
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
        });
    }
}
