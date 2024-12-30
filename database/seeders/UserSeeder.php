<?php

namespace Database\Seeders;

use App\Enums\Language;
use App\Models\User;
use App\Enums\UserType;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('12345678'),
            'profile_image' => null,
            'teacher_user_id' => null,
            'student_id' => null,
        ]);

        $user->assignRole(UserType::SuperAdmin()->key);

        $user = User::create([
            'username' => 'teacher',
            'email' => 'teacher@teacher.com',
            'password' => bcrypt('12345678'),
            'profile_image' => null,
            'teacher_user_id' => null,
            'student_id' => null,
        ]);

        $user->assignRole(UserType::Admin()->key);

        $user = User::create([
            'username' => 'teacher',
            'email' => 'teacher@teacher.com',
            'password' => bcrypt('12345678'),
            'profile_image' => null,
            'teacher_user_id' => 2,
            'student_id' => 1,
        ]);

        $user->assignRole(UserType::Monitor()->key);
    }
}
