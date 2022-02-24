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
        //
        $now = date('Y-m-d H:i:s');
        DB::table('cy_users')->insert([
            'username' => env('ADMIN_USERNAME', 'ogenes'),
            'email' => env('ADMIN_EMAIL', 'ogenes.yi@gmail.com'),
            'password' => Hash::make(env('ADMIN_PASSWORD', '123456')),
            'created_at' => $now,
            'updated_at' => $now,
            'is_active' => 1,
            'active_at' => $now
        ]);
    }
}
