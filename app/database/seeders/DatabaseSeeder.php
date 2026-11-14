<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with Ayamo UMKM testing data (excluding products).
     */
    public function run(): void
    {
        $this->call(UmkmTestingSeeder::class);
    }
}
