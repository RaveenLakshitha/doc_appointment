<?php

namespace Database\Seeders;

use App\Models\AgeGroup;
use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorAgeGroupAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = Doctor::all();
        $ageGroups = AgeGroup::all();

        if ($ageGroups->isEmpty()) {
            $this->command->warn('No age groups found. Please run AgeGroupSeeder first.');
            return;
        }

        foreach ($doctors as $doctor) {
            // Randomly pick 1 to 3 age groups for each doctor
            $randomGroups = $ageGroups->random(rand(1, min(3, $ageGroups->count())))->pluck('id');
            $doctor->ageGroups()->sync($randomGroups);
        }

        $this->command->info('Age groups assigned to ' . $doctors->count() . ' doctors.');
    }
}
