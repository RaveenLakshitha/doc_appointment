<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Room::unguard();

        $departments = Department::pluck('id', 'name')->toArray();

        $rooms = [
            // Cardiology
            ['room_number' => 'C-301', 'name' => 'Cardiology Consultation Room 1', 'department_id' => $departments['Cardiology'], 'description' => 'General cardiology consultations', 'room_type' => 'consultation', 'floor' => '3', 'capacity' => 1, 'price_per_day' => 0, 'facilities' => ['wifi', 'air_conditioning', 'telephone'], 'is_active' => true],
            ['room_number' => 'C-302', 'name' => 'Cardiology Consultation Room 2', 'department_id' => $departments['Cardiology'], 'description' => 'Interventional follow-ups', 'room_type' => 'consultation', 'floor' => '3', 'capacity' => 1, 'price_per_day' => 0, 'facilities' => ['wifi', 'air_conditioning', 'television'], 'is_active' => true],
            ['room_number' => 'C-303', 'name' => 'ECG & Echo Room', 'department_id' => $departments['Cardiology'], 'description' => 'Diagnostic testing', 'room_type' => 'diagnostic', 'floor' => '3', 'capacity' => 2, 'price_per_day' => 0, 'facilities' => ['air_conditioning', 'wheelchair_accessible'], 'is_active' => true],
            ['room_number' => 'C-304', 'name' => 'Pediatric Cardiology Room', 'department_id' => $departments['Cardiology'], 'description' => 'Pediatric heart patients', 'room_type' => 'consultation', 'floor' => '3', 'capacity' => 1, 'price_per_day' => 0, 'facilities' => ['wifi', 'television', 'air_conditioning'], 'is_active' => true],

            // Neurology
            ['room_number' => 'N-201', 'name' => 'Neurology Consultation Room 1', 'department_id' => $departments['Neurology'], 'description' => 'General neurology', 'room_type' => 'consultation', 'floor' => '2', 'capacity' => 1, 'price_per_day' => 0, 'facilities' => ['wifi', 'air_conditioning'], 'is_active' => true],
            ['room_number' => 'N-202', 'name' => 'Neurology Consultation Room 2', 'department_id' => $departments['Neurology'], 'description' => 'Epilepsy & stroke', 'room_type' => 'consultation', 'floor' => '2', 'capacity' => 1, 'price_per_day' => 0, 'facilities' => ['wifi', 'telephone'], 'is_active' => true],
            ['room_number' => 'N-203', 'name' => 'EEG Room', 'department_id' => $departments['Neurology'], 'description' => 'Electroencephalography', 'room_type' => 'diagnostic', 'floor' => '2', 'capacity' => 2, 'price_per_day' => 0, 'facilities' => ['air_conditioning', 'wheelchair_accessible'], 'is_active' => true],

            // Orthopedics
            ['room_number' => 'O-101', 'name' => 'Ortho Consultation Room 1', 'department_id' => $departments['Orthopedics'], 'description' => 'Joint replacement', 'room_type' => 'consultation', 'floor' => '1', 'capacity' => 1, 'price_per_day' => 0, 'facilities' => ['wifi', 'air_conditioning', 'wheelchair_accessible'], 'is_active' => true],
            ['room_number' => 'O-102', 'name' => 'Ortho Consultation Room 2', 'department_id' => $departments['Orthopedics'], 'description' => 'Sports medicine', 'room_type' => 'consultation', 'floor' => '1', 'capacity' => 1, 'price_per_day' => 0, 'facilities' => ['wifi', 'television'], 'is_active' => true],
            ['room_number' => 'O-103', 'name' => 'Casting Room', 'department_id' => $departments['Orthopedics'], 'description' => 'Fracture care', 'room_type' => 'procedure', 'floor' => '1', 'capacity' => 3, 'price_per_day' => 0, 'facilities' => ['wheelchair_accessible'], 'is_active' => true],

            // Pediatrics
            ['room_number' => 'P-101', 'name' => 'General Pediatrics Room 1', 'department_id' => $departments['Pediatrics'], 'description' => 'Routine checkups', 'room_type' => 'consultation', 'floor' => 'Ground', 'capacity' => 1, 'price_per_day' => 0, 'facilities' => ['wifi', 'television', 'air_conditioning'], 'is_active' => true],
            ['room_number' => 'P-102', 'name' => 'Vaccination Room', 'department_id' => $departments['Pediatrics'], 'description' => 'Immunizations', 'room_type' => 'procedure', 'floor' => 'Ground', 'capacity' => 4, 'price_per_day' => 0, 'facilities' => ['air_conditioning'], 'is_active' => true],
            ['room_number' => 'P-103', 'name' => 'Neonatology Follow-up', 'department_id' => $departments['Pediatrics'], 'description' => 'NICU graduates', 'room_type' => 'consultation', 'floor' => 'Ground', 'capacity' => 1, 'price_per_day' => 0, 'facilities' => ['wifi', 'telephone'], 'is_active' => true],

            // Oncology
            ['room_number' => 'ONC-401', 'name' => 'Oncology Consultation 1', 'department_id' => $departments['Oncology'], 'description' => 'Medical oncology', 'room_type' => 'consultation', 'floor' => '4', 'capacity' => 1, 'price_per_day' => 0, 'facilities' => ['wifi', 'air_conditioning', 'telephone', 'television'], 'is_active' => true],
            ['room_number' => 'ONC-402', 'name' => 'Oncology Consultation 2', 'department_id' => $departments['Oncology'], 'description' => 'Clinical trials', 'room_type' => 'consultation', 'floor' => '4', 'capacity' => 1, 'price_per_day' => 0, 'facilities' => ['wifi', 'air_conditioning', 'wheelchair_accessible'], 'is_active' => true],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }

        Room::reguard();
    }
}