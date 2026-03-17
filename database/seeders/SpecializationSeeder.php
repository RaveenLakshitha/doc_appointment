<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Specialization::unguard();

        $departments = Department::pluck('id', 'name')->toArray();

        $specializations = [
            [
                'name' => 'Psicoterapeuta clínico',
                'department_id' => $departments['Psicoterapia Clínica'] ?? null,
                'description' => 'Atención clínica para niños, adolescentes, adultos jóvenes y adultos.'
            ],
            [
                'name' => 'Psicoterapeuta clínico y de la salud',
                'department_id' => $departments['Psicoterapia Clínica'] ?? null,
                'description' => 'Enfoque en salud mental y bienestar integral.'
            ],

            [
                'name' => 'Consejería de adicciones',
                'department_id' => $departments['Adicciones'] ?? null,
                'description' => 'Apoyo y tratamiento para el manejo de dependencias y adicciones.'
            ],

            [
                'name' => 'Evaluación Psicológica y Psicométrica',
                'department_id' => $departments['Evaluación'] ?? null,
                'description' => 'Pruebas diagnósticas y medición de procesos mentales.'
            ],

            [
                'name' => 'Neurodesarrollo',
                'department_id' => $departments['Neurodesarrollo'] ?? null,
                'description' => 'Especialidad en TCC, terapia de lenguaje y desarrollo neurológico.'
            ],

            [
                'name' => 'Tanatología',
                'department_id' => $departments['Tanatología'] ?? null,
                'description' => 'Acompañamiento en procesos de duelo y pérdida.'
            ],

            [
                'name' => 'Nutrición Clínica',
                'department_id' => $departments['Nutrición'] ?? null,
                'description' => 'Especialidad en diabetes y trastornos de la conducta alimentaria.'
            ],
            [
                'name' => 'Psiquiatría de Enlace y Medicina Psicosomática',
                'department_id' => $departments['Psiquiatría'] ?? null,
                'description' => 'Atención psiquiátrica integrada a condiciones médicas.'
            ],
        ];

        foreach ($specializations as $specialization) {
            Specialization::create($specialization);
        }

        Specialization::reguard();
    }
}