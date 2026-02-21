<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo uploader
        User::factory()->create([
            'name'  => 'Uploader User',
            'email' => 'uploader@gmail.com',
            'role'  => 'uploader',
        ]);

        // Create demo viewer
        User::factory()->create([
            'name'  => 'Viewer User',
            'email' => 'viewer@gmail.com',
            'role'  => 'viewer',
        ]);
    }
}
