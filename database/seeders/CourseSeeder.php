<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $course = Course::create([
            'name' => 'Course 1',
        ]);


        $course = Course::create([
            'name' => 'Course 2',
        ]);
    }
}
