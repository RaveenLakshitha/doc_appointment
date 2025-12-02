<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        Department::unguard();

        $departments = [
            [
                'name'        => 'Cardiology',
                'location'    => 'Building A - 3rd Floor',
                'email'       => 'cardiology@hospital.com',
                'phone'       => '+1234567001',
                'status'      => true,
                'description' => 'Department of Cardiovascular Medicine',
            ],
            [
                'name'        => 'Neurology',
                'location'    => 'Building B - 2nd Floor',
                'email'       => 'neurology@hospital.com',
                'phone'       => '+1234567002',
                'status'      => true,
                'description' => 'Department of Neurology and Stroke Care',
            ],
            [
                'name'        => 'Orthopedics',
                'location'    => 'Building A - 1st Floor',
                'email'       => 'orthopedics@hospital.com',
                'phone'       => '+1234567003',
                'status'      => true,
                'description' => 'Department of Orthopedic Surgery',
            ],
            [
                'name'        => 'Pediatrics',
                'location'    => 'Building C - 1st Floor',
                'email'       => 'pediatrics@hospital.com',
                'phone'       => '+1234567004',
                'status'      => true,
                'description' => 'Department of Child Health',
            ],
            [
                'name'        => 'Oncology',
                'location'    => 'Building D - 4th Floor',
                'email'       => 'oncology@hospital.com',
                'phone'       => '+1234567005',
                'status'      => true,
                'description' => 'Cancer Treatment and Research Center',
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }

        Department::reguard();
    }
}