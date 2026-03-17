<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::unguard();

        $categories = [
            // Main clinical / medicine categories
            [
                'name' => 'Analgesics',
                'description' => 'Pain relievers and antipyretics',
                'is_active' => true,
            ],
            [
                'name' => 'Antibiotics',
                'description' => 'Antibacterial medications',
                'is_active' => true,
            ],
            [
                'name' => 'Anti-ulcer Agents',
                'description' => 'Proton pump inhibitors, H2 blockers, antacids',
                'is_active' => true,
            ],
            [
                'name' => 'Respiratory',
                'description' => 'Medications for asthma, COPD, and respiratory conditions',
                'is_active' => true,
            ],

            // Supportive / fallback categories used in inventory
            [
                'name' => 'Injectables',
                'description' => 'IV and IM injectable medications',
                'is_active' => true,
            ],
            [
                'name' => 'Inhalers & Nebulizers',
                'description' => 'Inhalation devices and respiratory medications',
                'is_active' => true,
            ],
            [
                'name' => 'Antiemetics',
                'description' => 'Medications for nausea and vomiting',
                'is_active' => true,
            ],

            // Optional parent / broader categories (if you use hierarchical structure)
            [
                'name' => 'General Medicine',
                'description' => 'Common outpatient and ward medications',
                'is_active' => true,
            ],
            [
                'name' => 'Gastrointestinal',
                'description' => 'Medications for GI disorders',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            // Use firstOrCreate to avoid duplicates if re-run
            Category::firstOrCreate(
                ['name' => $cat['name']],
                $cat
            );
        }

        Category::reguard();

        $this->command->info('Core medicine categories seeded successfully (' . count($categories) . ' categories).');
    }
}