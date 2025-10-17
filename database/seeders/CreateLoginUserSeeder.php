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
            ['email' => 'test@example.com'],
            [
                'name' => 'test',
                'emp_id' => '0000',
                'password' => Hash::make('shokubai'),
                'grade' => 0,
                'department' => 'IT',
                'department_id' => 106,
                'roles' => 1,
            ]
        );
    }
}
