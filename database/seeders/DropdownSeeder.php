<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OptionList;

class DropdownSeeder extends Seeder
{
    public function run(): void
{
    $positions = [
        'Attending Physician', 'Resident Physician', 'Chief Resident', 'Fellow',
        'Consultant', 'Specialist', 'Surgeon', 'Medical Director',
        'Head of Department', 'General Practitioner', 'Other'
    ];

    foreach ($positions as $i => $name) {
        OptionList::create([
            'type' => 'doctor_position',
            'name' => $name,
            'order' => $i + 1,
        ]);
    }

    // Add more types as needed
    OptionList::create(['type' => 'patient_status', 'name' => 'Active', 'order' => 1]);
    OptionList::create(['type' => 'patient_status', 'name' => 'Inactive', 'order' => 2]);
}
}
