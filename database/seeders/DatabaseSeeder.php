<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'email' => 'admin@spiagge.it',
        ]);

        // Create test user
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Create additional users
        User::factory(8)->create();

        // Run all seeders
        $this->call([
            TaskSeeder::class,
        ]);
    }
}
