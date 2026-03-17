<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\DoctorScheduleDay;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // Set Carbon to current date: January 02, 2026
        Carbon::setTestNow(Carbon::parse('2026-01-02'));

        $patients = Patient::where('is_active', true)->get();
        $doctors = Doctor::where('is_active', true)->get();

        $statuses = [
            Appointment::STATUS_PENDING => 30,
            Appointment::STATUS_APPROVED => 50,
            Appointment::STATUS_CANCELLED => 12,
            Appointment::STATUS_REJECTED => 8,
        ];

        $types = [
            Appointment::TYPE_SPECIFIC,
            Appointment::TYPE_ANY,
        ];

        foreach ($patients as $patient) {
            $appointmentCount = fake()->numberBetween(1, 6);

            for ($i = 0; $i < $appointmentCount; $i++) {
                $appointmentType = fake()->randomElement($types);

                $doctor = null;
                $room = null;
                $scheduledStart = null;
                $scheduledEnd = null;

                if (in_array($appointmentType, [Appointment::TYPE_SPECIFIC])) {
                    $doctor = $doctors->random();
                }

                // Generate random date between 3 months ago and 3 months from now (around Jan 2026)
                $appointmentDate = fake()->dateTimeBetween('-3 months', '+3 months');
                $dayName = strtolower($appointmentDate->format('l')); // e.g., 'monday'

                if ($doctor) {
                    // Find a valid active schedule for this doctor on this day
                    $schedule = DoctorSchedule::where('doctor_id', $doctor->id)
                        ->where('is_active', true)
                        ->whereHas('days', fn($q) => $q->where('day_of_week', $dayName))
                        ->inRandomOrder()
                        ->first();

                    if ($schedule) {
                        $room = $schedule->room;

                        // Parse start/end times correctly
                        $startHour = $schedule->start_time->hour;
                        $startMinute = $schedule->start_time->minute;
                        $endHour = $schedule->end_time->hour;
                        $endMinute = $schedule->end_time->minute;

                        // Generate possible 30-minute slots
                        $possibleSlots = [];
                        $current = Carbon::createFromTime($startHour, $startMinute, 0);

                        $endTime = Carbon::createFromTime($endHour, $endMinute, 0);
                        if ($endTime->lt($current)) {
                            $endTime->addDay(); // Handle overnight (rare)
                        }

                        while ($current->copy()->addMinutes(30)->lte($endTime)) {
                            $possibleSlots[] = $current->copy();
                            $current->addMinutes(30);
                        }

                        if (!empty($possibleSlots)) {
                            $slotStart = fake()->randomElement($possibleSlots);
                            $scheduledStart = Carbon::parse($appointmentDate->format('Y-m-d') . ' ' . $slotStart->format('H:i:s'));
                            $scheduledEnd = $scheduledStart->copy()->addMinutes(30);
                        }
                    }
                }

                // Fallback for TYPE_ANY or if no schedule found
                if (!$scheduledStart) {
                    $fallbackHour = fake()->numberBetween(9, 16);
                    $fallbackMinute = fake()->randomElement([0, 30]);

                    $scheduledStart = Carbon::parse($appointmentDate->format('Y-m-d'))
                        ->setTime($fallbackHour, $fallbackMinute);

                    $scheduledEnd = $scheduledStart->copy()->addMinutes(30);
                    $room = Room::inRandomOrder()->first();
                }

                // Weighted random status
                $status = fake()->randomElement(array_keys($statuses));

                Appointment::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor?->id,
                    'room_id' => $room?->id,
                    'scheduled_start' => $scheduledStart,
                    'scheduled_end' => $scheduledEnd,
                    'appointment_type' => $appointmentType,
                    'status' => $status,
                    'reason_for_visit' => fake()->randomElement([
                        'Routine check-up',
                        'Follow-up consultation',
                        'Chest pain evaluation',
                        'Joint pain assessment',
                        'Pediatric vaccination',
                        'Post-surgery review',
                        'Blood pressure check',
                        'Diabetes management',
                        'Headache evaluation',
                        'Cancer treatment follow-up',
                    ]),
                    'patient_notes' => fake()->optional(0.6)->paragraph(1, 3),
                    'doctor_notes' => in_array($status, [Appointment::STATUS_APPROVED, Appointment::STATUS_PENDING])
                        ? fake()->optional(0.7)->paragraph(1, 2)
                        : null,
                    'admin_notes' => fake()->optional(0.3)->sentence,
                    'cancelled_at' => $status === Appointment::STATUS_CANCELLED
                        ? fake()->dateTimeBetween('-2 months', 'now')
                        : null,
                    'cancelled_by' => $status === Appointment::STATUS_CANCELLED ? null : null,
                ]);
            }
        }

        // Clear test now
        Carbon::setTestNow();
    }
}