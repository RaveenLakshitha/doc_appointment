<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OptionList;

class DropdownSeeder extends Seeder
{
    public function run(): void
    {
        // Doctor Positions
        $positions = [
            'Attending Physician',
            'Resident Physician',
            'Chief Resident',
            'Fellow',
            'Consultant',
            'Specialist',
            'Surgeon',
            'Medical Director',
            'Head of Department',
            'General Practitioner',
            'Other',
        ];

        foreach ($positions as $i => $name) {
            OptionList::updateOrCreate(
                ['type' => 'doctor_position', 'name' => $name],
                [
                    'order'  => $i + 1,
                    'status' => true,
                ]
            );
        }

        // Languages
        $languages = [
            ['name' => 'English', 'slug' => 'en', 'order' => 1],
            ['name' => 'Spanish', 'slug' => 'es', 'order' => 2],
        ];

        foreach ($languages as $lang) {
            OptionList::updateOrCreate(
                ['type' => 'language', 'name' => $lang['name']],
                [
                    'slug'   => $lang['slug'],
                    'order'  => $lang['order'],
                    'status' => true,
                ]
            );
        }

        // Gender options
        $genders = [
            ['name' => 'Male',   'slug' => 'male',   'order' => 1],
            ['name' => 'Female', 'slug' => 'female', 'order' => 2],
            ['name' => 'Other',  'slug' => 'other',  'order' => 3],
        ];

        foreach ($genders as $gender) {
            OptionList::updateOrCreate(
                ['type' => 'gender', 'name' => $gender['name']],
                [
                    'slug'   => $gender['slug'],
                    'order'  => $gender['order'],
                    'status' => true,
                ]
            );
        }

        // Patient Status
        $patientStatuses = [
            ['name' => 'Active',     'order' => 1],
            ['name' => 'Inactive',   'order' => 2],
            ['name' => 'Discharged', 'order' => 3],
            ['name' => 'Deceased',   'order' => 4],
        ];

        foreach ($patientStatuses as $status) {
            OptionList::updateOrCreate(
                ['type' => 'patient_status', 'name' => $status['name']],
                [
                    'order'  => $status['order'],
                    'status' => true,
                ]
            );
        }

        // ──────────────────────────────────────────────
        // NEW: Room Facilities
        // ──────────────────────────────────────────────
        $facilities = [
            ['name' => 'WiFi',                  'slug' => 'wifi',                  'order' => 1],
            ['name' => 'Air Conditioning',      'slug' => 'air_conditioning',      'order' => 2],
            ['name' => 'Television',            'slug' => 'television',            'order' => 3],
            ['name' => 'Telephone',             'slug' => 'telephone',             'order' => 4],
            ['name' => 'Wheelchair Accessible', 'slug' => 'wheelchair_accessible', 'order' => 5],
            ['name' => 'Attached Bathroom',     'slug' => 'attached_bathroom',     'order' => 6],
            ['name' => 'Oxygen Supply',         'slug' => 'oxygen_supply',         'order' => 7],
            ['name' => 'Nurse Call Button',     'slug' => 'nurse_call_button',     'order' => 8],
        ];

        foreach ($facilities as $i => $facility) {
            OptionList::updateOrCreate(
                ['type' => 'room_facility', 'slug' => $facility['slug']],
                [
                    'name'   => $facility['name'],
                    'order'  => $facility['order'],
                    'status' => true,
                ]
            );
        }

        // Medication Routes of Administration
        $routes = [
            ['name' => 'Oral',              'slug' => 'oral',              'order' => 1],
            ['name' => 'Intravenous (IV)',  'slug' => 'iv',                'order' => 2],
            ['name' => 'Intramuscular (IM)', 'slug' => 'im',               'order' => 3],
            ['name' => 'Subcutaneous',      'slug' => 'subcutaneous',      'order' => 4],
            ['name' => 'Topical',           'slug' => 'topical',           'order' => 5],
            ['name' => 'Sublingual',        'slug' => 'sublingual',        'order' => 6],
            ['name' => 'Buccal',            'slug' => 'buccal',            'order' => 7],
            ['name' => 'Inhalation',        'slug' => 'inhalation',        'order' => 8],
            ['name' => 'Nasal',             'slug' => 'nasal',             'order' => 9],
            ['name' => 'Ophthalmic',        'slug' => 'ophthalmic',        'order' => 10],
            ['name' => 'Otic',              'slug' => 'otic',              'order' => 11],
            ['name' => 'Rectal',            'slug' => 'rectal',            'order' => 12],
            ['name' => 'Vaginal',           'slug' => 'vaginal',           'order' => 13],
            ['name' => 'Transdermal',       'slug' => 'transdermal',       'order' => 14],
            ['name' => 'Other',             'slug' => 'other',             'order' => 99],
        ];

        foreach ($routes as $route) {
            OptionList::updateOrCreate(
                ['type' => 'medication_route', 'slug' => $route['slug']],
                [
                    'name'   => $route['name'],
                    'order'  => $route['order'],
                    'status' => true,
                ]
            );
        }
    }
}