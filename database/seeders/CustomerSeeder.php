<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'code' => 'CUS001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '1234567890',
                'address' => '123 Main St',
                'city' => 'Springfield',
                'country' => 'USA',
                'gender' => 'male',
                'active' => true,
            ],
            [
                'code' => 'CUS002',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '0987654321',
                'address' => '456 Oak Ave',
                'city' => 'Metropolis',
                'country' => 'USA',
                'gender' => 'female',
                'active' => true,
            ],
            [
                'code' => 'CUS003',
                'first_name' => 'Robert',
                'last_name' => 'Johnson',
                'email' => 'robert.j@example.com',
                'phone' => '5551234567',
                'address' => '789 Pine Rd',
                'city' => 'Gotham',
                'country' => 'USA',
                'gender' => 'male',
                'active' => true,
            ],
            [
                'code' => 'CUS004',
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.d@example.com',
                'phone' => '5559876543',
                'address' => '321 Elm St',
                'city' => 'Star City',
                'country' => 'USA',
                'gender' => 'female',
                'active' => true,
            ],
            [
                'code' => 'CUS005',
                'first_name' => 'Michael',
                'last_name' => 'Wilson',
                'email' => 'michael.w@example.com',
                'phone' => '5552468135',
                'address' => '654 Maple Dr',
                'city' => 'Central City',
                'country' => 'USA',
                'gender' => 'male',
                'active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(['email' => $customer['email']], $customer);
        }
    }
}
