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
        // RolePermissionSeeder::class,
        // DepartmentSeeder::class,
        // SpecializationSeeder::class,
        // RoomSeeder::class,
        // CategorySeeder::class,
        // UnitOfMeasureSeeder::class,
        // SupplierSeeder::class,
        // AgeGroupSeeder::class,
        // LeaveTypeSeeder::class,
        // DropdownSeeder::class,

        // UserSeeder::class,
        // EmployeeSeeder::class,
        // DoctorSeeder::class, // Omitted as requested

      PatientSeeder::class,
      // MedicineTemplateSeeder::class,
      // InventoryItemSeeder::class,
      // ServicesSeeder::class,

      // TreatmentSeeder::class,
      // ExpenseCategorySeeder::class,
      // ExpenseSeeder::class,

      // DoctorScheduleSeeder::class,
      //PrescriptionSeeder::class,
      // AppointmentSeeder::class,
      // AppointmentRequestSeeder::class,
    ]);

    // User::factory()->create([
    //     'name' => 'Test User',
    //     'email' => 'test@example.com',
    // ]);
  }
}
