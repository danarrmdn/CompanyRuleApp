<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateLoginUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'krisnabudiutomo5@gmail.com'],
            [
                'name' => 'Ramdani',
                'emp_id' => '9997',
                'password' => Hash::make('password123'),
                'grade' => 8,
                'department' => 'Information Technology',
                'department_id' => 106,
            ]
        );
    }
}
