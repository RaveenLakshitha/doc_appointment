<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmployeeLicense;
use App\Models\Employee;
use Carbon\Carbon;
use Faker\Factory as Faker;

class EmployeeLicenseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $faker->seed(1234);

        $licenseTypes = [
            'Medical License',
            'State Medical Board License',
            'DEA Registration',
            'Nursing License (RN)',
            'Advanced Practice Nurse (APN)',
            'Board Certification - ' . $faker->randomElement(['Cardiology', 'Neurology', 'Pediatrics', 'Internal Medicine', 'Surgery']),
            'Controlled Substance License',
            'BLS Certification',
            'ACLS Certification',
            'PALS Certification',
        ];

        $authorities = [
            'Pakistan Medical Commission',
            'Medical Board of California',
            'Texas Medical Board',
            'New York State Board of Medicine',
            'American Board of Internal Medicine',
            'American Board of Pediatrics',
            'American Nurses Credentialing Center',
            'Drug Enforcement Administration (DEA)',
        ];

        // Seed licenses for medical professionals
        $medicalEmployees = Employee::whereIn('profession', ['Physician', 'Registered Nurse', 'Nurse'])
            ->orWhere('position', 'like', '%Doctor%')
            ->orWhere('position', 'like', '%Nurse%')
            ->get();

        foreach ($medicalEmployees as $employee) {
            $numLicenses = $employee->profession === 'Physician' ? $faker->numberBetween(3, 6) : $faker->numberBetween(2, 4);

            for ($i = 0; $i < $numLicenses; $i++) {
                $issueDate = Carbon::now()->subYears(rand(1, 10))->subMonths(rand(0, 11));
                $expiryDate = (bool)rand(0, 1) ? $issueDate->copy()->addYears(rand(2, 10)) : null;

                $type = $faker->randomElement($licenseTypes);
                if (str_contains($type, 'Board Certification')) {
                    $type = 'Board Certification - ' . $employee->specialization;
                }

                EmployeeLicense::create([
                    'employee_id' => $employee->id,
                    'license_type' => $type,
                    'license_number' => strtoupper($faker->bothify('??#####')),
                    'issue_date' => $issueDate,
                    'expiry_date' => $expiryDate,
                    'issuing_authority' => $faker->randomElement($authorities),
                ]);
            }
        }
    }
}
