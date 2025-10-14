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
            ['email' => 'test@gmail.com'],
            [
                'name' => 'test2',
                'emp_id' => '9999',
                'password' => Hash::make('test2'),
                'grade' => 4,
                'department' => 'IT',
                'department_id' => 1,
            ]
        );
    }
}
