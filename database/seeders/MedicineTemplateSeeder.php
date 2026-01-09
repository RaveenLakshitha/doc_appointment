<?php

namespace Database\Seeders;

use App\Models\MedicineTemplate;
use App\Models\TemplateMedication;
use Illuminate\Database\Seeder;

class MedicineTemplateSeeder extends Seeder
{
    public function run(): void
    {
        MedicineTemplate::unguard();
        TemplateMedication::unguard();

        $templates = [
            [
                'name' => 'Hypertension Standard',
                'category' => 'Cardiology',
                'description' => 'Standard treatment protocol for essential hypertension',
                'medications' => [
                    ['name' => 'Amlodipine', 'dosage' => '5 mg', 'route' => 'Oral', 'frequency' => 'Once daily', 'instructions' => 'Take in the morning'],
                    ['name' => 'Losartan', 'dosage' => '50 mg', 'route' => 'Oral', 'frequency' => 'Once daily', 'instructions' => 'Can be taken with or without food'],
                ],
            ],
            [
                'name' => 'Type 2 Diabetes Basic',
                'category' => 'Endocrinology',
                'description' => 'Initial management for newly diagnosed Type 2 Diabetes',
                'medications' => [
                    ['name' => 'Metformin', 'dosage' => '500 mg', 'route' => 'Oral', 'frequency' => 'Twice daily with meals', 'instructions' => 'Start low, increase gradually'],
                    ['name' => 'Gliclazide', 'dosage' => '80 mg', 'route' => 'Oral', 'frequency' => 'Once daily with breakfast', 'instructions' => 'Monitor for hypoglycemia'],
                ],
            ],
            [
                'name' => 'Asthma Maintenance',
                'category' => 'Pulmonology',
                'description' => 'Controller therapy for moderate persistent asthma',
                'medications' => [
                    ['name' => 'Budesonide/Formoterol', 'dosage' => '160/4.5 mcg', 'route' => 'Inhalation', 'frequency' => '1 puff twice daily', 'instructions' => 'Rinse mouth after use'],
                    ['name' => 'Montelukast', 'dosage' => '10 mg', 'route' => 'Oral', 'frequency' => 'Once daily at night', 'instructions' => 'For additional control'],
                ],
            ],
            [
                'name' => 'Hyperlipidemia Control',
                'category' => 'Cardiology',
                'description' => 'Statin therapy for primary prevention',
                'medications' => [
                    ['name' => 'Atorvastatin', 'dosage' => '20 mg', 'route' => 'Oral', 'frequency' => 'Once daily at night', 'instructions' => 'Take with or without food'],
                ],
            ],
            [
                'name' => 'Hypothyroidism Replacement',
                'category' => 'Endocrinology',
                'description' => 'Levothyroxine replacement therapy',
                'medications' => [
                    ['name' => 'Levothyroxine', 'dosage' => '100 mcg', 'route' => 'Oral', 'frequency' => 'Once daily on empty stomach', 'instructions' => 'Take 30-60 min before breakfast'],
                ],
            ],
            [
                'name' => 'GERD Management',
                'category' => 'Gastroenterology',
                'description' => 'Proton pump inhibitor therapy for GERD',
                'medications' => [
                    ['name' => 'Omeprazole', 'dosage' => '20 mg', 'route' => 'Oral', 'frequency' => 'Once daily before breakfast', 'instructions' => 'For 4-8 weeks'],
                    ['name' => 'Domperidone', 'dosage' => '10 mg', 'route' => 'Oral', 'frequency' => 'Three times daily before meals', 'instructions' => 'If nausea present'],
                ],
            ],
        ];

        foreach ($templates as $templateData) {
            $medications = $templateData['medications'];
            unset($templateData['medications']);

            $template = MedicineTemplate::create($templateData);

            foreach ($medications as $med) {
                $template->medications()->create($med);
            }
        }

        MedicineTemplate::reguard();
        TemplateMedication::reguard();
    }
}