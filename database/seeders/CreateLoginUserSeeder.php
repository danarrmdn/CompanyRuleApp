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
            ['email' => 'test2@nsi.co.id'],
            [
                'name' => 'Test2',
                'emp_id' => '9999',
                'password' => Hash::make('test2'),
                'grade' => 7,
                'department' => 'IT',
                'department_id' => 1,
            ]
        );
    }
}
