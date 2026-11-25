<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's demo data and local demonstration accounts.
     */
    public function run(): void
    {
        $this->call(DemoDataSeeder::class);
    }
}
