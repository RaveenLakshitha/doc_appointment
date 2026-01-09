<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\User;
use App\Models\AppointmentRequest;
use Carbon\Carbon;

class AppointmentRequestSeeder extends Seeder
{
    public function run(): void
    {
        // Load existing data
        $patients        = Patient::where('is_active', true)->get();
        $doctors         = Doctor::where('is_active', true)->get();
        $specializations = Specialization::all();

        if ($patients->isEmpty()) {
            $this->command->info('No active patients found. Run PatientSeeder first!');
            return;
        }
        if ($doctors->isEmpty()) {
            $this->command->info('No active doctors found. Run DoctorSeeder first!');
            return;
        }
        if ($specializations->isEmpty()) {
            $this->command->info('No specializations found. Run SpecializationSeeder first!');
            return;
        }

        // Patients who have a primary care provider (for primary_provider mode)
        $patientsWithPCP = $patients->whereNotNull('primary_care_provider_id');

        $modes = [
            AppointmentRequest::DOCTOR_SELECTION_SPECIFIC,
            AppointmentRequest::DOCTOR_SELECTION_ANY,
            AppointmentRequest::DOCTOR_SELECTION_PRIMARY_PROVIDER,
        ];

        $reasons = [
            'Annual physical examination',
            'Follow-up for hypertension',
            'Persistent cough and fatigue',
            'Knee pain and swelling',
            'Skin rash evaluation',
            'Pre-operative clearance',
            'Diabetes management review',
            'Vaccination update',
            'Migraine consultation',
            'Back pain assessment',
            'Routine blood work review',
            'Chest pain evaluation',
            'Pediatric well-child visit',
            'Newborn check-up',
            'Joint pain after sports injury',
        ];

        // Create 50 realistic appointment requests
        foreach (range(1, 50) as $i) {
            $patient        = $patients->random();
            $specialization = $specializations->random();
            $mode           = fake()->randomElement($modes);

            $request = AppointmentRequest::create([
                'patient_id'            => $patient->id,
                'specialization_id'     => $specialization->id,
                'doctor_selection_mode' => $mode,
                'reason_for_visit'      => fake()->randomElement($reasons),
                'notes'                 => fake()->optional(0.5)->paragraph(1),
                'duration_minutes'      => fake()->randomElement([30, 45, 60]),
                'status'                => fake()->randomElement(['pending', 'approved', 'rejected', 'cancelled']),
                'created_at'            => fake()->dateTimeBetween('-4 months', 'now'),
                'updated_at'            => now(),
            ]);

            // Reset mode-specific fields
            $request->doctor_id = null;
            $request->primary_care_provider_id = null;
            $request->requested_date = null;
            $request->requested_start_time = null;
            $request->preferred_time_range_start = null;
            $request->preferred_time_range_end = null;

            // Mode-specific data
            if ($mode === AppointmentRequest::DOCTOR_SELECTION_SPECIFIC) {
                $request->doctor_id = $doctors->random()->id;

                $dateObj = fake()->dateTimeBetween('now', '+60 days');
                $request->requested_date = $dateObj->format('Y-m-d');
                $request->requested_start_time = fake()->randomElement(['09:00', '10:00', '11:00', '14:00', '15:00', '16:00']);
            }
            elseif ($mode === AppointmentRequest::DOCTOR_SELECTION_ANY) {
                $dateObj = fake()->optional(0.7)->dateTimeBetween('+3 days', '+45 days');
                if ($dateObj) {
                    $request->requested_date = $dateObj->format('Y-m-d');
                    $request->requested_start_time = fake()->randomElement(['10:00', '14:00', '15:30']);
                }

                $request->preferred_time_range_start = '09:00';
                $request->preferred_time_range_end   = '17:00';
            }
            elseif ($mode === AppointmentRequest::DOCTOR_SELECTION_PRIMARY_PROVIDER) {
                if ($patientsWithPCP->isNotEmpty()) {
                    $pcpPatient = $patientsWithPCP->random();
                    $request->patient_id = $pcpPatient->id;
                    $request->primary_care_provider_id = $pcpPatient->primary_care_provider_id;
                }

                $dateObj = fake()->optional(0.4)->dateTimeBetween('+7 days', '+60 days');
                if ($dateObj) {
                    $request->requested_date = $dateObj->format('Y-m-d');
                }

                $request->requested_start_time = null;
                $request->preferred_time_range_start = fake()->randomElement(['08:00', '09:00']);
                $request->preferred_time_range_end   = fake()->randomElement(['12:00', '16:00', '17:00']);
            }

            // Approval simulation
            if ($request->status === 'approved') {
                $assignedDoctorId = $request->doctor_id ?? $doctors->random()->id;
                $request->assigned_doctor_id = $assignedDoctorId;
                $request->approved_by = User::inRandomOrder()->first()?->id;
                $request->approved_at = fake()->dateTimeBetween($request->created_at, 'now');
            }

            // Rejection simulation
            if ($request->status === 'rejected') {
                $request->rejected_reason = fake()->randomElement([
                    'No available slots in requested period',
                    'Specialist currently on leave',
                    'Additional medical history required',
                    'Patient already has recent appointment',
                    'Request outside clinic hours',
                ]);
                $request->approved_by = User::inRandomOrder()->first()?->id;
                $request->approved_at = fake()->dateTimeBetween($request->created_at, 'now');
            }

            $request->save();
        }

        $this->command->info('50 Appointment Requests seeded successfully! (Includes specific, any, and primary provider modes)');
    }
}