<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RolesAndPermissionsSeeder::class,
            CourseSeeder::class,
            ClassSeeder::class,
            StudentSeeder::class,
            UserSeeder::class,
            ClassTeacherSeeder::class,
        ]);
    }
}
