<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run(): void
    {

        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department' => 'IT Administration',
            'employee_id' => 'ADMIN001',
            'joining_date' => '2023-01-01',
            'email_verified_at' => now(),
        ]);


        User::create([
            'name' => 'John Doe',
            'email' => 'employee1@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'department' => 'Human Resources',
            'employee_id' => 'EMP001',
            'joining_date' => '2023-02-15',
            'email_verified_at' => now(),
        ]);


        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'department' => 'Marketing',
            'employee_id' => 'EMP002',
            'joining_date' => '2023-03-10',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Mike Johnson',
            'email' => 'mike.johnson@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'department' => 'Development',
            'employee_id' => 'EMP003',
            'joining_date' => '2023-01-20',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Sarah Wilson',
            'email' => 'sarah.wilson@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'department' => 'Finance',
            'employee_id' => 'EMP004',
            'joining_date' => '2023-04-05',
            'email_verified_at' => now(),
        ]);
    }
}