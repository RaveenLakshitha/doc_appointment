<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    public function run(): void
    {
        // Disable mass assignment protection
        InventoryItem::unguard();

        // -----------------------------------------------------------------
        // 1. Create Suppliers
        // -----------------------------------------------------------------
        Supplier::insert([
            ['name' => 'Medico Supplies Ltd.',        'contact_email' => 'sales@medico.lk',      'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PharmaCare Distributors',     'contact_email' => 'orders@pharmacare.lk', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HealthTech Solutions',        'contact_email' => 'info@healthtech.lk',   'created_at' => now(), 'updated_at' => now()],
            ['name' => 'LabChem Lanka',               'contact_email' => 'lab@labchem.lk',       'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SterileTech Pvt Ltd',         'contact_email' => 'sterile@tech.lk',      'created_at' => now(), 'updated_at' => now()],
        ]);

        $medico     = Supplier::where('name', 'Medico Supplies Ltd.')->first();
        $pharma     = Supplier::where('name', 'PharmaCare Distributors')->first();
        $healthtech = Supplier::where('name', 'HealthTech Solutions')->first();
        $labchem    = Supplier::where('name', 'LabChem Lanka')->first();
        $steriletech = Supplier::where('name', 'SterileTech Pvt Ltd')->first();

        // -----------------------------------------------------------------
        // 2. Create Units of Measure
        // -----------------------------------------------------------------
        UnitOfMeasure::insert([
            ['name' => 'Box',   'short' => 'bx', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Each',  'short' => 'ea', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pack',  'short' => 'pk', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vial',  'short' => 'vl', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Strip', 'short' => 'st', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $box   = UnitOfMeasure::where('name', 'Box')->first()->name;
        $each  = UnitOfMeasure::where('name', 'Each')->first()->name;
        $pack  = UnitOfMeasure::where('name', 'Pack')->first()->name;
        $vial  = UnitOfMeasure::where('name', 'Vial')->first()->name;
        $strip = UnitOfMeasure::where('name', 'Strip')->first()->name;

        // -----------------------------------------------------------------
        // 3. Create Categories (with hierarchy in name)
        // -----------------------------------------------------------------
        $categories = [
            'Adhesive Bandages',
            'Insulin Syringes',
            'Penicillins',
            'N95 Masks',
            'Blood Glucose Test Strips',
            'Scalpel Blades',
            'IV Cannulas',
            'Surgical Gloves',
            'Face Shields',
            'Disinfectants',
        ];

        foreach ($categories as $cat) {
            Category::create(['name' => $cat]);
        }

        // -----------------------------------------------------------------
        // 4. Inventory Items + Primary + Secondary Suppliers
        // -----------------------------------------------------------------
        $items = [
            // 1. Band-Aid
            [
                'name' => 'Band-Aid Flexible Fabric (Box of 100)',
                'sku' => 'BAND-FAB-100',
                'category_id' => Category::where('name', 'Adhesive Bandages')->first()->id,
                'unit_of_measure' => $box,
                'unit_quantity' => 100,
                'current_stock' => 45,
                'reorder_point' => 20,
                'reorder_quantity' => 50,
                'unit_cost' => 850.00,
                'unit_price' => 1200.00,
                'primary_supplier_id' => $medico->id,
                'supplier_item_code' => 'MED-BAND100',
                'supplier_price' => 820.00,
                'lead_time_days' => 3,
                'minimum_order_quantity' => 10,
                'sterile' => false,
            ],
            // 2. Insulin Syringe
            [
                'name' => 'BD Insulin Syringe 1mL 31G (Box of 100)',
                'sku' => 'INS-SYR-1ML',
                'category_id' => Category::where('name', 'Insulin Syringes')->first()->id,
                'unit_of_measure' => $box,
                'unit_quantity' => 100,
                'current_stock' => 80,
                'reorder_point' => 30,
                'reorder_quantity' => 100,
                'unit_cost' => 3200.00,
                'unit_price' => 4500.00,
                'primary_supplier_id' => $medico->id,
                'supplier_item_code' => 'BD-INS100',
                'supplier_price' => 3100.00,
                'lead_time_days' => 5,
                'minimum_order_quantity' => 5,
                'sterile' => true,
            ],
            // 3. Amoxicillin
            [
                'name' => 'Amoxicillin 500mg Capsules (Strip of 10)',
                'sku' => 'AMOX-500-10',
                'category_id' => Category::where('name', 'Penicillins')->first()->id,
                'unit_of_measure' => $strip,
                'unit_quantity' => 10,
                'current_stock' => 120,
                'reorder_point' => 50,
                'reorder_quantity' => 200,
                'unit_cost' => 75.00,
                'unit_price' => 150.00,
                'primary_supplier_id' => $pharma->id,
                'supplier_item_code' => 'PH-AMOX500',
                'supplier_price' => 70.00,
                'lead_time_days' => 2,
                'minimum_order_quantity' => 50,
                'controlled_substance' => false,
                'expiry_tracking' => true,
            ],
            // 4. N95 Mask
            [
                'name' => '3M N95 Respirator 1860 (Box of 20)',
                'sku' => '3M-N95-1860',
                'category_id' => Category::where('name', 'N95 Masks')->first()->id,
                'unit_of_measure' => $box,
                'unit_quantity' => 20,
                'current_stock' => 15,
                'reorder_point' => 10,
                'reorder_quantity' => 50,
                'unit_cost' => 4800.00,
                'unit_price' => 6500.00,
                'primary_supplier_id' => $medico->id,
                'supplier_item_code' => 'MED-N9520',
                'supplier_price' => 4600.00,
                'lead_time_days' => 4,
                'minimum_order_quantity' => 5,
            ],
            // 5. Glucose Strips
            [
                'name' => 'Accu-Chek Active Test Strips (Pack of 50)',
                'sku' => 'ACCU-STRIP50',
                'category_id' => Category::where('name', 'Blood Glucose Test Strips')->first()->id,
                'unit_of_measure' => $pack,
                'unit_quantity' => 50,
                'current_stock' => 25,
                'reorder_point' => 15,
                'reorder_quantity' => 40,
                'unit_cost' => 2100.00,
                'unit_price' => 3200.00,
                'primary_supplier_id' => $labchem->id,
                'supplier_item_code' => 'LC-ACCU50',
                'supplier_price' => 2000.00,
                'lead_time_days' => 3,
                'minimum_order_quantity' => 10,
                'expiry_tracking' => true,
            ],
            // 6. Scalpel Blade
            [
                'name' => 'Swann-Morton #11 Sterile Blades (Box of 100)',
                'sku' => 'SWANN-11-100',
                'category_id' => Category::where('name', 'Scalpel Blades')->first()->id,
                'unit_of_measure' => $box,
                'unit_quantity' => 100,
                'current_stock' => 30,
                'reorder_point' => 20,
                'reorder_quantity' => 50,
                'unit_cost' => 3800.00,
                'unit_price' => 5200.00,
                'primary_supplier_id' => $steriletech->id,
                'supplier_item_code' => 'ST-SW11',
                'supplier_price' => 3700.00,
                'lead_time_days' => 6,
                'minimum_order_quantity' => 5,
                'sterile' => true,
            ],
            // 7. IV Cannula
            [
                'name' => 'BD Venflon IV Cannula 20G (Each)',
                'sku' => 'IVC-20G-EA',
                'category_id' => Category::where('name', 'IV Cannulas')->first()->id,
                'unit_of_measure' => $each,
                'unit_quantity' => 1,
                'current_stock' => 90,
                'reorder_point' => 40,
                'reorder_quantity' => 100,
                'unit_cost' => 180.00,
                'unit_price' => 350.00,
                'primary_supplier_id' => $healthtech->id,
                'supplier_item_code' => 'HT-IV20',
                'supplier_price' => 170.00,
                'lead_time_days' => 4,
                'minimum_order_quantity' => 50,
                'sterile' => true,
            ],
            // 8. Surgical Gloves
            [
                'name' => 'Nitrile Exam Gloves Powder-Free (Box of 100)',
                'sku' => 'GLV-NIT-100',
                'category_id' => Category::where('name', 'Surgical Gloves')->first()->id,
                'unit_of_measure' => $box,
                'unit_quantity' => 100,
                'current_stock' => 60,
                'reorder_point' => 30,
                'reorder_quantity' => 80,
                'unit_cost' => 1400.00,
                'unit_price' => 2200.00,
                'primary_supplier_id' => $medico->id,
                'supplier_item_code' => 'MED-GLV100',
                'supplier_price' => 1350.00,
                'lead_time_days' => 3,
                'minimum_order_quantity' => 10,
            ],
            // 9. Face Shield
            [
                'name' => 'Full Face Shield with Foam Headband (Each)',
                'sku' => 'FSHIELD-01',
                'category_id' => Category::where('name', 'Face Shields')->first()->id,
                'unit_of_measure' => $each,
                'unit_quantity' => 1,
                'current_stock' => 200,
                'reorder_point' => 100,
                'reorder_quantity' => 200,
                'unit_cost' => 250.00,
                'unit_price' => 500.00,
                'primary_supplier_id' => $healthtech->id,
                'supplier_item_code' => 'HT-FS01',
                'supplier_price' => 230.00,
                'lead_time_days' => 5,
                'minimum_order_quantity' => 50,
            ],
            // 10. Disinfectant
            [
                'name' => 'Dettol Antiseptic Liquid 500ml (Each)',
                'sku' => 'DET-500ML',
                'category_id' => Category::where('name', 'Disinfectants')->first()->id,
                'unit_of_measure' => $each,
                'unit_quantity' => 1,
                'current_stock' => 75,
                'reorder_point' => 30,
                'reorder_quantity' => 100,
                'unit_cost' => 420.00,
                'unit_price' => 650.00,
                'primary_supplier_id' => $pharma->id,
                'supplier_item_code' => 'PH-DET500',
                'supplier_price' => 400.00,
                'lead_time_days' => 2,
                'minimum_order_quantity' => 20,
                'hazardous_material' => true,
            ],
        ];

        // Create items
        foreach ($items as $data) {
            $item = InventoryItem::create($data);

            // Attach primary supplier to pivot (with is_primary = true)
            $item->suppliers()->attach($item->primary_supplier_id, [
                'supplier_item_code' => $item->supplier_item_code,
                'supplier_price' => $item->supplier_price,
                'lead_time_days' => $item->lead_time_days,
                'minimum_order_quantity' => $item->minimum_order_quantity,
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Attach 1–2 alternative suppliers
            $alternatives = [
                $medico, $pharma, $healthtech, $labchem, $steriletech
            ];
            shuffle($alternatives);
            $altCount = rand(1, 2);

            for ($i = 0; $i < $altCount; $i++) {
                if ($alternatives[$i]->id === $item->primary_supplier_id) continue;

                $item->suppliers()->attach($alternatives[$i]->id, [
                    'supplier_item_code' => strtoupper(substr($alternatives[$i]->name, 0, 3)) . '-' . $item->sku,
                    'supplier_price' => $item->unit_cost * 0.9 + rand(10, 50),
                    'lead_time_days' => rand(3, 7),
                    'minimum_order_quantity' => rand(5, 20),
                    'is_primary' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        InventoryItem::reguard();
    }
}