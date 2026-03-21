<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\DoctorScheduleDay;
use App\Models\Room;
use Illuminate\Database\Seeder;

class DoctorScheduleSeeder extends Seeder
{
    public function run(): void
    {
        DoctorSchedule::unguard();

        $doctors = Doctor::active()->get();
        $rooms   = Room::active()->get();

        if ($doctors->isEmpty() || $rooms->isEmpty()) {
            return;
        }

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        foreach ($doctors as $doctor) {
            // Find rooms for this doctor's department or any generic room if not found
            $deptRooms = $rooms->where('department_id', $doctor->department_id);
            if ($deptRooms->isEmpty()) {
                $deptRooms = $rooms; // Fallback to any room
            }

            // Give each doctor 2 to 4 schedules
            $numSchedules = rand(2, 4);

            for ($i = 0; $i < $numSchedules; $i++) {
                $room = $deptRooms->random();
                $isMorning = (bool)rand(0, 1);
                $start = $isMorning ? '08:00:00' : '13:00:00';
                $end   = $isMorning ? '12:00:00' : '17:00:00';

                $schedule = DoctorSchedule::create([
                    'doctor_id'   => $doctor->id,
                    'room_id'     => $room->id,
                    'start_time'  => $start,
                    'end_time'    => $end,
                    'is_active'   => true,
                ]);

                // Assign 1 to 3 random days
                $assignedDays = collect($days)->random(rand(1, 3));
                foreach ($assignedDays as $day) {
                    DoctorScheduleDay::create([
                        'doctor_schedule_id' => $schedule->id,
                        'day_of_week'        => $day,
                    ]);
                }
            }
        }

        DoctorSchedule::reguard();
    }
}