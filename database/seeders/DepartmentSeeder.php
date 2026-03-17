<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::unguard();

        $departments = [
            [
                'name' => 'Psicoterapia Clínica',
                'description' => 'Servicios de psicoterapia clínica para niños, adolescentes y adultos.',
                'status' => true
            ],
            [
                'name' => 'Adicciones',
                'description' => 'Tratamiento y consejería para adicciones.',
                'status' => true
            ],
            [
                'name' => 'Evaluación',
                'description' => 'Evaluación Psicológica y Psicométrica.',
                'status' => true
            ],
            [
                'name' => 'Neurodesarrollo',
                'description' => 'Especialidad en neurodesarrollo.',
                'status' => true
            ],
            [
                'name' => 'Tanatología',
                'description' => 'Acompañamiento en procesos de duelo.',
                'status' => true
            ],
            [
                'name' => 'Nutrición',
                'description' => 'Nutrición Clínica.',
                'status' => true
            ],
            [
                'name' => 'Psiquiatría',
                'description' => 'Psiquiatría de enlace y Medicina Psicosomática.',
                'status' => true
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(['name' => $department['name']], $department);
        }

        Department::reguard();
    }
}