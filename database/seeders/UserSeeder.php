<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@donlight.com'],
            [
                'name' => 'Admin Donlight',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
    }
}