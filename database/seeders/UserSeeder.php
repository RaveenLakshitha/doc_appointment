<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $users = [
            ['name' => 'Hospital Admin',        'email' => 'admin@hospital.com',       'role' => 'admin',        'pass' => 'admin123'],
            ['name' => 'Dr. Ahmad Khan',        'email' => 'ahmad.khan@hospital.com',  'role' => 'doctor',       'pass' => 'password123'],
            ['name' => 'Dr. Sarah Williams',    'email' => 'sarah.williams@hospital.com','role' => 'doctor',      'pass' => 'password123'],
            ['name' => 'Receptionist',          'email' => 'reception@hospital.com',   'role' => 'receptionist','pass' => 'reception123'],
            ['name' => 'Nurse Emily',           'email' => 'emily.nurse@hospital.com', 'role' => 'nurse',        'pass' => 'nurse123'],
            ['name' => 'HR Manager',            'email' => 'hr@hospital.com',          'role' => 'hr',           'pass' => 'hr123'],
        ];

        foreach ($users as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make($u['pass']),
                ]
            );
            $user->assignRole($u['role']);
        }
    }
}