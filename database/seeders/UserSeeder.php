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
            'account' => 'developer',
            'username' => '开发人员',
            'password' => md5(env('SALT', '') . '123456'),
            'mobile' => '16666666666',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
