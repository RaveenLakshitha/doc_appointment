<?php

namespace Database\Seeders;

use App\Models\Prescription;
use App\Models\PrescriptionMedication;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Database\Seeder;

class PrescriptionSeeder extends Seeder
{
    public function run(): void
    {
        Prescription::unguard();
        PrescriptionMedication::unguard();

        $patients = Patient::active()->inRandomOrder()->take(20)->get();
        $doctors = Doctor::active()->get();

        $prescriptionData = [
            [
                'type' => 'Standard',
                'diagnosis' => 'Essential Hypertension',
                'notes' => 'Patient advised lifestyle modification. Follow up in 1 month.',
                'date' => '2025-11-20',
                'medications' => [
                    ['name' => 'Amlodipine', 'dosage' => '5 mg', 'route' => 'Oral', 'frequency' => 'Once daily', 'instructions' => 'Morning dose', 'duration_days' => 30],
                    ['name' => 'Losartan', 'dosage' => '50 mg', 'route' => 'Oral', 'frequency' => 'Once daily', 'instructions' => null, 'duration_days' => 30],
                ],
            ],
            [
                'type' => 'Standard',
                'diagnosis' => 'Type 2 Diabetes Mellitus',
                'notes' => 'Diet and exercise counseling done. HbA1c to be checked in 3 months.',
                'date' => '2025-11-18',
                'medications' => [
                    ['name' => 'Metformin', 'dosage' => '500 mg', 'route' => 'Oral', 'frequency' => 'Twice daily with meals', 'instructions' => 'Increase to 1000 mg if tolerated', 'duration_days' => 90],
                ],
            ],
            [
                'type' => 'Follow-up',
                'diagnosis' => 'Asthma - Moderate Persistent',
                'notes' => 'Good compliance reported. Peak flow improved.',
                'date' => '2025-12-01',
                'medications' => [
                    ['name' => 'Budesonide/Formoterol', 'dosage' => '160/4.5 mcg', 'route' => 'Inhalation', 'frequency' => '1 puff twice daily', 'instructions' => 'Rinse mouth after use', 'duration_days' => 30],
                ],
            ],
            [
                'type' => 'Standard',
                'diagnosis' => 'Hypercholesterolemia',
                'notes' => 'LDL 160 mg/dL. High-intensity statin initiated.',
                'date' => '2025-11-25',
                'medications' => [
                    ['name' => 'Atorvastatin', 'dosage' => '40 mg', 'route' => 'Oral', 'frequency' => 'Once daily at night', 'instructions' => null, 'duration_days' => 90],
                ],
            ],
            [
                'type' => 'Chronic',
                'diagnosis' => 'Hypothyroidism',
                'notes' => 'TSH normalized on current dose.',
                'date' => '2025-12-10',
                'medications' => [
                    ['name' => 'Levothyroxine', 'dosage' => '125 mcg', 'route' => 'Oral', 'frequency' => 'Once daily on empty stomach', 'instructions' => 'Take 30 min before food', 'duration_days' => 90],
                ],
            ],
        ];

        foreach ($patients as $patient) {
            // Each patient gets 1-3 random prescriptions
            $numPrescriptions = rand(1, 3);
            $selectedData = collect($prescriptionData)->random($numPrescriptions)->all();

            foreach ($selectedData as $data) {
                $medications = $data['medications'];
                unset($data['medications']);

                $prescription = $patient->prescriptions()->create([
                    'doctor_id' => $doctors->random()->id,
                    'prescription_date' => $data['date'],
                    'type' => $data['type'],
                    'diagnosis' => $data['diagnosis'],
                    'notes' => $data['notes'],
                ]);

                foreach ($medications as $med) {
                    $prescription->medications()->create($med);
                }
            }
        }

        Prescription::reguard();
        PrescriptionMedication::reguard();
    }
}