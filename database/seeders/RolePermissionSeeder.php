<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── All permissions: ONLY index / create / edit / delete per resource ──
        $permissions = [

            // Dashboard
            'dashboard.index',

            // Doctor Panel (doctor-specific views)
            'doctor-panel.index',

            // Doctors & Schedules
            'doctors.index',
            'doctors.create',
            'doctors.edit',
            'doctors.delete',
            'doctor-schedules.index',
            'doctor-schedules.create',
            'doctor-schedules.edit',
            'doctor-schedules.delete',
            'specializations.index',
            'specializations.create',
            'specializations.edit',
            'specializations.delete',
            'age-groups.index',
            'age-groups.create',
            'age-groups.edit',
            'age-groups.delete',

            // Patients
            'patients.index',
            'patients.create',
            'patients.edit',
            'patients.delete',

            // Appointments & Queues
            'appointments.index',
            'appointments.create',
            'appointments.edit',
            'appointments.delete',
            'queues.index',

            // Appointment Requests
            'appointment_requests.index',
            'appointment_requests.create',
            'appointment_requests.edit',
            'appointment_requests.delete',

            // Prescriptions & Medicine Templates
            'prescriptions.index',
            'prescriptions.create',
            'prescriptions.edit',
            'prescriptions.delete',
            'medicine-templates.index',
            'medicine-templates.create',
            'medicine-templates.edit',
            'medicine-templates.delete',

            // Billing
            'invoices.index',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'payments.index',
            'payments.create',
            'payments.edit',
            'payments.delete',
            'cash-registers.index',

            // Clinic
            'departments.index',
            'departments.create',
            'departments.edit',
            'departments.delete',
            'rooms.index',
            'rooms.create',
            'rooms.edit',
            'rooms.delete',
            'services.index',
            'services.create',
            'services.edit',
            'services.delete',
            'treatments.index',
            'treatments.create',
            'treatments.edit',
            'treatments.delete',

            // Inventory
            'inventory.index',
            'inventory.create',
            'inventory.edit',
            'inventory.delete',
            'suppliers.index',
            'suppliers.create',
            'suppliers.edit',
            'suppliers.delete',
            'categories.index',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'unit-of-measures.index',
            'unit-of-measures.create',
            'unit-of-measures.edit',
            'unit-of-measures.delete',

            // Users & Roles
            'users.index',
            'users.create',
            'users.edit',
            'users.delete',
            'roles.index',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // HR – Employees
            'employees.index',
            'employees.create',
            'employees.edit',
            'employees.delete',

            // HR – Attendance (singular, not plural)
            'attendance.index',
            'attendance.create',
            'attendance.edit',
            'attendance.delete',

            // HR – Leave
            'leave-types.index',
            'leave-types.create',
            'leave-types.edit',
            'leave-types.delete',
            'leave-requests.index',
            'leave-requests.create',
            'leave-requests.edit',
            'leave-requests.delete',
            'leave-requests.approve',
            'leave-requests.reject',

            // Reports
            'reports.index',

            // Settings & Dropdowns
            'settings.index',
            'settings.edit',
            'dropdowns.index',
            'dropdowns.create',
            'dropdowns.edit',
            'dropdowns.delete',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ── Role assignments ──────────────────────────────────────────────────

        // Admin — gets ALL permissions
        Role::firstOrCreate(['name' => 'admin'])
            ->syncPermissions(Permission::all()->pluck('name'));

        // Doctor
        Role::firstOrCreate(['name' => 'doctor'])->syncPermissions([
            'dashboard.index',
            'doctor-panel.index',
            'patients.index',
            'patients.create',
            'patients.edit',
            'appointments.index',
            'appointments.create',
            'appointments.edit',
            'prescriptions.index',
            'prescriptions.create',
            'prescriptions.edit',
            'medicine-templates.index',
            'medicine-templates.create',
            'medicine-templates.edit',
        ]);

        // Primary Care Provider
        Role::firstOrCreate(['name' => 'primary_care_provider'])->syncPermissions([
            'dashboard.index',
            'patients.index',
            'patients.create',
            'patients.edit',
            'appointments.index',
            'appointments.create',
            'appointments.edit',
            'prescriptions.index',
            'prescriptions.create',
            'prescriptions.edit',
            'medicine-templates.index',
            'inventory.index',
            'appointment_requests.index',
            'appointment_requests.create',
            'appointment_requests.edit',
        ]);

        // Receptionist
        Role::firstOrCreate(['name' => 'receptionist'])->syncPermissions([
            'dashboard.index',
            'patients.index',
            'patients.create',
            'patients.edit',
            'appointments.index',
            'appointments.create',
            'appointments.edit',
            'invoices.index',
            'invoices.create',
            'payments.index',
            'payments.create',
            'cash-registers.index',
        ]);

        // Nurse
        Role::firstOrCreate(['name' => 'nurse'])->syncPermissions([
            'dashboard.index',
            'patients.index',
            'appointments.index',
        ]);

        // HR
        Role::firstOrCreate(['name' => 'hr'])->syncPermissions([
            'dashboard.index',
            'employees.index',
            'employees.create',
            'employees.edit',
            'employees.delete',
            'attendance.index',
            'attendance.create',
            'attendance.edit',
            'attendance.delete',
            'leave-requests.index',
            'leave-requests.create',
            'leave-requests.edit',
            'leave-requests.delete',
            'leave-requests.approve',
            'leave-requests.reject',
            'leave-types.index',
            'leave-types.create',
            'leave-types.edit',
            'leave-types.delete',
            'leave-types.delete',
            'users.index',
            'roles.index',
        ]);

        // Support
        Role::firstOrCreate(['name' => 'support'])->syncPermissions([
            'dashboard.index',
            'departments.index',
            'rooms.index',
            'rooms.edit',
        ]);
    }
}