<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo uploader (skip if already exists)
        User::firstOrCreate(
            ['email' => 'uploader@gmail.com'],
            [
                'name'     => 'Uploader User',
                'password' => Hash::make('password'),
                'role'     => 'uploader',
            ]
        );

        // Create demo viewer (skip if already exists)
        User::firstOrCreate(
            ['email' => 'viewer@gmail.com'],
            [
                'name'     => 'Viewer User',
                'password' => Hash::make('password'),
                'role'     => 'viewer',
            ]
        );
    }
}
