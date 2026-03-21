<?php

namespace Database\Seeders;

use App\Models\Treatment;
use Illuminate\Database\Seeder;

class TreatmentSeeder extends Seeder
{
    public function run(): void
    {
        $treatments = [
            ['name' => 'General Consultation', 'code' => 'TRT-GEN-CONSULT'],
            ['name' => 'Follow-up Consultation', 'code' => 'TRT-FOLLOW-UP'],
            ['name' => 'Wound Dressing & Care', 'code' => 'TRT-WOUND-CARE'],
            ['name' => 'Stitch Removal', 'code' => 'TRT-STITCH-REMOVAL'],
            ['name' => 'Vaccination / Immunization', 'code' => 'TRT-VACCINE'],
            ['name' => 'Blood Test (CBC)', 'code' => 'TRT-BLOOD-TEST'],
            ['name' => 'Physical Therapy Session', 'code' => 'TRT-PHYS-THERAPY'],
            ['name' => 'Minor Surgery', 'code' => 'TRT-MINOR-SURG'],
            ['name' => 'Dental Extraction', 'code' => 'TRT-DENT-EXTRACT'],
            ['name' => 'X-Ray Scan', 'code' => 'TRT-XRAY'],
            ['name' => 'Ultrasound Scan', 'code' => 'TRT-ULTRASOUND'],
        ];

        foreach ($treatments as $trt) {
            Treatment::firstOrCreate(
                ['code' => $trt['code']],
                ['name' => $trt['name'], 'active' => true]
            );
        }
    }
}
