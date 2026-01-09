<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $faker->seed(1234);

        // Create missing support departments if they don't exist
        $supportDepts = [
            'Administration'     => 'Main Building - Admin Wing',
            'Human Resources'    => 'Main Building - 2nd Floor',
            'Nursing'            => 'All Wards',
            'Pharmacy'           => 'Building A - Ground Floor',
            'Laboratory'         => 'Building B - Basement',
        ];

        foreach ($supportDepts as $name => $location) {
            Department::firstOrCreate(
                ['name' => $name],
                [
                    'location'    => $location,
                    'email'       => strtolower(str_replace(' ', '.', $name)) . '@hospital.com',
                    'phone'       => '+1234567' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT),
                    'status'      => true,
                    'description' => "Department of {$name}",
                ]
            );
        }

        // Load all departments
        $departments = Department::pluck('id', 'name')->toArray();

        // Generate employee codes EMP-0001, EMP-0002, ...
        $highestCode = Employee::where('employee_code', 'like', 'EMP-%')
            ->orderByRaw("CAST(SUBSTRING(employee_code, 5) AS UNSIGNED) DESC")
            ->value('employee_code');

        $nextNumber = 1;
        if ($highestCode && preg_match('/EMP-(\d{4,})/', $highestCode, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }

        $generateCode = function () use (&$nextNumber) {
            $code = 'EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $nextNumber++;
            return $code;
        };

        $adminEmployee = null;
        $hrEmployee = null;
        $seniorNurseEmployee = null;

        $coreStaff = [
            [
                'email' => 'admin@hospital.com',
                'first_name' => 'Hospital',
                'middle_name' => '',
                'last_name' => 'Administrator',
                'position' => 'System Administrator',
                'profession' => 'Hospital Administrator',
                'department' => 'Administration',
                'salary' => 120000,
            ],
            [
                'email' => 'reception@hospital.com',
                'first_name' => 'Reception',
                'middle_name' => 'Front',
                'last_name' => 'Desk',
                'position' => 'Front Desk Receptionist',
                'profession' => 'Receptionist',
                'department' => 'Administration',
                'salary' => 55000,
            ],
            [
                'email' => 'emily.nurse@hospital.com',
                'first_name' => 'Emily',
                'middle_name' => 'Grace',
                'last_name' => 'Johnson',
                'position' => 'Head Nurse',
                'profession' => 'Registered Nurse',
                'specialization' => 'General Nursing',
                'department' => 'Nursing',
                'salary' => 95000,
            ],
            [
                'email' => 'hr@hospital.com',
                'first_name' => 'Sarah',
                'middle_name' => 'Jane',
                'last_name' => 'Miller',
                'position' => 'Human Resources Manager',
                'profession' => 'HR Manager',
                'department' => 'Human Resources',
                'salary' => 105000,
            ],
        ];

        foreach ($coreStaff as $staff) {
            $user = User::where('email', $staff['email'])->first();
            if (!$user) continue;

            $deptId = $departments[$staff['department']];

            $photo = $faker->boolean(80)
                ? "https://picsum.photos/seed/employee{$user->id}/300/300"
                : null;

            $employee = Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $staff['first_name'],
                    'middle_name' => $staff['middle_name'],
                    'last_name' => $staff['last_name'],
                    'photo' => $photo,
                    'profession' => $staff['profession'],
                    'specialization' => $staff['specialization'] ?? null,
                    'position' => $staff['position'],
                    'department_id' => $deptId,
                    'employee_code' => $generateCode(),
                    'hire_date' => Carbon::createFromDate(rand(2018, 2024), rand(1, 12), rand(1, 28)),
                    'employment_type' => 'Full-time',
                    'work_hours_weekly' => 40,
                    'salary' => $staff['salary'],
                    'payment_frequency' => 'Monthly',
                    'gender' => $faker->randomElement(['male', 'female', 'other']),
                    'date_of_birth' => $faker->dateTimeBetween('-55 years', '-25 years')->format('Y-m-d'),
                    'address' => $faker->streetAddress,
                    'city' => $faker->city,
                    'state' => $faker->state,
                    'postal_code' => $faker->postcode,
                    'country' => 'United States',
                    'emergency_contact_name' => $faker->name,
                    'emergency_contact_phone' => $faker->phoneNumber,
                    'professional_bio' => $faker->paragraph(4),

                    // Qualifications
                    'degree' => $faker->randomElement(['Bachelor of Science', 'MBA', 'BSN', 'Bachelor of Nursing']),
                    'institution' => $faker->randomElement(['Harvard University', 'Stanford University', 'NYU', 'UCLA']),
                    'year_completed' => $faker->year(),

                    // Licenses (safe handling of null)
                    'license_type' => $faker->optional(0.7)->word(),
                    'license_number' => $faker->optional(0.7)->regexify('[A-Z0-9]{8}'),
                    'license_issue_date' => ($issue = $faker->optional(0.7)->dateTime()) ? $issue->format('Y-m-d') : null,
                    'license_expiry_date' => ($expiry = $faker->optional(0.6)->dateTimeBetween('+1 year', '+10 years')) ? $expiry->format('Y-m-d') : null,
                    'license_issuing_authority' => $faker->optional(0.7)->company(),

                    'status' => true,
                ]
            );

            if ($staff['email'] === 'admin@hospital.com') {
                $adminEmployee = $employee;
            } elseif ($staff['email'] === 'hr@hospital.com') {
                $hrEmployee = $employee;
                $employee->update(['reporting_to' => $adminEmployee?->id]);
            } elseif ($staff['email'] === 'emily.nurse@hospital.com') {
                $seniorNurseEmployee = $employee;
            } elseif ($staff['email'] === 'reception@hospital.com') {
                $employee->update(['reporting_to' => $hrEmployee?->id]);
            }
        }

        // Doctors
        $doctorUsers = User::role('doctor')->get();

        foreach ($doctorUsers as $user) {
            $cleanName = str_replace('Dr. ', '', trim($user->name));
            $nameParts = explode(' ', $cleanName);
            $firstName = $nameParts[0];
            $middleName = $nameParts[1] ?? $faker->firstName();
            $lastName = end($nameParts);

            $specialization = $faker->randomElement(array_keys($departments));
            $departmentId = $departments[$specialization];

            $isSenior = in_array($user->email, ['ahmad.khan@hospital.com', 'sarah.williams@hospital.com']);
            $position = $isSenior ? 'Senior Consultant Physician' : 'Consultant Physician';

            $hireYear = $isSenior ? rand(2015, 2020) : rand(2020, 2025);
            $hireDate = Carbon::createFromDate($hireYear, rand(1, 12), rand(1, 28));

            $photo = $faker->boolean(90)
                ? "https://picsum.photos/seed/doctor{$user->id}/300/300"
                : null;

            Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'photo' => $photo,
                    'profession' => 'Physician',
                    'specialization' => $specialization,
                    'position' => $position,
                    'department_id' => $departmentId,
                    'employee_code' => $generateCode(),
                    'hire_date' => $hireDate,
                    'employment_type' => $faker->randomElement(['Full-time', 'Part-time']),
                    'work_hours_weekly' => $faker->randomElement([36, 40, 44]),
                    'salary' => $isSenior ? rand(250000, 350000) : rand(180000, 280000),
                    'payment_frequency' => 'Monthly',
                    'gender' => $faker->randomElement(['male', 'female']),
                    'date_of_birth' => $faker->dateTimeBetween('-60 years', '-35 years')->format('Y-m-d'),
                    'address' => $faker->streetAddress,
                    'city' => $faker->city,
                    'state' => $faker->state,
                    'postal_code' => $faker->postcode,
                    'country' => 'United States',
                    'emergency_contact_name' => $faker->name,
                    'emergency_contact_phone' => $faker->phoneNumber,
                    'professional_bio' => "Dedicated {$specialization} specialist with extensive experience in patient care and advanced medical procedures.",

                    // Qualifications
                    'degree' => $faker->randomElement(['MD', 'MBBS', 'DO']),
                    'institution' => $faker->randomElement(['Harvard Medical School', 'Johns Hopkins', 'Mayo Clinic School of Medicine']),
                    'year_completed' => $faker->year(),

                    // Licenses (doctors always have them)
                    'license_type' => 'Medical License',
                    'license_number' => $faker->regexify('[A-Z]{2}[0-9]{6}'),
                    'license_issue_date' => $faker->dateTimeBetween('-15 years', '-1 year')->format('Y-m-d'),
                    'license_expiry_date' => $faker->dateTimeBetween('+1 year', '+10 years')->format('Y-m-d'),
                    'license_issuing_authority' => $faker->randomElement(['State Medical Board', 'American Board of Medical Specialties']),

                    'reporting_to' => $seniorNurseEmployee?->id ?? $adminEmployee?->id ?? null,
                    'status' => true,
                ]
            );
        }
    }
}