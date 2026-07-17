<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoUsers = [
            [
                'name' => 'Demo Admin',
                'email' => 'admin@demo.com',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ],
            [
                'name' => 'Demo Doctor',
                'email' => 'doctor@demo.com',
                'password' => Hash::make('password'),
                'role' => 'doctor'
            ],
            [
                'name' => 'Demo Receptionist',
                'email' => 'receptionist@demo.com',
                'password' => Hash::make('password'),
                'role' => 'receptionist'
            ],
            [
                'name' => 'Demo HR',
                'email' => 'hr@demo.com',
                'password' => Hash::make('password'),
                'role' => 'hr'
            ]
        ];

        foreach ($demoUsers as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);
            
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            if ($user->wasRecentlyCreated) {
                // Ensure the role exists before assigning
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $user->assignRole($role);
                }
            }
        }
    }
}
