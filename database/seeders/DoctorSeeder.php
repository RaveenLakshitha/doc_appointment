<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\Department;
use App\Models\Specialization;
use App\Models\OptionList;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        Doctor::unguard();

        $faker = Faker::create();
        $faker->seed(1234); // For consistent data across runs

        // Get all required references
        $departments = Department::pluck('id', 'name')->toArray();
        $specializations = Specialization::pluck('id', 'name')->toArray();
        $positions = OptionList::where('type', 'doctor_position')
            ->where('status', true)
            ->pluck('id', 'name')
            ->toArray();

        $doctorsData = [
            [
                'first_name' => 'Alexander',
                'middle_name' => 'James',
                'last_name' => 'Thompson',
                'gender' => 'male',
                'specialization' => 'Interventional Cardiology',
                'department' => 'Cardiology',
                'position' => 'Attending Physician',
                'years_experience' => 18,
                'hourly_rate' => 350.00,
            ],
            [
                'first_name' => 'Sophia',
                'middle_name' => 'Grace',
                'last_name' => 'Martinez',
                'gender' => 'female',
                'specialization' => 'Clinical Cardiology',
                'department' => 'Cardiology',
                'position' => 'Consultant',
                'years_experience' => 14,
                'hourly_rate' => 320.00,
            ],
            [
                'first_name' => 'Michael',
                'middle_name' => 'Robert',
                'last_name' => 'Chen',
                'gender' => 'male',
                'specialization' => 'Pediatric Cardiology',
                'department' => 'Cardiology',
                'position' => 'Specialist',
                'years_experience' => 12,
                'hourly_rate' => 300.00,
            ],
            [
                'first_name' => 'Elena',
                'middle_name' => 'Marie',
                'last_name' => 'Rodriguez',
                'gender' => 'female',
                'specialization' => 'Stroke Neurology',
                'department' => 'Neurology',
                'position' => 'Head of Department',
                'years_experience' => 22,
                'hourly_rate' => 420.00,
            ],
            [
                'first_name' => 'David',
                'middle_name' => 'Paul',
                'last_name' => 'Kim',
                'gender' => 'male',
                'specialization' => 'Epilepsy & Seizure Disorders',
                'department' => 'Neurology',
                'position' => 'Attending Physician',
                'years_experience' => 15,
                'hourly_rate' => 340.00,
            ],
            [
                'first_name' => 'Rachel',
                'middle_name' => 'Anne',
                'last_name' => 'Patel',
                'gender' => 'female',
                'specialization' => 'Joint Replacement Surgery',
                'department' => 'Orthopedics',
                'position' => 'Surgeon',
                'years_experience' => 20,
                'hourly_rate' => 450.00,
            ],
            [
                'first_name' => 'James',
                'middle_name' => 'William',
                'last_name' => 'O\'Connor',
                'gender' => 'male',
                'specialization' => 'Sports Medicine',
                'department' => 'Orthopedics',
                'position' => 'Specialist',
                'years_experience' => 10,
                'hourly_rate' => 280.00,
            ],
            [
                'first_name' => 'Linda',
                'middle_name' => 'Joy',
                'last_name' => 'Anderson',
                'gender' => 'female',
                'specialization' => 'General Pediatrics',
                'department' => 'Pediatrics',
                'position' => 'Medical Director',
                'years_experience' => 25,
                'hourly_rate' => 380.00,
            ],
            [
                'first_name' => 'Thomas',
                'middle_name' => 'Edward',
                'last_name' => 'Brown',
                'gender' => 'male',
                'specialization' => 'Neonatology',
                'department' => 'Pediatrics',
                'position' => 'Chief Resident',
                'years_experience' => 8,
                'hourly_rate' => 260.00,
            ],
            [
                'first_name' => 'Natalie',
                'middle_name' => 'Rose',
                'last_name' => 'Singh',
                'gender' => 'female',
                'specialization' => 'Medical Oncology',
                'department' => 'Oncology',
                'position' => 'Attending Physician',
                'years_experience' => 16,
                'hourly_rate' => 400.00,
            ],
        ];

        // Create the 10 predefined doctors
        foreach ($doctorsData as $data) {
            $this->createDoctor($data, $departments, $specializations, $positions, $faker);
        }

        // Create 10 more random doctors using Faker
        for ($i = 0; $i < 10; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $deptName = $faker->randomElement(array_keys($departments));
            $possibleSpecs = collect($specializations)->filter(function ($id, $name) use ($deptName) {
                return str_contains($name, $deptName) || in_array($deptName, ['Cardiology', 'Neurology', 'Orthopedics', 'Pediatrics', 'Oncology']);
            })->keys()->toArray();

            $specName = $faker->randomElement($possibleSpecs ?: array_keys($specializations));

            $this->createDoctor([
                'first_name' => $gender === 'male' ? $faker->firstNameMale : $faker->firstNameFemale,
                'middle_name' => $faker->optional(0.4)->firstName,
                'last_name' => $faker->lastName,
                'gender' => $gender,
                'specialization' => $specName,
                'department' => $deptName,
                'position' => $faker->randomElement(array_keys($positions)),
                'years_experience' => $faker->numberBetween(3, 30),
                'hourly_rate' => $faker->randomFloat(2, 200, 500),
            ], $departments, $specializations, $positions, $faker);
        }

        Doctor::reguard();
    }

    private function createDoctor(array $data, array $departments, array $specializations, array $positions, $faker)
    {
        Doctor::create([
            'first_name'                => $data['first_name'],
            'middle_name'               => $data['middle_name'] ?? null,
            'last_name'                 => $data['last_name'],
            'date_of_birth'             => $faker->dateTimeBetween('-60 years', '-30 years')->format('Y-m-d'),
            'gender'                    => $data['gender'],
            'address'                   => $faker->streetAddress,
            'city'                      => $faker->city,
            'state'                     => $faker->state,
            'zip_code'                  => $faker->postcode,
            'phone'                     => $faker->unique()->phoneNumber,
            'email'                     => $faker->unique()->safeEmail,
            'emergency_contact_name'    => $faker->name,
            'emergency_contact_phone'   => $faker->phoneNumber,
            'license_number'            => 'LIC-' . $faker->unique()->randomNumber(8),
            'license_expiry_date'       => Carbon::now()->addYears(5),
            'qualifications'            => $faker->sentence,
            'years_experience'          => $data['years_experience'],
            'education'                 => "MD, " . $faker->company . " Medical School",
            'certifications'            => $faker->optional()->sentence,
            'department_id'             => $departments[$data['department']],
            'primary_specialization_id' => $specializations[$data['specialization']],
            'position_id'               => $positions[$data['position']], // Important: uses position_id
            'hourly_rate'               => $data['hourly_rate'],
            'profile_photo'             => null,
            'is_active'                 => true,
        ]);
    }
}