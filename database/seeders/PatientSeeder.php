<?php
// database/seeders/PatientSeeder.php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('si_LK'); // Sri Lankan names, addresses, phones

        for ($i = 0; $i < 35; $i++) {
            $hasAllergies = $faker->boolean(40);
            $smokes = $faker->boolean(25);
            $hasChronic = $faker->boolean(35);
            $hasFamilyHistory = $faker->boolean(55);

            Patient::create([
                // === Personal Info ===
                'first_name'               => $faker->firstName,
                'middle_name'              => $faker->optional(0.25)->firstName,
                'last_name'                => $faker->lastName,
                'date_of_birth'            => $faker->dateTimeBetween('-80 years', '-5 years')->format('Y-m-d'),
                'gender'                   => $faker->randomElement(['male', 'female', 'other']),
                'marital_status'           => $faker->optional(0.8)->randomElement(['single', 'married', 'divorced', 'widowed']),
                'address'                  => $faker->streetAddress,
                'city'                     => $faker->city,
                'state'                    => $faker->state,
                'zip_code'                 => $faker->postcode,
                'phone'                    => '07' . $faker->numberBetween(10000000, 99999999),
                'alternative_phone'        => $faker->optional(0.6)->phoneNumber,
                'email'                    => $faker->unique()->safeEmail,
                'preferred_contact_method' => $faker->randomElement(['phone', 'email', 'sms']),

                // === Emergency Contact ===
                'emergency_contact_name'        => $faker->name,
                'emergency_contact_relationship'=> $faker->randomElement(['Spouse', 'Parent', 'Sibling', 'Child', 'Friend']),
                'emergency_contact_phone'       => '07' . $faker->numberBetween(10000000, 99999999),
                'emergency_contact_email'       => $faker->optional(0.7)->safeEmail,

                // === Medical Profile ===
                'blood_type'               => $faker->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
                'height_cm'                => $faker->numberBetween(140, 195),
                'weight_kg'                => $faker->numberBetween(45, 130),
                'allergies'                => $hasAllergies ? $faker->randomElements(
                    ['Penicillin', 'Peanuts', 'Latex', 'Dust Mites', 'Shellfish', 'Bee Stings', 'Aspirin', 'Ibuprofen'], 
                    $faker->numberBetween(1, 3)
                ) : null,
                'current_medications'      => $faker->optional(0.6)->randomElements(
                    ['Metformin', 'Amlodipine', 'Atorvastatin', 'Losartan', 'Salbutamol', 'Omeprazole'], 
                    $faker->numberBetween(0, 3)
                ),
                'chronic_conditions'       => $hasChronic ? $faker->randomElements(
                    ['Diabetes Type 2', 'Hypertension', 'Asthma', 'Arthritis', 'Thyroid Disorder', 'Migraine'], 
                    $faker->numberBetween(1, 2)
                ) : null,
                'past_surgeries'           => $faker->optional(0.4)->sentences(2),

                // === Family History ===
                'family_history_diabetes'       => $hasFamilyHistory && $faker->boolean(60),
                'family_history_hypertension'   => $hasFamilyHistory && $faker->boolean(70),
                'family_history_heart_disease'  => $hasFamilyHistory && $faker->boolean(40),
                'family_history_cancer'         => $hasFamilyHistory && $faker->boolean(30),
                'family_history_asthma'         => $hasFamilyHistory && $faker->boolean(25),
                'family_history_mental_health'  => $hasFamilyHistory && $faker->boolean(20),
                'family_history_notes'          => $hasFamilyHistory ? $faker->optional()->sentence : null,

                // === Lifestyle ===
                'smoking_status'           => $smokes ? $faker->randomElement(['former', 'current']) : 'never',
                'alcohol_consumption'      => $faker->randomElement(['none', 'occasional', 'moderate', 'heavy']),
                'exercise_frequency'       => $faker->randomElement(['never', 'rarely', 'weekly', 'daily']),
                'dietary_habits'           => $faker->sentence,

                // === Insurance ===
                'primary_insurance_provider'    => $faker->randomElement(['Asiri Health', 'Hemas Health', 'SLIC', 'Ceylinco Insurance', 'Self Pay']),
                'primary_policy_number'         => $faker->optional(0.8)->bothify('POL-####-???'),
                'primary_group_number'          => $faker->optional(0.7)->numerify('GRP-######'),
                'preferred_billing_method'      => $faker->randomElement(['insurance_first', 'self_pay', 'insurance_only']),
                'payment_methods'               => $faker->randomElements(['credit_card', 'debit_card', 'cash', 'bank_transfer'], $faker->numberBetween(1, 3)),

                // === Consent & Communication ===
                'receive_appointment_reminders' => $faker->boolean(90),
                'receive_lab_results'           => $faker->boolean(85),
                'receive_prescription_notifications' => $faker->boolean(88),
                'receive_newsletter'            => $faker->boolean(30),

                'consent_hipaa'      => $faker->boolean(95),
                'consent_treatment'  => $faker->boolean(97),
                'consent_financial'  => $faker->boolean(96),

                // === System ===
                'medical_record_number' => 'MRN' . str_pad($i + 1000, 7, '0', STR_PAD_LEFT),
                'is_active'             => $faker->boolean(92),
                'is_deleted'            => false,
            ]);
        }
    }
}