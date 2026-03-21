<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'CL-RENT', 'name' => 'Clinic Rent'],
            ['code' => 'ELEC', 'name' => 'Electricity & Utilities'],
            ['code' => 'WATER', 'name' => 'Water Bill'],
            ['code' => 'MED-SUPP', 'name' => 'Medical Supplies'],
            ['code' => 'OFF-SUPP', 'name' => 'Office Supplies'],
            ['code' => 'PAYROLL', 'name' => 'Payroll & Salaries'],
            ['code' => 'MAINT', 'name' => 'Maintenance & Repairs'],
            ['code' => 'MARKETING', 'name' => 'Marketing & Advertising'],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::firstOrCreate(
                ['code' => $cat['code']],
                ['name' => $cat['name'], 'is_active' => true]
            );
        }
    }
}
