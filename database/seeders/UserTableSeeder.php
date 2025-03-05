<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            // Admin

            [
                'name' => 'ram',
                'email' => 'ram@gmail.com',
                'password' => Hash::make('ram@123'),
                'role' => 'admin',
            ],
            [
                'name' => 'hari',
                'email' => 'hari@gmail.com',
                'password' => Hash::make('hari@123'),
                'role' => 'admin',
            ],
            
        ]);
    }
}
