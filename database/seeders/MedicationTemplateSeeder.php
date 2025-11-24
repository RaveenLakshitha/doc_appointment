<?php
// database/seeders/MedicationTemplateSeeder.php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\MedicationTemplate;
use App\Models\MedicationTemplateCategory;
use Illuminate\Database\Seeder;

class MedicationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Get any active doctor
        $doctor = Doctor::active()->first();

        // If no doctor exists, create one
        if (!$doctor) {
            $doctor = Doctor::create([
                'first_name'         => 'John',
                'last_name'          => 'Doe',
                'email'              => 'doctor@clinic.test',
                'phone'              => '+1234567890',
                'primary_specialty'  => 'General Medicine',
                'license_number'     => 'MD123456',
                'is_active'          => true,
            ]);
        }

        $doctorId = $doctor->id;

        // Helper to get category
        $cat = fn($name) => MedicationTemplateCategory::where('name', $name)->firstOrFail();

        $templates = [
            ['name' => 'Essential Hypertension - First Line', 'category' => 'Cardiology', 'meds' => [
                ['name' => 'Amlodipine',     'dosage' => '5 mg',    'route' => 'Oral', 'frequency' => 'Once daily',     'instructions' => '', 'duration' => 'Ongoing', 'order' => 10],
                ['name' => 'Lisinopril',     'dosage' => '10 mg',   'route' => 'Oral', 'frequency' => 'Once daily',     'instructions' => 'Monitor BP & potassium', 'duration' => 'Ongoing', 'order' => 20],
            ]],
            ['name' => 'Type 2 Diabetes - Newly Diagnosed', 'category' => 'Endocrinology', 'meds' => [
                ['name' => 'Metformin',      'dosage' => '500 mg',  'route' => 'Oral', 'frequency' => 'Twice daily with meals', 'instructions' => 'Start low to reduce GI upset', 'duration' => 'Ongoing', 'order' => 10],
            ]],
            ['name' => 'Viral Upper Respiratory Infection', 'category' => 'General Practice', 'meds' => [
                ['name' => 'Paracetamol',    'dosage' => '500-1000 mg', 'route' => 'Oral', 'frequency' => 'Q6-8H PRN', 'instructions' => 'For fever/pain', 'duration' => '5-7 days', 'order' => 10],
                ['name' => 'Loratadine',     'dosage' => '10 mg',   'route' => 'Oral', 'frequency' => 'Once daily', 'instructions' => '', 'duration' => '7 days', 'order' => 20],
            ]],
            ['name' => 'Post-Laparoscopic Cholecystectomy', 'category' => 'Surgery', 'meds' => [
                ['name' => 'Paracetamol',    'dosage' => '1 g',     'route' => 'Oral', 'frequency' => 'Q6H', 'instructions' => '', 'duration' => '5 days', 'order' => 10],
                ['name' => 'Ibuprofen',      'dosage' => '400 mg',  'route' => 'Oral', 'frequency' => 'TID with food', 'instructions' => '', 'duration' => '5 days', 'order' => 20],
            ]],
            ['name' => 'Anxiety - PRN Rescue', 'category' => 'Psychiatry', 'meds' => [
                ['name' => 'Lorazepam',      'dosage' => '0.5-1 mg', 'route' => 'Oral/SL', 'frequency' => 'PRN max 2/day', 'instructions' => 'For acute anxiety', 'duration' => 'Short term', 'order' => 10],
            ]],
            ['name' => 'Uncomplicated UTI (Female)', 'category' => 'Infectious Disease', 'meds' => [
                ['name' => 'Nitrofurantoin', 'dosage' => '100 mg',  'route' => 'Oral', 'frequency' => 'Twice daily', 'instructions' => 'Take with food', 'duration' => '5 days', 'order' => 10],
            ]],
        ];

        foreach ($templates as $t) {
            MedicationTemplate::create([
                'name'        => $t['name'],
                'category_id' => $cat($t['category'])->id,
                'description' => 'Standard protocol',
                'created_by'  => $doctorId,
            ])->medications()->createMany($t['meds']);
        }
    }
}