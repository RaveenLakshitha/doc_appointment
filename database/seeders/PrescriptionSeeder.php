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
        // Load templates with their related medications
        $templates = \App\Models\MedicineTemplate::with('medications')->get();

        if ($templates->isEmpty() || $doctors->isEmpty() || $patients->isEmpty()) {
            return;
        }

        foreach ($patients as $patient) {
            // Each patient gets 1-3 random prescriptions based on templates
            $numPrescriptions = rand(1, 3);
            $selectedTemplates = $templates->random($numPrescriptions);

            foreach ($selectedTemplates as $template) {
                $prescription = $patient->prescriptions()->create([
                    'doctor_id' => $doctors->random()->id,
                    'prescription_date' => \Carbon\Carbon::now()->subDays(rand(1, 60))->format('Y-m-d'),
                    'type' => 'Standard',
                    'diagnosis' => $template->name,
                    'notes' => $template->description,
                ]);

                foreach ($template->medications as $med) {
                    $prescription->medications()->create([
                        'name' => $med->name,
                        'dosage' => $med->dosage,
                        'route' => $med->route,
                        'frequency' => $med->frequency,
                        'instructions' => $med->instructions,
                        'duration_days' => 7, // Default duration if not specified
                    ]);
                }
            }
        }

        Prescription::reguard();
        PrescriptionMedication::reguard();
    }
}