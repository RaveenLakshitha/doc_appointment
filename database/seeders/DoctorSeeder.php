<?php
// database/seeders/DoctorSeeder.php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $specialties = [
            'Cardiology', 'Dermatology', 'Endocrinology', 'Gastroenterology',
            'Hematology', 'Neurology', 'Oncology', 'Pediatrics',
            'Psychiatry', 'Pulmonology', 'Rheumatology', 'Urology',
            'General Surgery', 'Orthopedics', 'Ophthalmology', 'ENT',
            'Radiology', 'Anesthesiology', 'Pathology', 'Emergency Medicine'
        ];
        $departments = ['Cardiology', 'Surgery', 'Pediatrics', 'Internal Medicine', 'Emergency', 'Radiology', 'Psychiatry', 'OB/GYN'];
        $positions = ['Consultant', 'Senior Resident', 'Attending Physician', 'Chief of Department', 'Specialist'];

        for ($i = 0; $i < 20; $i++) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $gender = $faker->randomElement(['male', 'female', 'other']);
            $isActive = $faker->boolean(85);

            Doctor::create([
                'first_name' => $firstName,
                'middle_name' => $faker->optional(0.3)->firstName,
                'last_name' => $lastName,
                'date_of_birth' => $faker->dateTimeBetween('-60 years', '-30 years')->format('Y-m-d'),
                'gender' => $gender,
                'address' => $faker->streetAddress,
                'city' => $faker->city,
                'state' => $faker->state,
                'zip_code' => $faker->postcode,
                'email' => strtolower($firstName . '.' . $lastName . '@hospital.com'),
                'phone' => $faker->numerify('##########'),
                'emergency_contact_name' => $faker->name,
                'emergency_contact_phone' => $faker->numerify('##########'),

                'primary_specialty' => $primary = $faker->randomElement($specialties),
                'secondary_specialty' => $faker->optional(0.4)->randomElement($specialties),
                'license_number' => 'LIC-' . strtoupper(Str::random(3)) . '-' . $faker->unique()->randomNumber(6),
                'license_expiry_date' => $faker->dateTimeBetween('+1 year', '+5 years')->format('Y-m-d'),
                'qualifications' => $faker->randomElement(['MD', 'DO', 'MBBS', 'MD, PhD']),
                'years_experience' => $years = $faker->numberBetween(3, 35),
                'education' => "Medical School: {$faker->company}\nResidency: {$faker->company}",
                'certifications' => $faker->sentence,
                'department' => $faker->randomElement($departments),
                'position' => $faker->randomElement($positions),
                'hourly_rate' => $faker->randomFloat(2, 80, 300),
                'profile_photo' => null,
                'is_active' => $isActive,
            ]);
        }
    }
}