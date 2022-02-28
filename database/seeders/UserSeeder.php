<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user')->insert([
            'account' => env('ADMIN_ACCOUNT', 'admin'),
            'username' => '系统管理员',
            'password' => md5(env('SALT', '') . env('ADMIN_PASSWORD', '123456')),
        ]);
    }
}
