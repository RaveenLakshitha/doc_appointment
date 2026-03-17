<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::unguard();

        $suppliers = [
            [
                'name' => 'MedicalSupplies.co.uk',
                'category' => 'Medical Supplies',
                'description' => 'Leading UK distributor of consumables, PPE, and everyday medical supplies',
                'status' => true,
                'contact_person' => 'Sarah Thompson',
                'email' => 'sales@medicalsupplies.co.uk',
                'phone' => '+44 20 1234 5678',
                'location' => 'London',
                'website' => 'https://www.medicalsupplies.co.uk',
            ],
            [
                'name' => 'Alliance Healthcare',
                'category' => 'Pharmaceuticals',
                'description' => 'Major UK wholesaler and distributor of branded and generic pharmaceuticals',
                'status' => true,
                'contact_person' => 'James Wilson',
                'email' => 'healthcare@alliance-healthcare.co.uk',
                'phone' => '+44 161 987 6543',
                'location' => 'Manchester',
                'website' => 'https://www.alliance-healthcare.co.uk',
            ],
            [
                'name' => 'Medisave UK',
                'category' => 'Equipment',
                'description' => 'Authorised distributor of diagnostic equipment, stethoscopes, and medical devices',
                'status' => true,
                'contact_person' => 'Emma Clarke',
                'email' => 'info@medisave.co.uk',
                'phone' => '+44 1202 345 678',
                'location' => 'Poole, Dorset',
                'website' => 'https://www.medisave.co.uk',
            ],
            [
                'name' => 'Scientific Laboratory Supplies (SLS)',
                'category' => 'Laboratory',
                'description' => 'Largest independent UK supplier of lab reagents, chemicals, and diagnostic kits',
                'status' => true,
                'contact_person' => 'David Patel',
                'email' => 'sales@scientificlabs.co.uk',
                'phone' => '+44 115 982 1111',
                'location' => 'Nottingham',
                'website' => 'https://www.scientificlabs.co.uk',
            ],
            [
                'name' => 'Surgical Holdings',
                'category' => 'Surgical',
                'description' => 'British manufacturer and supplier of high-quality surgical instruments and sterile supplies',
                'status' => true,
                'contact_person' => 'Michael Harris',
                'email' => 'sales@surgicalholdings.co.uk',
                'phone' => '+44 1702 123 456',
                'location' => 'Southend-on-Sea',
                'website' => 'https://www.surgicalholdings.co.uk',
            ],
            [
                'name' => 'Williams Medical Supplies',
                'category' => 'Medical Supplies',
                'description' => 'One of the UK’s largest suppliers of medical consumables and practice equipment',
                'status' => true,
                'contact_person' => 'Laura Evans',
                'email' => 'sales@williamsmedicalsupplies.co.uk',
                'phone' => '+44 1443 844 000',
                'location' => 'Caerphilly, Wales',
                'website' => 'https://www.wms.co.uk',
            ],
            [
                'name' => 'B.Braun Medical Ltd',
                'category' => 'Pharmaceuticals & Devices',
                'description' => 'Global healthcare company providing infusion therapy, pain management, and surgical solutions',
                'status' => true,
                'contact_person' => 'Thomas Müller',
                'email' => 'info.uk@bbraun.com',
                'phone' => '+44 114 225 9000',
                'location' => 'Sheffield',
                'website' => 'https://www.bbraun.co.uk',
            ],
        ];

        $count = 0;

        foreach ($suppliers as $data) {
            Supplier::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
            $count++;
        }

        Supplier::reguard();

        $this->command->info("Suppliers seeded successfully ({$count} suppliers processed).");
    }
}