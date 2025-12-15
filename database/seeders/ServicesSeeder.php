<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Equipment;
use App\Models\Doctor;
use App\Models\Department;
use App\Models\ServiceAvailabilitySlot;
use Carbon\Carbon;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch or create required departments
        $radiology = Department::firstOrCreate(
            ['name' => 'Radiology'],
            [
                'location'    => 'Building A - 2nd Floor',
                'email'       => 'radiology@hospital.com',
                'phone'       => '+1234567010',
                'status'      => true,
                'description' => 'Diagnostic Imaging Department',
            ]
        );

        $cardiology = Department::where('name', 'Cardiology')->first();

        $laboratory = Department::firstOrCreate(
            ['name' => 'Laboratory'],
            [
                'location'    => 'Building A - Basement',
                'email'       => 'lab@hospital.com',
                'phone'       => '+1234567011',
                'status'      => true,
                'description' => 'Clinical Laboratory Services',
            ]
        );

        $physicalTherapy = Department::firstOrCreate(
            ['name' => 'Physical Therapy'],
            [
                'location'    => 'Building C - Ground Floor',
                'email'       => 'pt@hospital.com',
                'phone'       => '+1234567012',
                'status'      => true,
                'description' => 'Rehabilitation and Physical Therapy',
            ]
        );

        // Create Equipment
        $equipment = [
            ['name' => 'Siemens MAGNETOM Vida 3T', 'status' => 'Operational', 'last_maintenance' => '2025-03-15'],
            ['name' => 'GE Revolution CT Scanner', 'status' => 'Operational', 'last_maintenance' => '2025-02-20'],
            ['name' => 'Philips PageWriter TC70 ECG', 'status' => 'Operational', 'last_maintenance' => '2025-04-10'],
            ['name' => 'Sysmex XN-1000 Hematology Analyzer', 'status' => 'Operational', 'last_maintenance' => '2025-05-01'],
            ['name' => 'Ultrasound Machine - GE Voluson E10', 'status' => 'Operational', 'last_maintenance' => '2025-01-10'],
            ['name' => 'X-Ray Machine - Carestream DRX', 'status' => 'Operational', 'last_maintenance' => '2025-06-05'],
        ];

        $equipmentModels = [];
        foreach ($equipment as $item) {
            $equipmentModels[] = Equipment::create($item);
        }

        // Create Services
        $services = [
            [
                'name'                 => 'MRI Scan',
                'department_id'        => $radiology->id,
                'type'                 => 'Diagnostic',
                'duration_minutes'     => 45,
                'price'                => 850.00,
                'description'          => 'Magnetic Resonance Imaging (MRI) is a non-invasive imaging technology that produces three-dimensional detailed anatomical images.',
                'patient_preparation'  => 'No food/drink 4-6 hours prior. Remove all metal objects. Inform staff of implants.',
                'requires_insurance'   => true,
                'requires_referral'    => true,
                'equipment'            => [$equipmentModels[0]], // Siemens MRI
            ],
            [
                'name'                 => 'CT Scan',
                'department_id'        => $radiology->id,
                'type'                 => 'Diagnostic',
                'duration_minutes'     => 30,
                'price'                => 650.00,
                'description'          => 'Computed Tomography using X-rays to create cross-sectional images.',
                'patient_preparation'  => 'May require contrast dye. Remove metal objects.',
                'requires_insurance'   => true,
                'requires_referral'    => true,
                'equipment'            => [$equipmentModels[1]], // GE CT
            ],
            [
                'name'                 => 'Electrocardiogram (ECG)',
                'department_id'        => $cardiology->id,
                'type'                 => 'Diagnostic',
                'duration_minutes'     => 15,
                'price'                => 120.00,
                'description'          => 'Records electrical activity of the heart to detect abnormalities.',
                'patient_preparation'  => 'Wear loose clothing. Avoid lotions on chest.',
                'requires_insurance'   => false,
                'requires_referral'    => false,
                'equipment'            => [$equipmentModels[2]], // Philips ECG
            ],
            [
                'name'                 => 'Complete Blood Count (CBC)',
                'department_id'        => $laboratory->id,
                'type'                 => 'Diagnostic',
                'duration_minutes'     => 10,
                'price'                => 85.00,
                'description'          => 'Comprehensive blood test evaluating red cells, white cells, and platelets.',
                'patient_preparation'  => 'Fasting preferred but not required.',
                'requires_insurance'   => false,
                'requires_referral'    => false,
                'equipment'            => [$equipmentModels[3]], // Sysmex Analyzer
            ],
            [
                'name'                 => 'Ultrasound Scan',
                'department_id'        => $radiology->id,
                'type'                 => 'Diagnostic',
                'duration_minutes'     => 30,
                'price'                => 300.00,
                'description'          => 'Uses sound waves to produce images of internal organs and tissues.',
                'patient_preparation'  => 'May require full bladder for pelvic ultrasound.',
                'requires_insurance'   => true,
                'requires_referral'    => false,
                'equipment'            => [$equipmentModels[4]], // GE Voluson
            ],
            [
                'name'                 => 'Physical Therapy Session',
                'department_id'        => $physicalTherapy->id,
                'type'                 => 'Therapeutic',
                'duration_minutes'     => 60,
                'price'                => 150.00,
                'description'          => 'Individual rehabilitation session with licensed physical therapist.',
                'patient_preparation'  => 'Wear comfortable, loose clothing suitable for movement.',
                'requires_insurance'   => true,
                'requires_referral'    => true,
                'equipment'            => [],
            ],
            [
                'name'                 => 'X-Ray',
                'department_id'        => $radiology->id,
                'type'                 => 'Diagnostic',
                'duration_minutes'     => 15,
                'price'                => 150.00,
                'description'          => 'Digital X-ray imaging for bones and chest.',
                'patient_preparation'  => 'Remove jewelry and clothing from area being imaged.',
                'requires_insurance'   => false,
                'requires_referral'    => false,
                'equipment'            => [$equipmentModels[5]], // Carestream X-Ray
            ],
        ];

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $slots = [
            ['start' => '09:00:00', 'end' => '12:00:00'],
            ['start' => '14:00:00', 'end' => '17:00:00'],
        ];

        foreach ($services as $data) {
            $service = Service::create([
                'name'                => $data['name'],
                'department_id'       => $data['department_id'],
                'type'                => $data['type'],
                'duration_minutes'    => $data['duration_minutes'],
                'price'               => $data['price'],
                'description'         => $data['description'],
                'patient_preparation' => $data['patient_preparation'],
                'requires_insurance'  => $data['requires_insurance'],
                'requires_referral'   => $data['requires_referral'],
                'is_active'           => true,
            ]);

            // Attach equipment
            if (!empty($data['equipment'])) {
                $service->equipment()->attach(collect($data['equipment'])->pluck('id'));
            }

            // Assign 1–3 random active doctors from the same department
            $doctors = Doctor::where('department_id', $data['department_id'])
                ->where('is_active', true)
                ->inRandomOrder()
                ->limit(rand(1, 3))
                ->get();

            if ($doctors->isNotEmpty()) {
                $service->doctors()->attach($doctors->pluck('id'));
            }

            // Add availability slots (Mon–Fri, morning + afternoon)
            foreach ($days as $day) {
                foreach ($slots as $slot) {
                    ServiceAvailabilitySlot::create([
                        'service_id'  => $service->id,
                        'day_of_week' => $day,
                        'start_time'  => $slot['start'],
                        'end_time'    => $slot['end'],
                    ]);
                }
            }
        }
    }
}