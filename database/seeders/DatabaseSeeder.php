<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
                    //UnitOfMeasureSeeder::class,
                     //CategorySeeder::class,
                    //SupplierSeeder::class,
                   //InventoryItemSeeder::class,
                  // PatientSeeder::class,
                    //AppointmentSeeder::class,
                   // MedicationTemplateCategorySeeder::class,
                    MedicationTemplateSeeder::class,
                ]);

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
