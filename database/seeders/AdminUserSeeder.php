<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@shabat.local'],
            [
                'name' => 'Shabat Admin',
                'password' => Hash::make('password'), // ganti di production
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
