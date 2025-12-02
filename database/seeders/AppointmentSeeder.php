<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('si_LK');

        $patients = Patient::active()->inRandomOrder()->take(30)->get();
        $doctors  = Doctor::active()->get();

        if ($patients->isEmpty() || $doctors->isEmpty()) {
            $this->command->info('No active patients or doctors found. Run PatientSeeder and DoctorSeeder first!');
            return;
        }

        $statuses = ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show', 'rescheduled'];
        $types    = ['consultation', 'follow_up', 'procedure', 'checkup', 'emergency'];

        foreach ($patients as $patient) {
            $appointmentsCreated = 0;
            $attempts = 0;

            while ($appointmentsCreated < mt_rand(1, 4) && $attempts < 20) {
                $doctor = $doctors->random();
                $date   = Carbon::today()->addDays(mt_rand(-30, 60));
                $hour   = mt_rand(8, 17);
                $minute = in_array($hour, [12, 13]) ? 0 : [0, 30][mt_rand(0, 1)]; // Avoid lunch conflicts
                $start  = $date->copy()->setTime($hour, $minute);

                $duration = [30, 45, 60, 90][array_rand([30, 45, 60, 90])];
                $end = $start->copy()->addMinutes($duration);

                $conflict = Appointment::where('doctor_id', $doctor->id)
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('appointment_datetime', [$start, $end])
                          ->orWhereBetween(\DB::raw('DATE_ADD(appointment_datetime, INTERVAL duration_minutes MINUTE)'), [$start, $end])
                          ->orWhereRaw('? BETWEEN appointment_datetime AND DATE_ADD(appointment_datetime, INTERVAL duration_minutes MINUTE)', [$start]);
                    })
                    ->exists();

                if ($conflict) {
                    $attempts++;
                    continue;
                }

                Appointment::create([
                    'patient_id'           => $patient->id,
                    'doctor_id'            => $doctor->id,
                    'appointment_datetime' => $start,
                    'duration_minutes'     => $duration,
                    'status'               => $statuses[array_rand($statuses)],
                    'appointment_type'     => $types[array_rand($types)],
                    'reason_for_visit'     => $faker->realText(80),
                    'doctor_notes'         => mt_rand(0, 3) == 0 ? $faker->paragraph(2) : null,
                    'patient_notes'        => mt_rand(0, 4) == 0 ? $faker->paragraph() : null,
                    'admin_notes'          => mt_rand(0, 10) == 0 ? 'Insurance verified / VIP patient' : null,
                ]);

                $appointmentsCreated++;
            }
        }

        // Guaranteed appointments for today & tomorrow (perfect for testing calendar)
        $this->createGuaranteedAppointments($doctors, $patients);
    }

    private function createGuaranteedAppointments($doctors, $patients)
    {
        $today    = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $slots = [
            ['hour' => 9,  'minute' => 0],
            ['hour' => 10, 'minute' => 30],
            ['hour' => 14, 'minute' => 0],
            ['hour' => 15, 'minute' => 30],
        ];

        foreach ([$today, $tomorrow] as $day) {
            foreach ($doctors->take(5) as $doctor) {
                $slot = $slots[array_rand($slots)];
                $time = $day->copy()->setHour($slot['hour'])->setMinute($slot['minute']);

                if (Appointment::where('doctor_id', $doctor->id)
                    ->where('appointment_datetime', $time)
                    ->exists()) {
                    continue;
                }

                Appointment::create([
                    'patient_id'           => $patients->random()->id,
                    'doctor_id'            => $doctor->id,
                    'appointment_datetime' => $time,
                    'duration_minutes'     => 45,
                    'status'               => 'confirmed',
                    'appointment_type'     => 'consultation',
                    'reason_for_visit'     => 'Follow-up visit - ' . ($day->isToday() ? 'Today' : 'Tomorrow'),
                    'doctor_notes'         => 'Patient stable, review blood reports',
                    'patient_notes'        => 'Please be on time',
                ]);
            }
        }
    }
}