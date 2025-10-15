<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateLoginUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'schoolnarr@gmail.com'],
            [
                'name' => 'Danarusmia',
                'emp_id' => '9999',
                'password' => Hash::make('password123'),
                'grade' => 10,
                'department' => 'Information Technology',
                'department_id' => 106,
                'roles' => 2,
            ]
        );
    }
}
