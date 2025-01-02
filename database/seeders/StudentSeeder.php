<?php

namespace Database\Seeders;

use App\Enums\Language;
use App\Models\student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $class = Student::create([
            'class_id' => 1,
            'profile_image' =>  null,
            'name' => 'Alex',
            'gender' => 'Male',
            'enrollment_date' => '2024-12-27',
        ]);


        $class = Student::create([
            'class_id' => 1,
            'profile_image' => null,
            'name' => 'John',
            'gender' => 'Male',
            'enrollment_date' => '2024-12-27',
        ]);


        $class = Student::create([
            'class_id' => 1,
            'profile_image' => null,
            'name' => 'Ben',
            'gender' => 'Female',
            'enrollment_date' => '2024-12-27',
        ]);

        $class = Student::create([
            'class_id' => 1,
            'profile_image' => null,
            'name' => 'Ali',
            'gender' => 'Female',
            'enrollment_date' => '2024-12-27',
        ]);


        $class = Student::create([
            'class_id' => 2,
            'profile_image' => null,
            'name' => 'William',
            'gender' => 'Male',
            'enrollment_date' => '2024-12-27',
        ]);

        $class = Student::create([
            'class_id' => 2,
            'profile_image' => null,
            'name' => 'Robert',
            'gender' => 'Male',
            'enrollment_date' => '2024-12-27',
        ]);


        $class = Student::create([
            'class_id' => 2,
            'profile_image' => null,
            'name' => 'Michael',
            'gender' => 'Male',
            'enrollment_date' => '2024-12-27',
        ]);


        $class = Student::create([
            'class_id' => 2,
            'profile_image' => null,
            'name' => 'Mary ',
            'gender' => 'Female',
            'enrollment_date' => '2024-12-27',
        ]);


        $class = Student::create([
            'class_id' => 2,
            'profile_image' => null,
            'name' => 'Claude',
            'gender' => 'Male',
            'enrollment_date' => '2024-12-27',
        ]);



        $class = Student::create([
            'class_id' => 3,
            'profile_image' => null,
            'name' => 'Luz',
            'gender' => 'Male',
            'enrollment_date' => '2024-12-27',
        ]);


        $class = Student::create([
            'class_id' => 3,
            'profile_image' => null,
            'name' => 'Jennifer',
            'gender' => 'Female',
            'enrollment_date' => '2024-12-27',
        ]);


        $class = Student::create([
            'class_id' => 3,
            'profile_image' => null,
            'name' => 'Linda',
            'gender' => 'Female',
            'enrollment_date' => '2024-12-27',
        ]);

        $class = Student::create([
            'class_id' => 3,
            'profile_image' => null,
            'name' => 'Luna',
            'gender' => 'Female',
            'enrollment_date' => '2024-12-27',
        ]);
    }
}
