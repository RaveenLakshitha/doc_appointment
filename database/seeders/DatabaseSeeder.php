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

        // UserSeeder::class,
        //  AgeGroupSeeder::class,
        //  RolePermissionSeeder::class,
        //UnitOfMeasureSeeder::class,
        // CategorySeeder::class,
        // SupplierSeeder::class,
        //InventoryItemSeeder::class,
        //    PatientSeeder::class,
        //  AppointmentSeeder::class,
        //     MedicationTemplateCategorySeeder::class,
        //  DepartmentSeeder::class,
        //SpecializationSeeder::class,
        //     DoctorSeeder::class,
        //   ServicesSeeder::class,
      MedicineTemplateSeeder::class,
      // PrescriptionSeeder::class,
      // RoomSeeder::class,
      // DoctorScheduleSeeder::class,
      //  DropdownSeeder::class,
      //AppointmentRequestSeeder::class,
      // AppointmentSeeder::class,
      //DoctorSeeder::class,
      // EmployeeSeeder::class,
      //CategorySeeder::class,
      //     InventoryItemSeeder::class,
      // MedicineSeeder::class,
      //   MedicineBatchSeeder::class,
      // LeaveTypeSeeder::class,

    ]);

    // User::factory()->create([
    //     'name' => 'Test User',
    //     'email' => 'test@example.com',
    // ]);
  }
}
