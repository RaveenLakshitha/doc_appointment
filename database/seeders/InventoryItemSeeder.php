<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    public function run(): void
    {
        InventoryItem::unguard();

        // Get a real supplier ID (the first one that exists)
        $defaultSupplier = Supplier::first();

        if (!$defaultSupplier) {
            $this->command->error('No suppliers found in the database. Please run SupplierSeeder first.');
            return;
        }

        $defaultSupplierId = $defaultSupplier->id;

        $this->command->info("Using supplier ID {$defaultSupplierId} ({$defaultSupplier->name}) for all inventory items.");

        // Helper to safely get category ID
        $getCategoryId = fn(string $name) => Category::where('name', $name)->first()?->id;

        $items = [
            // ───────────────────────────────────────────────
            // Pain / Fever
            // ───────────────────────────────────────────────
            [
                'name' => 'Paracetamol 500 mg Tablets',
                'sku' => 'PARA-500-TAB-100s',
                'category_id' => $getCategoryId('Analgesics'),
                'primary_supplier_id' => $defaultSupplierId,
                'generic_name' => 'Paracetamol',
                'medicine_type' => 'Tablet',
                'dosage' => '500 mg',
                'unit_of_measure' => 'Strip',
                'unit_quantity' => 10,
                'unit_cost' => 85.00,
                'unit_price' => 150.00,
                'current_stock' => 680,
                'minimum_stock_level' => 150,
                'expiry_tracking' => true,
                'is_active' => true,
            ],

            [
                'name' => 'Paracetamol 1 g IV Injection',
                'sku' => 'PARA-1G-VIAL-10',
                'category_id' => $getCategoryId('Injectables') ?? $getCategoryId('General Medicine'),
                'primary_supplier_id' => $defaultSupplierId,
                'generic_name' => 'Paracetamol',
                'medicine_type' => 'Injection',
                'dosage' => '1000 mg / 100 mL',
                'unit_of_measure' => 'Vial',
                'unit_quantity' => 1,
                'unit_cost' => 420.00,
                'unit_price' => 780.00,
                'current_stock' => 85,
                'minimum_stock_level' => 20,
                'expiry_tracking' => true,
                'is_active' => true,
            ],

            // ───────────────────────────────────────────────
            // Antibiotics
            // ───────────────────────────────────────────────
            [
                'name' => 'Amoxicillin 500 mg Capsules',
                'sku' => 'AMOX-500-CAP-20s',
                'category_id' => $getCategoryId('Antibiotics'),
                'primary_supplier_id' => $defaultSupplierId,
                'generic_name' => 'Amoxicillin',
                'medicine_type' => 'Capsule',
                'dosage' => '500 mg',
                'unit_of_measure' => 'Box',
                'unit_quantity' => 20,
                'unit_cost' => 720.00,
                'unit_price' => 1150.00,
                'current_stock' => 210,
                'minimum_stock_level' => 50,
                'expiry_tracking' => true,
                'is_active' => true,
            ],

            [
                'name' => 'Ceftriaxone 1 g IV/IM Injection',
                'sku' => 'CEFT-1G-VIAL-1',
                'category_id' => $getCategoryId('Antibiotics') ?? $getCategoryId('Injectables'),
                'primary_supplier_id' => $defaultSupplierId,
                'generic_name' => 'Ceftriaxone',
                'medicine_type' => 'Injection',
                'dosage' => '1 g',
                'unit_of_measure' => 'Vial',
                'unit_quantity' => 1,
                'unit_cost' => 380.00,
                'unit_price' => 720.00,
                'current_stock' => 140,
                'minimum_stock_level' => 30,
                'expiry_tracking' => true,
                'is_active' => true,
            ],

            // ───────────────────────────────────────────────
            // Gastroenterology
            // ───────────────────────────────────────────────
            [
                'name' => 'Omeprazole 20 mg Capsules',
                'sku' => 'OMEP-20-CAP-30s',
                'category_id' => $getCategoryId('Anti-ulcer Agents') ?? $getCategoryId('Gastrointestinal'),
                'primary_supplier_id' => $defaultSupplierId,
                'generic_name' => 'Omeprazole',
                'medicine_type' => 'Capsule',
                'dosage' => '20 mg',
                'unit_of_measure' => 'Strip',
                'unit_quantity' => 10,
                'unit_cost' => 180.00,
                'unit_price' => 320.00,
                'current_stock' => 360,
                'minimum_stock_level' => 80,
                'expiry_tracking' => true,
                'is_active' => true,
            ],

            // ───────────────────────────────────────────────
            // Respiratory
            // ───────────────────────────────────────────────
            [
                'name' => 'Salbutamol 100 mcg Inhaler',
                'sku' => 'SALB-100-INH-200',
                'category_id' => $getCategoryId('Respiratory') ?? $getCategoryId('Inhalers & Nebulizers'),
                'primary_supplier_id' => $defaultSupplierId,
                'generic_name' => 'Salbutamol',
                'medicine_type' => 'Metered Dose Inhaler',
                'dosage' => '100 mcg / puff',
                'unit_of_measure' => 'Each',
                'unit_quantity' => 1,
                'unit_cost' => 1250.00,
                'unit_price' => 1950.00,
                'current_stock' => 95,
                'minimum_stock_level' => 20,
                'expiry_tracking' => true,
                'is_active' => true,
            ],

            // You can add more items here...
        ];

        $createdCount = 0;

        foreach ($items as $item) {
            if (empty($item['category_id'])) {
                $this->command->warn("Skipping item '{$item['name']}' — category not found.");
                continue;
            }

            InventoryItem::create($item);
            $createdCount++;
        }

        InventoryItem::reguard();

        $this->command->info("Inventory items seeded successfully ({$createdCount} items created).");
    }
}