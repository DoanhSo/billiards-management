<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Thứ tự quan trọng: roles trước, users sau
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
        ]);
    }
}
