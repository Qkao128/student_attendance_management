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
            'password' => bcrypt('12345'),
            'profile_image' => null,
            'teacher_user_id' => null,
            'student_id' => null,
        ]);
    }
}
