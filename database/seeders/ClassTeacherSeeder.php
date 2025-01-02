<?php

namespace Database\Seeders;

use App\Enums\Language;
use App\Models\ClassTeacher;
use Illuminate\Database\Seeder;

class ClassTeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $class = ClassTeacher::create([
            'user_id' => 1,
            'class_id' =>  1,
        ]);


        $class = ClassTeacher::create([
            'user_id' => 2,
            'class_id' => 2,
        ]);


        $class = ClassTeacher::create([
            'user_id' => 2,
            'class_id' => 3,
        ]);
    }
}
