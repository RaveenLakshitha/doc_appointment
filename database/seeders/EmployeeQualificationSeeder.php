<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmployeeQualification;
use App\Models\Employee;
use Faker\Factory as Faker;

class EmployeeQualificationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $faker->seed(1234);

        // Common medical degrees and certifications
        $degrees = [
            'MBBS' => ['Bachelor of Medicine, Bachelor of Surgery', ['University of Health Sciences', 'Aga Khan University', 'King Edward Medical University']],
            'MD' => ['Doctor of Medicine', ['Harvard Medical School', 'Johns Hopkins University', 'Stanford University']],
            'DO' => ['Doctor of Osteopathic Medicine', ['Michigan State University', 'Philadelphia College of Osteopathic Medicine']],
            'BSc Nursing' => ['Bachelor of Science in Nursing', ['Johns Hopkins School of Nursing', 'University of Pennsylvania', 'Yale University']],
            'MSN' => ['Master of Science in Nursing', ['Duke University', 'University of Washington', 'Vanderbilt University']],
            'DNP' => ['Doctor of Nursing Practice', ['Columbia University', 'University of Pittsburgh']],
            'PharmD' => ['Doctor of Pharmacy', ['University of California San Francisco', 'University of Michigan']],
            'FCPS' => ['Fellow of College of Physicians and Surgeons', ['College of Physicians & Surgeons Pakistan']],
            'FRCS' => ['Fellow of the Royal College of Surgeons', ['Royal College of Surgeons']],
            'MRCP' => ['Member of the Royal College of Physicians', ['Royal College of Physicians']],
        ];

        // Only seed qualifications for employees in medical roles (doctors, nurses)
        $medicalEmployees = Employee::whereIn('profession', ['Physician', 'Registered Nurse', 'Nurse'])
            ->orWhere('position', 'like', '%Doctor%')
            ->orWhere('position', 'like', '%Nurse%')
            ->get();

        foreach ($medicalEmployees as $employee) {
            $numQuals = $faker->numberBetween(1, 3);

            for ($i = 0; $i < $numQuals; $i++) {
                // Pick a random degree
                $degreeKey = $faker->randomElement(array_keys($degrees));
                $degreeInfo = $degrees[$degreeKey];
                $degreeName = $degreeInfo[0];
                $institutions = $degreeInfo[1];

                EmployeeQualification::create([
                    'employee_id' => $employee->id,
                    'degree' => $degreeKey . ' - ' . $degreeName,
                    'institution' => $faker->randomElement($institutions),
                    'year_completed' => $faker->year(),
                ]);
            }
        }

        // Special: Add advanced degrees for known senior doctors
        $seniorDoctors = Employee::whereHas('user', function ($q) {
            $q->whereIn('email', ['ahmad.khan@hospital.com', 'sarah.williams@hospital.com']);
        })->get();

        foreach ($seniorDoctors as $doc) {
            EmployeeQualification::create([
                'employee_id' => $doc->id,
                'degree' => 'Fellowship in ' . $doc->specialization,
                'institution' => $faker->randomElement(['Mayo Clinic', 'Cleveland Clinic', 'Johns Hopkins Hospital']),
                'year_completed' => $faker->year(),
            ]);
        }
    }
}
