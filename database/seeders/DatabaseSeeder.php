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
        // Create test user
        User::factory()->create([
            'name' => 'Hotel Manager',
            'email' => 'manager@hotel.com',
        ]);

        // Create additional staff users
        User::factory(5)->create();

        // Seed hotel data
        $this->call(HotelSeeder::class);
    }
}
