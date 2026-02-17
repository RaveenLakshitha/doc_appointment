<?php

namespace Database\Seeders;

use App\Models\MedicineBatch;
use App\Models\InventoryItem;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MedicineBatchSeeder extends Seeder
{
    public function run(): void
    {
        MedicineBatch::unguard();

        $medicines = InventoryItem::whereNotNull('generic_name')->get(); // Only medicines

        $batches = [
            // Paracetamol
            [
                'inventory_item_id'   => $medicines[0]?->id,
                'batch_number'        => 'PARA-2501',
                'manufacturing_date'  => '2025-01-15',
                'expiry_date'         => '2027-01-14',
                'initial_quantity'    => 500,
                'current_quantity'    => 500,
            ],
            [
                'inventory_item_id'   => $medicines[0]?->id,
                'batch_number'        => 'PARA-2502',
                'manufacturing_date'  => '2025-03-10',
                'expiry_date'         => '2027-03-09',
                'initial_quantity'    => 800,
                'current_quantity'    => 720,
            ],

            // Amoxicillin
            [
                'inventory_item_id'   => $medicines[1]?->id,
                'batch_number'        => 'AMOX-2409',
                'manufacturing_date'  => '2024-09-20',
                'expiry_date'         => '2026-09-19',
                'initial_quantity'    => 300,
                'current_quantity'    => 180,
            ],

            // Ceftriaxone
            [
                'inventory_item_id'   => $medicines[2]?->id,
                'batch_number'        => 'CEFT-2504',
                'manufacturing_date'  => '2025-04-05',
                'expiry_date'         => '2027-04-04',
                'initial_quantity'    => 150,
                'current_quantity'    => 150,
            ],

            // Omeprazole
            [
                'inventory_item_id'   => $medicines[3]?->id,
                'batch_number'        => 'OMEP-2502',
                'manufacturing_date'  => '2025-02-12',
                'expiry_date'         => '2027-02-11',
                'initial_quantity'    => 400,
                'current_quantity'    => 400,
            ],

            // Salbutamol
            [
                'inventory_item_id'   => $medicines[4]?->id,
                'batch_number'        => 'SALB-2411',
                'manufacturing_date'  => '2024-11-08',
                'expiry_date'         => '2026-11-07',
                'initial_quantity'    => 120,
                'current_quantity'    => 92,
            ],

            // More batches for first two items (to have variety)
            [
                'inventory_item_id'   => $medicines[0]?->id,
                'batch_number'        => 'PARA-2503',
                'manufacturing_date'  => '2025-05-01',
                'expiry_date'         => '2027-04-30',
                'initial_quantity'    => 600,
                'current_quantity'    => 600,
            ],
            [
                'inventory_item_id'   => $medicines[1]?->id,
                'batch_number'        => 'AMOX-2501',
                'manufacturing_date'  => '2025-01-20',
                'expiry_date'         => '2027-01-19',
                'initial_quantity'    => 250,
                'current_quantity'    => 250,
            ],
            [
                'inventory_item_id'   => $medicines[2]?->id,
                'batch_number'        => 'CEFT-2505',
                'manufacturing_date'  => '2025-05-15',
                'expiry_date'         => '2027-05-14',
                'initial_quantity'    => 200,
                'current_quantity'    => 200,
            ],
            [
                'inventory_item_id'   => $medicines[3]?->id,
                'batch_number'        => 'OMEP-2503',
                'manufacturing_date'  => '2025-06-10',
                'expiry_date'         => '2027-06-09',
                'initial_quantity'    => 350,
                'current_quantity'    => 350,
            ],
        ];

        foreach ($batches as $batch) {
            if ($batch['inventory_item_id']) {
                MedicineBatch::create($batch);
            }
        }

        MedicineBatch::reguard();
    }
}