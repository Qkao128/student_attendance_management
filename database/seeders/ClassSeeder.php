<?php

namespace Database\Seeders;

use App\Enums\Language;
use App\Models\Classes;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $class = Classes::create([
            'name' => 'Class A',
            'course_id' =>  1,
            'is_disabled' => false,
            'date_from' => '2022-10-01',
            'date_to' => '2025-1-31',
        ]);


        $class = Classes::create([
            'name' => 'Class B',
            'course_id' => 1,
            'is_disabled' => false,
            'date_from' => '2022-10-01',
            'date_to' => '2025-1-31',
        ]);

        $class = Classes::create([
            'name' => 'Class C',
            'course_id' => 2,
            'is_disabled' => false,
            'date_from' => '2022-10-01',
            'date_to' => '2025-1-31',
        ]);
    }
}
