<?php

namespace Database\Seeders;

use App\Models\MedicineTemplate;
use App\Models\InventoryItem;
use App\Models\TemplateMedication;
use Illuminate\Database\Seeder;

class MedicineTemplateSeeder extends Seeder
{
    public function run(): void
    {
        MedicineTemplate::unguard();
        TemplateMedication::unguard();

        // Fetch inventory items by generic_name
        $paracetamol = InventoryItem::where('generic_name', 'Paracetamol')->first();
        $amoxicillin = InventoryItem::where('generic_name', 'Amoxicillin')->first();
        $ceftriaxone = InventoryItem::where('generic_name', 'Ceftriaxone')->first();
        $omeprazole = InventoryItem::where('generic_name', 'Omeprazole')->first();
        $salbutamol = InventoryItem::where('generic_name', 'Salbutamol')->first();

        $missing = [];
        if (!$paracetamol)
            $missing[] = 'Paracetamol';
        if (!$amoxicillin)
            $missing[] = 'Amoxicillin';
        if (!$ceftriaxone)
            $missing[] = 'Ceftriaxone';
        if (!$omeprazole)
            $missing[] = 'Omeprazole';
        if (!$salbutamol)
            $missing[] = 'Salbutamol';

        if (!empty($missing)) {
            $this->command->warn('Cannot seed medicine templates. Missing inventory items: ' . implode(', ', $missing));
            $this->command->warn('→ Run InventoryItemSeeder first and ensure these generic_names exist.');
            return;
        }

        $this->command->info('All required inventory items found. Seeding medicine templates...');

        $templatesCreated = 0;

        // ───────────────────────────────────────────────
        // Template 1: Fever & Pain Relief (Adult)
        // ───────────────────────────────────────────────
        $template1 = MedicineTemplate::create([
            'name' => 'Fever & Pain Relief (Adult)',
            'category' => 'General Medicine',
            'description' => 'Standard template for mild to moderate fever, headache, or body pain in adults.',
        ]);

        TemplateMedication::create([
            'medicine_template_id' => $template1->id,
            'inventory_item_id' => $paracetamol->id,
            'name' => $paracetamol->name,
            'dosage' => '500–1000 mg',
            'route' => 'Oral',
            'frequency' => 'Every 6–8 hours as needed',
            'instructions' => 'Take with food or water. Max 4g/day.',
        ]);

        $templatesCreated++;

        // ───────────────────────────────────────────────
        // Template 2: Community-Acquired Pneumonia (Mild)
        // ───────────────────────────────────────────────
        $template2 = MedicineTemplate::create([
            'name' => 'Community-Acquired Pneumonia (Mild)',
            'category' => 'Respiratory',
            'description' => 'Outpatient treatment for mild bacterial lower respiratory tract infection.',
        ]);

        TemplateMedication::create([
            'medicine_template_id' => $template2->id,
            'inventory_item_id' => $amoxicillin->id,
            'name' => $amoxicillin->name,
            'dosage' => '500 mg',
            'route' => 'Oral',
            'frequency' => 'Three times daily',
            'instructions' => 'Complete full 7-day course. Take with food.',
        ]);

        TemplateMedication::create([
            'medicine_template_id' => $template2->id,
            'inventory_item_id' => $paracetamol->id,
            'name' => $paracetamol->name,
            'dosage' => '500–1000 mg',
            'route' => 'Oral',
            'frequency' => 'Every 6 hours as needed',
            'instructions' => 'For fever and pain relief.',
        ]);

        $templatesCreated++;

        // ───────────────────────────────────────────────
        // Template 3: Severe Bacterial Infection (IV Start)
        // ───────────────────────────────────────────────
        $template3 = MedicineTemplate::create([
            'name' => 'Severe Bacterial Infection (IV Start)',
            'category' => 'Infectious Diseases',
            'description' => 'Initial IV therapy for suspected severe bacterial infection (e.g. sepsis, cellulitis).',
        ]);

        TemplateMedication::create([
            'medicine_template_id' => $template3->id,
            'inventory_item_id' => $ceftriaxone->id,
            'name' => $ceftriaxone->name,
            'dosage' => '1–2 g',
            'route' => 'IV',
            'frequency' => 'Once daily',
            'instructions' => 'Administer over 30 minutes. Monitor for anaphylaxis.',
        ]);

        TemplateMedication::create([
            'medicine_template_id' => $template3->id,
            'inventory_item_id' => $paracetamol->id,
            'name' => $paracetamol->name,
            'dosage' => '1 g',
            'route' => 'IV',
            'frequency' => 'Every 6–8 hours as needed',
            'instructions' => 'For fever control.',
        ]);

        $templatesCreated++;

        // ───────────────────────────────────────────────
        // Template 4: GERD Maintenance Therapy
        // ───────────────────────────────────────────────
        $template4 = MedicineTemplate::create([
            'name' => 'GERD Maintenance Therapy',
            'category' => 'Gastroenterology',
            'description' => 'Long-term acid suppression for gastroesophageal reflux disease.',
        ]);

        TemplateMedication::create([
            'medicine_template_id' => $template4->id,
            'inventory_item_id' => $omeprazole->id,
            'name' => $omeprazole->name,
            'dosage' => '20 mg',
            'route' => 'Oral',
            'frequency' => 'Once daily before breakfast',
            'instructions' => 'Swallow whole. Do not crush/chew. Take 30–60 min before meal.',
        ]);

        TemplateMedication::create([
            'medicine_template_id' => $template4->id,
            'inventory_item_id' => $salbutamol->id,
            'name' => $salbutamol->name,
            'dosage' => '100 mcg',
            'route' => 'Inhalation',
            'frequency' => 'As needed (max 8 puffs/day)',
            'instructions' => 'For associated wheezing or shortness of breath.',
        ]);

        $templatesCreated++;

        // ───────────────────────────────────────────────
        // Final feedback
        // ───────────────────────────────────────────────
        MedicineTemplate::reguard();
        TemplateMedication::reguard();

        $this->command->info("Medicine templates seeded successfully! ({$templatesCreated} templates created with linked medications)");
    }
}