<?php
// database/seeders/MedicationTemplateCategorySeeder.php

namespace Database\Seeders;

use App\Models\MedicationTemplateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MedicationTemplateCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Cardiology',          'color' => 'red-600',     'order' => 10],
            ['name' => 'Endocrinology',       'color' => 'purple-600',  'order' => 20],
            ['name' => 'General Practice',    'color' => 'blue-600',    'order' => 30],
            ['name' => 'Surgery',             'color' => 'green-600',   'order' => 40],
            ['name' => 'Psychiatry',          'color' => 'indigo-600',  'order' => 50],
            ['name' => 'Pediatrics',          'color' => 'pink-600',    'order' => 60],
            ['name' => 'Obstetrics & Gynecology', 'color' => 'rose-600', 'order' => 70],
            ['name' => 'Pain Management',     'color' => 'orange-600',  'order' => 80],
            ['name' => 'Infectious Disease',  'color' => 'yellow-600',  'order' => 90],
            ['name' => 'Dermatology',         'color' => 'cyan-600',    'order' => 100],
            ['name' => 'Gastroenterology',    'color' => 'emerald-600', 'order' => 110],
            ['name' => 'Oncology',            'color' => 'violet-700',  'order' => 120],
        ];

        foreach ($categories as $cat) {
            MedicationTemplateCategory::updateOrCreate(
                ['name' => $cat['name']],
                [
                    'slug'      => Str::slug($cat['name']),
                    'color'     => $cat['color'],
                    'order'     => $cat['order'],
                    'is_active' => true,
                ]
            );
        }
    }
}