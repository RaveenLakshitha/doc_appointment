<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        Specialization::unguard();

        $specializations = [
            ['name' => 'Interventional Cardiology', 'department_id' => 1, 'description' => 'Catheter-based treatment of heart diseases'],
            ['name' => 'Clinical Cardiology',        'department_id' => 1, 'description' => 'Non-invasive cardiac care'],
            ['name' => 'Pediatric Cardiology',      'department_id' => 1, 'description' => 'Congenital and acquired heart disease in children'],
            ['name' => 'Stroke Neurology',          'department_id' => 2, 'description' => 'Acute stroke management and prevention'],
            ['name' => 'Epilepsy & Seizure Disorders', 'department_id' => 2, 'description' => 'Diagnosis and treatment of epilepsy'],
            ['name' => 'Joint Replacement Surgery', 'department_id' => 3, 'description' => 'Hip and knee replacement'],
            ['name' => 'Sports Medicine',           'department_id' => 3, 'description' => 'Injury prevention and treatment in athletes'],
            ['name' => 'General Pediatrics',        'department_id' => 4, 'description' => 'Primary care for infants, children and adolescents'],
            ['name' => 'Neonatology',               'department_id' => 4, 'description' => 'Care of newborns and premature infants'],
            ['name' => 'Medical Oncology',          'department_id' => 5, 'description' => 'Chemotherapy and targeted therapy'],
        ];

        foreach ($specializations as $spec) {
            Specialization::create($spec);
        }

        Specialization::reguard();
    }
}