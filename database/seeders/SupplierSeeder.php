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
                'name'           => 'Medico Supplies Ltd.',
                'category'       => 'Medical Supplies',
                'description'    => 'Leading distributor of consumables and PPE',
                'status'         => true,
                'contact_person' => 'Saman Perera',
                'email'          => 'saman@medico.lk',
                'phone'          => '+94 11 234 5678',
                'location'       => 'Colombo 03',
                'website'        => 'https://medico.lk',
            ],
            [
                'name'           => 'PharmaCare Distributors',
                'category'       => 'Pharmaceuticals',
                'description'    => 'Authorized distributor for generics and branded drugs',
                'status'         => true,
                'contact_person' => 'Nimal Fernando',
                'email'          => 'nimal@pharmacare.lk',
                'phone'          => '+94 11 345 6789',
                'location'       => 'Kandy',
                'website'        => 'https://pharmacare.lk',
            ],
            [
                'name'           => 'HealthTech Solutions',
                'category'       => 'Equipment',
                'description'    => 'Medical devices and diagnostic equipment',
                'status'         => true,
                'contact_person' => 'Kamal Rathnayake',
                'email'          => 'kamal@healthtech.lk',
                'phone'          => '+94 77 123 4567',
                'location'       => 'Galle',
                'website'        => 'https://healthtech.lk',
            ],
            [
                'name'           => 'LabChem Lanka',
                'category'       => 'Laboratory',
                'description'    => 'Reagents and diagnostic test kits',
                'status'         => true,
                'contact_person' => 'Priya Silva',
                'email'          => 'priya@labchem.lk',
                'phone'          => '+94 91 223 4455',
                'location'       => 'Matara',
                'website'        => 'https://labchem.lk',
            ],
            [
                'name'           => 'SterileTech Pvt Ltd',
                'category'       => 'Surgical',
                'description'    => 'Surgical instruments and sterile supplies',
                'status'         => true,
                'contact_person' => 'Ruwan Jayasinghe',
                'email'          => 'ruwan@steriletech.lk',
                'phone'          => '+94 11 567 8901',
                'location'       => 'Negombo',
                'website'        => 'https://steriletech.lk',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        Supplier::reguard();
    }
}