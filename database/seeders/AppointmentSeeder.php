<?php
// database/seeders/AppointmentSeeder.php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;   // <-- Add this

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // Create Faker instance with Sri Lankan locale
        $faker = Faker::create('si_LK');

        $patients = Patient::active()->inRandomOrder()->take(25)->get();
        $doctors  = Doctor::active()->get();

        if ($patients->isEmpty() || $doctors->isEmpty()) {
            $this->command->info('Warning: No patients or doctors found. Run PatientSeeder and DoctorSeeder first!');
            return;
        }

        $statuses = ['scheduled', 'tentative', 'waitlist', 'completed', 'cancelled', 'no_show'];
        $types    = ['consultation', 'follow_up', 'procedure', 'checkup'];

        foreach ($patients as $patient) {
            $doctor = $doctors->random();

            // Create 1–4 appointments per patient
            for ($i = 0; $i < mt_rand(1, 4); $i++) {
                $date   = Carbon::today()->addDays(mt_rand(-30, 60));
                $hour   = mt_rand(8, 17);
                $minute = [0, 30][mt_rand(0, 1)];

                $start = $date->copy()->setTime($hour, $minute);

                // Avoid double booking
                $conflict = Appointment::where('doctor_id', $doctor->id)
                    ->where('appointment_datetime', '>=', $start)
                    ->where('appointment_datetime', '<', $start->copy()->addMinutes(90))
                    ->exists();

                if ($conflict) continue;

                Appointment::create([
                    'patient_id'          => $patient->id,
                    'doctor_id'           => $doctor->id,
                    'appointment_datetime'=> $start,
                    'duration_minutes'    => [30, 45, 60, 90][mt_rand(0, 3)],
                    'status'              => $statuses[array_rand($statuses)],
                    'appointment_type'    => $types[array_rand($types)],
                    'reason_for_visit'    => $faker->sentence(6),
                    'notes'               => mt_rand(0, 4) == 0 ? $faker->paragraph(2) : null,
                ]);
            }
        }

        // Add guaranteed appointments for TODAY & TOMORROW (great for testing)
        $this->createGuaranteedAppointments($doctors, $patients, $faker);
    }

    private function createGuaranteedAppointments($doctors, $patients, $faker)
    {
        $today    = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        foreach ([$today, $tomorrow] as $day) {
            foreach ($doctors as $doctor) {
                $time = $day->copy()->addHours(mt_rand(9, 15))->addMinutes([0, 30][mt_rand(0, 1)]);

                Appointment::create([
                    'patient_id'          => $patients->random()->id,
                    'doctor_id'           => $doctor->id,
                    'appointment_datetime'=> $time,
                    'duration_minutes'    => 60,
                    'status'              => 'scheduled',
                    'appointment_type'    => 'consultation',
                    'reason_for_visit'    => 'Routine check-up / Follow-up consultation',
                    'notes'               => 'Patient requested ' . ($day->isToday() ? 'today' : 'tomorrow') . ' slot.',
                ]);
            }
        }
    }
}