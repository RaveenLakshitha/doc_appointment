<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;


class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $faker = Faker::create();
        $faker->seed(1234); // Same seed as DoctorSeeder for consistency

        // === 1. Core Staff Users (unchanged) ===
        $staff = [
            ['name' => 'Hospital Admin',        'email' => 'admin@hospital.com',       'role' => 'admin',        'pass' => 'admin123'],
            ['name' => 'Receptionist',          'email' => 'reception@hospital.com',   'role' => 'receptionist','pass' => 'reception123'],
            ['name' => 'Nurse Emily',           'email' => 'emily.nurse@hospital.com', 'role' => 'nurse',        'pass' => 'nurse123'],
            ['name' => 'HR Manager',            'email' => 'hr@hospital.com',          'role' => 'hr',           'pass' => 'hr123'],
        ];

        foreach ($staff as $u) {
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

        // === 2. Predefined Doctors with Known Logins ===
        $knownDoctors = [
            [
                'name' => 'Dr. Ahmad Khan',
                'email' => 'ahmad.khan@hospital.com',
                'password' => 'password123',
                'role' => 'doctor',
            ],
            [
                'name' => 'Dr. Sarah Williams',
                'email' => 'sarah.williams@hospital.com',
                'password' => 'password123',
                'role' => 'doctor',
            ],
        ];

        foreach ($knownDoctors as $doc) {
            $user = User::updateOrCreate(
                ['email' => $doc['email']],
                [
                    'name' => $doc['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make($doc['password']),
                ]
            );
            $user->assignRole($doc['role']);
        }

        // === 3. All Other Predefined Doctors (from your original list) ===
        $predefinedDoctors = [
            ['first' => 'Alexander', 'middle' => 'James',     'last' => 'Thompson',    'gender' => 'male'],
            ['first' => 'Sophia',    'middle' => 'Grace',     'last' => 'Martinez',    'gender' => 'female'],
            ['first' => 'Michael',   'middle' => 'Robert',    'last' => 'Chen',        'gender' => 'male'],
            ['first' => 'Elena',     'middle' => 'Marie',     'last' => 'Rodriguez',   'gender' => 'female'],
            ['first' => 'David',     'middle' => 'Paul',      'last' => 'Kim',         'gender' => 'male'],
            ['first' => 'Rachel',    'middle' => 'Anne',      'last' => 'Patel',       'gender' => 'female'],
            ['first' => 'James',     'middle' => 'William',   'last' => 'O\'Connor',   'gender' => 'male'],
            ['first' => 'Linda',     'middle' => 'Joy',       'last' => 'Anderson',    'gender' => 'female'],
            ['first' => 'Thomas',    'middle' => 'Edward',    'last' => 'Brown',       'gender' => 'male'],
            ['first' => 'Natalie',   'middle' => 'Rose',      'last' => 'Singh',       'gender' => 'female'],
        ];

        foreach ($predefinedDoctors as $doc) {
            $fullName = 'Dr. ' . $doc['first'] . ($doc['middle'] ? ' ' . $doc['middle'] : '') . ' ' . $doc['last'];
            $email = strtolower($doc['first'] . '.' . $doc['last'] . '@hospital.com');

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $fullName,
                    'email_verified_at' => now(),
                    'password' => Hash::make('doctor123'), // Default password for seeded doctors
                ]
            );
            $user->assignRole('doctor');
        }

        // === 4. 10 Random Doctors with Auto-Generated Users ===
        for ($i = 1; $i <= 10; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $firstName = $gender === 'male' ? $faker->firstNameMale : $faker->firstNameFemale;
            $lastName = $faker->lastName;
            $fullName = 'Dr. ' . $firstName . ' ' . $lastName;

            $email = strtolower($firstName . '.' . $lastName . '@hospital.com');

            // Ensure unique email
            $uniqueEmail = $email;
            $counter = 1;
            while (User::where('email', $uniqueEmail)->exists()) {
                $uniqueEmail = $email . $counter;
                $counter++;
            }

            $user = User::create([
                'name' => $fullName,
                'email' => $uniqueEmail,
                'email_verified_at' => now(),
                'password' => Hash::make('doctor123'), // You can change or randomize if needed
            ]);
            $user->assignRole('doctor');
        }
    }
}