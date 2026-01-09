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

        $doctors = Doctor::active()->get()->keyBy('email');
        $rooms   = Room::active()->get();

        $cardiologyRooms = $rooms->where('department.name', 'Cardiology');
        $neurologyRooms  = $rooms->where('department.name', 'Neurology');
        $orthoRooms      = $rooms->where('department.name', 'Orthopedics');
        $pedsRooms       = $rooms->where('department.name', 'Pediatrics');
        $oncologyRooms   = $rooms->where('department.name', 'Oncology');

        $schedules = [
            // Ahmad Khan - Interventional Cardiologist
            [
                'doctor' => $doctors['ahmad.khan@hospital.com'],
                'room'   => $cardiologyRooms->where('room_number', 'C-301')->first(),
                'days'   => ['monday', 'wednesday', 'friday'],
                'start'  => '09:00:00',
                'end'    => '13:00:00',
            ],
            [
                'doctor' => $doctors['ahmad.khan@hospital.com'],
                'room'   => $cardiologyRooms->where('room_number', 'C-302')->first(),
                'days'   => ['tuesday', 'thursday'],
                'start'  => '14:00:00',
                'end'    => '18:00:00',
            ],

            // Sarah Williams - Pediatric Cardiologist
            [
                'doctor' => $doctors['sarah.williams@hospital.com'],
                'room'   => $cardiologyRooms->where('room_number', 'C-304')->first(),
                'days'   => ['monday', 'wednesday', 'friday'],
                'start'  => '10:00:00',
                'end'    => '15:00:00',
            ],

            // Michael Brown - Head of Neurology
            [
                'doctor' => $doctors['michael.brown@hospital.com'],
                'room'   => $neurologyRooms->where('room_number', 'N-201')->first(),
                'days'   => ['tuesday', 'thursday', 'saturday'],
                'start'  => '08:30:00',
                'end'    => '14:30:00',
            ],

            // Priya Patel - Senior Orthopedic Surgeon
            [
                'doctor' => $doctors['priya.patel@hospital.com'],
                'room'   => $orthoRooms->where('room_number', 'O-101')->first(),
                'days'   => ['monday', 'wednesday', 'friday'],
                'start'  => '09:00:00',
                'end'    => '16:00:00',
            ],

            // Omar Farooq - Neonatologist
            [
                'doctor' => $doctors['omar.farooq@hospital.com'],
                'room'   => $pedsRooms->where('room_number', 'P-103')->first(),
                'days'   => ['tuesday', 'thursday'],
                'start'  => '13:00:00',
                'end'    => '17:00:00',
            ],

            // Fatima Ahmed - Electrophysiologist
            [
                'doctor' => $doctors['fatima.ahmed@hospital.com'],
                'room'   => $cardiologyRooms->where('room_number', 'C-303')->first(),
                'days'   => ['tuesday', 'thursday'],
                'start'  => '10:00:00',
                'end'    => '14:00:00',
            ],

            // James Wilson - Epileptologist
            [
                'doctor' => $doctors['james.wilson@hospital.com'],
                'room'   => $neurologyRooms->where('room_number', 'N-203')->first(),
                'days'   => ['monday', 'wednesday'],
                'start'  => '14:00:00',
                'end'    => '18:00:00',
            ],

            // Anita Sharma - Sports Medicine
            [
                'doctor' => $doctors['anita.sharma@hospital.com'],
                'room'   => $orthoRooms->where('room_number', 'O-102')->first(),
                'days'   => ['tuesday', 'friday'],
                'start'  => '09:00:00',
                'end'    => '15:00:00',
            ],

            // Carlos Ramirez - General Pediatrician
            [
                'doctor' => $doctors['carlos.ramirez@hospital.com'],
                'room'   => $pedsRooms->where('room_number', 'P-101')->first(),
                'days'   => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'start'  => '08:00:00',
                'end'    => '16:00:00',
            ],

            // Laura Miller - Medical Oncologist
            [
                'doctor' => $doctors['laura.miller@hospital.com'],
                'room'   => $oncologyRooms->where('room_number', 'ONC-401')->first(),
                'days'   => ['monday', 'wednesday', 'thursday'],
                'start'  => '09:30:00',
                'end'    => '16:30:00',
            ],

            // Hassan Malik - Interventional Cardiologist
            [
                'doctor' => $doctors['hassan.malik@hospital.com'],
                'room'   => $cardiologyRooms->where('room_number', 'C-302')->first(),
                'days'   => ['monday', 'friday'],
                'start'  => '13:00:00',
                'end'    => '17:00:00',
            ],
        ];

        foreach ($schedules as $sched) {
            if (!$sched['doctor'] || !$sched['room']) {
                continue;
            }

            $schedule = DoctorSchedule::create([
                'doctor_id'   => $sched['doctor']->id,
                'room_id'     => $sched['room']->id,
                'start_time'  => $sched['start'],
                'end_time'    => $sched['end'],
                'is_active'   => true,
            ]);

            foreach ($sched['days'] as $day) {
                DoctorScheduleDay::create([
                    'doctor_schedule_id' => $schedule->id,
                    'day_of_week'        => $day,
                ]);
            }
        }

        DoctorSchedule::reguard();
    }
}