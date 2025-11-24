<?php

namespace Database\Seeders;

use App\Models\UnitOfMeasure;
use Illuminate\Database\Seeder;

class UnitOfMeasureSeeder extends Seeder
{
    public function run(): void
    {
        UnitOfMeasure::unguard();

        $units = [
            ['name' => 'Each',          'abbreviation' => 'ea'],
            ['name' => 'Box',           'abbreviation' => 'bx'],
            ['name' => 'Pack',          'abbreviation' => 'pk'],
            ['name' => 'Pair',          'abbreviation' => 'pr'],
            ['name' => 'Roll',          'abbreviation' => 'rl'],
            ['name' => 'Bottle',        'abbreviation' => 'btl'],
            ['name' => 'Vial',          'abbreviation' => 'vial'],
            ['name' => 'Tube',         'abbreviation' => 'tube'],
            ['name' => 'Ampoule',       'abbreviation' => 'amp'],
            ['name' => 'Strip',         'abbreviation' => 'strip'],
            ['name' => 'Kit',           'abbreviation' => 'kit'],
            ['name' => 'Set',           'abbreviation' => 'set'],
            ['name' => 'Bag',           'abbreviation' => 'bag'],
            ['name' => 'Liter',         'abbreviation' => 'L'],
            ['name' => 'Milliliter',    'abbreviation' => 'mL'],
        ];

        foreach ($units as $unit) {
            UnitOfMeasure::create($unit + ['is_active' => true]);
        }

        UnitOfMeasure::reguard();
    }
}