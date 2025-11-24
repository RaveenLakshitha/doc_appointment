<?php
// database/seeders/TherapistSeeder.php

namespace Database\Seeders;

use App\Models\Therapist;
use Illuminate\Database\Seeder;

class TherapistSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Therapist::create([
                'name'           => fake()->name,
                'email'          => "therapist{$i}@example.com",
                'phone'          => '+94' . str_pad($i, 9, '0', STR_PAD_LEFT),
                'license_number' => 'LIC-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'date_of_birth'  => fake()->dateTimeBetween('-60 years', '-25 years'),
                'gender'         => fake()->randomElement(['male', 'female', 'other']),
                'address'        => fake()->address,
                'specialty'      => fake()->randomElement([
                    'Cognitive Behavioral Therapy',
                    'Family Therapy',
                    'Child Psychology',
                    'Trauma & PTSD',
                    'Addiction Counseling',
                    'Mindfulness-Based Therapy',
                    'Psychodynamic Therapy',
                    'Couples Therapy',
                ]),
                'hourly_rate'    => fake()->randomFloat(2, 50, 250),
                'is_active'      => true,
                'is_deleted'     => false,
            ]);
        }
    }
}