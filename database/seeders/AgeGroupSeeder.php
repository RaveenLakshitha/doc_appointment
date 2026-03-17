<?php

namespace Database\Seeders;

use App\Models\AgeGroup;
use Illuminate\Database\Seeder;

class AgeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AgeGroup::unguard();

        $ageGroups = [
            [
                'name' => 'Niños',
                'min_age' => 0,
                'max_age' => 12,
                'description' => 'Atención especializada para la infancia.',
                'is_active' => true,
            ],
            [
                'name' => 'Adolescentes',
                'min_age' => 13,
                'max_age' => 17,
                'description' => 'Atención para la etapa de la adolescencia.',
                'is_active' => true,
            ],
            [
                'name' => 'Adultos Jóvenes',
                'min_age' => 18,
                'max_age' => 25,
                'description' => 'Servicios enfocados en la transición a la adultez.',
                'is_active' => true,
            ],
            [
                'name' => 'Adultos',
                'min_age' => 26,
                'max_age' => 64,
                'description' => 'Atención psicoterapéutica y médica para adultos.',
                'is_active' => true,
            ],
            [
                'name' => 'Adultos Mayores',
                'min_age' => 65,
                'max_age' => 100,
                'description' => 'Especialidad en geriatría y atención para la tercera edad.',
                'is_active' => true,
            ],
            [
                'name' => 'Pareja y Familia',
                'min_age' => 0,
                'max_age' => 100,
                'description' => 'Atención grupal, de pareja o núcleo familiar.',
                'is_active' => true,
            ],
        ];

        foreach ($ageGroups as $group) {
            AgeGroup::updateOrCreate(
                ['name' => $group['name']],
                $group
            );
        }

        AgeGroup::reguard();
    }
}