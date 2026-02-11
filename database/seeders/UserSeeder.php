<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create(
            [
                'name' => 'Admin', 
                'phone' => '255123456789',
                'password' => Hash::make('admin'), 
                'email' => 'admin@admin.com', 
                'role' => 'Administrator',
                'created_at' => now(),
                'updated_at' => now()
            ],
        );
    }
}
