<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(

            [
                'username' => 'admin',
            ],

            [
                'name'      => 'مدیر سیستم',
                'email'     => 'admin@example.com',
                'phone'     => null,
                'password'  => Hash::make('123456'),
                'role_id'   => 1,
                'is_active' => true,
            ]

        );
    }
}
