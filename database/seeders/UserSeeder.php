<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 6,
            'name' => 'Merchant6',
            'email' => Str::random(10).'@gmail.com',
            'password' => Hash::make('password'),
            'key' => 'KaTf5tZYHx4v7pgZ',
            'format' => 'application/json',
            'amount_limit' => 3000,
        ]);

        DB::table('users')->insert([
            'id' => 816,
            'name' => 'Merchant816',
            'email' => Str::random(10).'@gmail.com',
            'password' => Hash::make('password'),
            'key' => 'rTaasVHeteGbhwBx',
            'format' => 'multipart/form-data',
            'amount_limit' => 5000,
        ]);
    }
}
