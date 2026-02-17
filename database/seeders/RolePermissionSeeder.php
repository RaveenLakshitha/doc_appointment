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

        // Define permissions using dot notation (resource.action)
        $permissions = [
            // ── Dashboards (view only, role-specific)
            'dashboard.admin',
            'dashboard.doctor',
            'dashboard.receptionist',
            'dashboard.nurse',
            'dashboard.hr',
            'dashboard.support',
            'dashboard.primary_care_provider',

            // ── Doctors & Specializations
            'doctors.index', 'doctors.create', 'doctors.show', 'doctors.edit', 'doctors.delete',
            'doctor-schedules.index', 'doctor-schedules.edit',
            'specializations.index', 'specializations.create', 'specializations.edit', 'specializations.delete',
            'age-groups.index', 'age-groups.create', 'age-groups.edit', 'age-groups.delete',

            // ── Patients
            'patients.index', 'patients.create', 'patients.show', 'patients.edit', 'patients.delete',

            // ── Appointments
            'appointments.index', 'appointments.create', 'appointments.show', 'appointments.edit', 'appointments.delete',
            'appointments.own',           // view own only
            'appointments.calendar',
            'appointments.requests',

            'queues.view','queues.manage',

            // ── Prescriptions & Templates
            'prescriptions.index', 'prescriptions.create', 'prescriptions.edit', 'prescriptions.delete',
            'medicine-templates.index', 'medicine-templates.create', 'medicine-templates.edit', 'medicine-templates.delete',

            // ── Billing
            'invoices.index', 'invoices.create', 'invoices.edit', 'invoices.delete', 'invoices.issue',
            'payments.index', 'payments.create', 'payments.edit', 'payments.delete',

            // ── Departments, Rooms, Services
            'departments.index', 'departments.create', 'departments.edit', 'departments.delete',
            'treatments.index', 'treatments.create', 'treatments.edit', 'treatments.delete',
            'rooms.index', 'rooms.create', 'rooms.edit', 'rooms.delete',
            'services.index', 'services.create', 'services.edit', 'services.delete',

            // ── Inventory & Suppliers (← this is what you asked for)
            'inventory.index',
            'inventory-items.index', 'inventory-items.create', 'inventory-items.edit', 'inventory-items.delete',

            'suppliers.index', 'suppliers.create', 'suppliers.show', 'suppliers.edit', 'suppliers.delete',
            'categories.index', 'categories.create', 'categories.edit', 'categories.delete',
            'unit-measures.index', 'unit-measures.create', 'unit-measures.edit', 'unit-measures.delete',

            // ── Users & Roles
            'users.index', 'users.create', 'users.edit', 'users.delete',
            'roles.index', 'roles.create', 'roles.edit', 'roles.delete',

            // ── HR / Staff
            'staff.index', 'staff.create', 'staff.edit', 'staff.delete',
            'attendance.index', 'attendance.create', 'attendance.edit', 'attendance.delete',

            'leave-entitlements.index','leave-entitlements.create','leave-entitlements.update','leave-entitlements.delete',
            'leave-requests.index','leave-requests.create','leave-requests.update','leave-requests.delete',
            'leave-types.index','leave-types.create','leave-types.update','leave-types.delete',

            // ── Reports
            'reports.index',
            'reports.appointments', 'reports.financial', 'reports.patient-visits', 'reports.inventory',

            // ── Settings & Misc
            'settings.manage',

            // ── Patient-facing actions
            'appointments.book',
        ];

        // Create all permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ── Assign permissions to roles ───────────────────────────────────────

        // Super Admin - gets everything
        Role::firstOrCreate(['name' => 'admin'])
            ->syncPermissions(Permission::all()->pluck('name'));

        // Doctor
        Role::firstOrCreate(['name' => 'doctor'])->syncPermissions([
            'dashboard.doctor',
            'patients.index', 'patients.create', 'patients.show', 'patients.edit',
            'appointments.index', 'appointments.create', 'appointments.show', 'appointments.edit',
            'appointments.own', 'appointments.calendar',
            'prescriptions.index', 'prescriptions.create', 'prescriptions.edit',
            'medicine-templates.index', 'medicine-templates.create', 'medicine-templates.edit',
        ]);

        // Receptionist
        Role::firstOrCreate(['name' => 'receptionist'])->syncPermissions([
            'dashboard.receptionist',
            'patients.index', 'patients.create', 'patients.show', 'patients.edit',
            'appointments.index', 'appointments.create', 'appointments.edit', 'appointments.calendar',
            'invoices.index', 'invoices.create', 'invoices.issue',
            'payments.index', 'payments.create',
        ]);

        // Nurse
        Role::firstOrCreate(['name' => 'nurse'])->syncPermissions([
            'dashboard.nurse',
            'patients.index', 'patients.show',
            'appointments.index', 'appointments.own',
        ]);

        // HR
        Role::firstOrCreate(['name' => 'hr'])->syncPermissions([
            'dashboard.hr',
            'staff.index', 'staff.create', 'staff.edit', 'staff.delete',
            'attendance.index', 'attendance.create', 'attendance.edit', 'attendance.delete',

            'leave-requests.index',
            'users.index', 'users.create', 'users.edit',
            'roles.index', 'roles.create', 'roles.edit',
        ]);

        // Primary Care Provider
        Role::firstOrCreate(['name' => 'primary_care_provider'])->syncPermissions([
            'dashboard.primary_care_provider',
            'patients.index', 'patients.create', 'patients.show', 'patients.edit',
            'appointments.index', 'appointments.create', 'appointments.edit',
            'appointments.own', 'appointments.calendar',
            'prescriptions.index', 'prescriptions.create', 'prescriptions.edit',
            'medicine-templates.index',
            'appointments.book',
        ]);

        // Support (maintenance focused)
        Role::firstOrCreate(['name' => 'support'])->syncPermissions([
            'dashboard.support',
            'departments.index',
            'rooms.index', 'rooms.edit',   // e.g. update status/cleaning
        ]);
    }
}