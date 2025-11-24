<?php
// database/seeders/SubcategorySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Medical Supplies
            'Medical Supplies' => ['Dressings', 'Bandages', 'Syringes', 'Needles', 'IV Sets', 'Catheters'],
            // Pharmaceuticals
            'Pharmaceuticals'  => ['Antibiotics', 'Analgesics', 'Antihypertensives', 'Vitamins', 'Injectables'],
            // Equipment
            'Equipment'        => ['Diagnostic', 'Monitoring', 'Therapeutic', 'Furniture'],
            // PPE
            'PPE'              => ['Gloves', 'Masks', 'Gowns', 'Face Shields', 'Caps'],
            // Laboratory
            'Laboratory'       => ['Reagents', 'Test Kits', 'Glassware'],
            // Surgical
            'Surgical'         => ['Instruments', 'Sutures', 'Drapes'],
        ];

        foreach ($data as $catName => $subs) {
            $category = Category::where('name', $catName)->first();
            if (!$category) continue;

            $records = array_map(fn($name) => [
                'category_id' => $category->id,
                'name' => $name,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ], $subs);

            Subcategory::upsert(
                $records,
                ['category_id', 'name'],
                ['is_active']
            );
        }
    }
}