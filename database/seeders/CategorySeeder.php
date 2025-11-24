<?php
// database/seeders/CategorySeeder.php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable mass-assignment protection temporarily
        Category::unguard();

        // -----------------------------------------------------------------
        // ROOT CATEGORIES (Level 0)
        // -----------------------------------------------------------------
        $medicalSupplies = Category::create([
            'name'        => 'Medical Supplies',
            'description' => 'Consumables used in patient care',
            'is_active'   => true,
        ]);

        $pharmaceuticals = Category::create([
            'name'        => 'Pharmaceuticals',
            'description' => 'Drugs and medications',
            'is_active'   => true,
        ]);

        $equipment = Category::create([
            'name'        => 'Equipment',
            'description' => 'Medical devices and tools',
            'is_active'   => true,
        ]);

        $ppe = Category::create([
            'name'        => 'PPE',
            'description' => 'Personal Protective Equipment',
            'is_active'   => true,
        ]);

        $laboratory = Category::create([
            'name'        => 'Laboratory',
            'description' => 'Lab reagents and consumables',
            'is_active'   => true,
        ]);

        $surgical = Category::create([
            'name'        => 'Surgical',
            'description' => 'Surgical instruments and supplies',
            'is_active'   => true,
        ]);

        $stationery = Category::create([
            'name'        => 'Stationery',
            'description' => 'Office and admin supplies',
            'is_active'   => true,
        ]);

        // -----------------------------------------------------------------
        // LEVEL 1 SUBCATEGORIES
        // -----------------------------------------------------------------
        $bandages = Category::create([
            'name'        => 'Bandages & Dressings',
            'description' => 'Adhesive bandages, gauze, wound dressings',
            'is_active'   => true,
            'parent_id'   => $medicalSupplies->id,
        ]);

        $syringes = Category::create([
            'name'        => 'Syringes & Needles',
            'description' => 'Disposable syringes, hypodermic needles',
            'is_active'   => true,
            'parent_id'   => $medicalSupplies->id,
        ]);

        $gloves = Category::create([
            'name'        => 'Gloves',
            'description' => 'Latex, nitrile, vinyl examination gloves',
            'is_active'   => true,
            'parent_id'   => $medicalSupplies->id,
        ]);

        $ivSupplies = Category::create([
            'name'        => 'IV Supplies',
            'description' => 'IV catheters, infusion sets, fluids',
            'is_active'   => true,
            'parent_id'   => $medicalSupplies->id,
        ]);

        // Pharmaceuticals
        $antibiotics = Category::create([
            'name'        => 'Antibiotics',
            'description' => 'Broad-spectrum and targeted antibiotics',
            'is_active'   => true,
            'parent_id'   => $pharmaceuticals->id,
        ]);

        $analgesics = Category::create([
            'name'        => 'Analgesics',
            'description' => 'Pain relief medications',
            'is_active'   => true,
            'parent_id'   => $pharmaceuticals->id,
        ]);

        $vaccines = Category::create([
            'name'        => 'Vaccines',
            'description' => 'Immunization and preventive vaccines',
            'is_active'   => true,
            'parent_id'   => $pharmaceuticals->id,
        ]);

        // Equipment
        $diagnostic = Category::create([
            'name'        => 'Diagnostic Equipment',
            'description' => 'BP monitors, stethoscopes, thermometers',
            'is_active'   => true,
            'parent_id'   => $equipment->id,
        ]);

        $monitoring = Category::create([
            'name'        => 'Patient Monitoring',
            'description' => 'Pulse oximeters, ECG machines',
            'is_active'   => true,
            'parent_id'   => $equipment->id,
        ]);

        $imaging = Category::create([
            'name'        => 'Imaging Devices',
            'description' => 'Ultrasound, X-ray portable units',
            'is_active'   => true,
            'parent_id'   => $equipment->id,
        ]);

        // PPE
        $masks = Category::create([
            'name'        => 'Masks',
            'description' => 'Surgical, N95, face shields',
            'is_active'   => true,
            'parent_id'   => $ppe->id,
        ]);

        $gowns = Category::create([
            'name'        => 'Gowns & Aprons',
            'description' => 'Isolation gowns, surgical gowns',
            'is_active'   => true,
            'parent_id'   => $ppe->id,
        ]);

        // Laboratory
        $reagents = Category::create([
            'name'        => 'Reagents',
            'description' => 'Chemical reagents for lab tests',
            'is_active'   => true,
            'parent_id'   => $laboratory->id,
        ]);

        $testKits = Category::create([
            'name'        => 'Test Kits',
            'description' => 'Rapid diagnostic test kits',
            'is_active'   => true,
            'parent_id'   => $laboratory->id,
        ]);

        // Surgical
        $instruments = Category::create([
            'name'        => 'Surgical Instruments',
            'description' => 'Scalpels, forceps, retractors',
            'is_active'   => true,
            'parent_id'   => $surgical->id,
        ]);

        $sutures = Category::create([
            'name'        => 'Sutures & Staples',
            'description' => 'Absorbable and non-absorbable sutures',
            'is_active'   => true,
            'parent_id'   => $surgical->id,
        ]);

        // Stationery
        $forms = Category::create([
            'name'        => 'Forms & Charts',
            'description' => 'Patient forms, medical charts',
            'is_active'   => true,
            'parent_id'   => $stationery->id,
        ]);

        $office = Category::create([
            'name'        => 'Office Supplies',
            'description' => 'Pens, paper, clipboards',
            'is_active'   => true,
            'parent_id'   => $stationery->id,
        ]);

        // -----------------------------------------------------------------
        // LEVEL 2 SUB-SUBCATEGORIES (Examples)
        // -----------------------------------------------------------------
        Category::create([
            'name'        => 'Adhesive Bandages',
            'description' => 'Band-Aids, fabric/plastic strips',
            'is_active'   => true,
            'parent_id'   => $bandages->id,
        ]);

        Category::create([
            'name'        => 'Gauze Pads',
            'description' => 'Sterile and non-sterile gauze',
            'is_active'   => true,
            'parent_id'   => $bandages->id,
        ]);

        Category::create([
            'name'        => 'Insulin Syringes',
            'description' => 'Fine needle syringes for diabetes',
            'is_active'   => true,
            'parent_id'   => $syringes->id,
        ]);

        Category::create([
            'name'        => 'Pen Needles',
            'description' => 'Needles for insulin pens',
            'is_active'   => true,
            'parent_id'   => $syringes->id,
        ]);

        Category::create([
            'name'        => 'Latex Gloves',
            'description' => 'Powdered and powder-free',
            'is_active'   => true,
            'parent_id'   => $gloves->id,
        ]);

        Category::create([
            'name'        => 'Nitrile Gloves',
            'description' => 'Latex-free examination gloves',
            'is_active'   => true,
            'parent_id'   => $gloves->id,
        ]);

        Category::create([
            'name'        => 'Cephalosporins',
            'description' => 'Ceftriaxone, Cefuroxime',
            'is_active'   => true,
            'parent_id'   => $antibiotics->id,
        ]);

        Category::create([
            'name'        => 'Penicillins',
            'description' => 'Amoxicillin, Augmentin',
            'is_active'   => true,
            'parent_id'   => $antibiotics->id,
        ]);

        Category::create([
            'name'        => 'Paracetamol',
            'description' => 'Acetaminophen tablets/syrup',
            'is_active'   => true,
            'parent_id'   => $analgesics->id,
        ]);

        Category::create([
            'name'        => 'Ibuprofen',
            'description' => 'NSAID for pain and inflammation',
            'is_active'   => true,
            'parent_id'   => $analgesics->id,
        ]);

        Category::create([
            'name'        => 'Blood Pressure Monitors',
            'description' => 'Digital and manual BP cuffs',
            'is_active'   => true,
            'parent_id'   => $diagnostic->id,
        ]);

        Category::create([
            'name'        => 'Stethoscopes',
            'description' => 'Littmann, dual-head',
            'is_active'   => true,
            'parent_id'   => $diagnostic->id,
        ]);

        Category::create([
            'name'        => 'N95 Masks',
            'description' => 'Respirator masks with filter',
            'is_active'   => true,
            'parent_id'   => $masks->id,
        ]);

        Category::create([
            'name'        => 'Surgical Masks',
            'description' => '3-ply disposable masks',
            'is_active'   => true,
            'parent_id'   => $masks->id,
        ]);

        Category::create([
            'name'        => 'Blood Glucose Test Strips',
            'description' => 'For glucometers',
            'is_active'   => true,
            'parent_id'   => $testKits->id,
        ]);

        Category::create([
            'name'        => 'Pregnancy Test Kits',
            'description' => 'hCG urine test kits',
            'is_active'   => true,
            'parent_id'   => $testKits->id,
        ]);

        Category::create([
            'name'        => 'Scalpel Blades',
            'description' => 'Disposable sterile blades',
            'is_active'   => true,
            'parent_id'   => $instruments->id,
        ]);

        Category::create([
            'name'        => 'Hemostats',
            'description' => 'Artery forceps, mosquito clamps',
            'is_active'   => true,
            'parent_id'   => $instruments->id,
        ]);

        // Re-enable mass-assignment protection
        Category::reguard();
    }
}