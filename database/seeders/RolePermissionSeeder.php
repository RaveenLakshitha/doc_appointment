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

        // Granular permissions based on sidebar modules + role-specific dashboards
        $permissions = [
            // Role-specific dashboards (view only)
            'view admin dashboard',
            'view doctor dashboard',
            'view receptionist dashboard',
            'view nurse dashboard',
            'view hr dashboard',
            'view support dashboard',
            'view primary care provider dashboard',

            // Doctors & Specializations
            'view doctors', 'create doctors', 'edit doctors', 'delete doctors',
            'view doctor schedules', 'edit doctor schedules',
            'view specializations', 'create specializations', 'edit specializations', 'delete specializations',

            // Patients
            'view patients', 'create patients', 'edit patients', 'delete patients',

            // Appointments
            'view appointments', 'create appointments', 'edit appointments', 'delete appointments',
            'view own appointments', 'view appointment calendar', 'view appointment requests',

            // Prescriptions & Medicine Templates
            'view prescriptions', 'create prescriptions', 'edit prescriptions', 'delete prescriptions',
            'view medicine templates', 'create medicine templates', 'edit medicine templates', 'delete medicine templates',

            // Ambulance
            'view ambulance calls', 'create ambulance calls', 'edit ambulance calls', 'delete ambulance calls',
            'view ambulances', 'create ambulances', 'edit ambulances', 'delete ambulances',

            // Pharmacy / Medicines
            'view medicines', 'create medicines', 'edit medicines', 'delete medicines',

            // Billing (Invoices & Payments)
            'view invoices', 'create invoices', 'edit invoices', 'delete invoices', 'issue invoices',
            'view payments', 'create payments', 'edit payments', 'delete payments',

            // Departments & Services
            'view departments', 'create departments', 'edit departments', 'delete departments',
            'view rooms', 'create rooms', 'edit rooms', 'delete rooms',
            'view services', 'create services', 'edit services', 'delete services',

            // Inventory
            'view inventory', 'create inventory items', 'edit inventory items', 'delete inventory items',
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view unit of measures', 'create unit of measures', 'edit unit of measures', 'delete unit of measures',

            // Users & Roles
            'view users', 'create users', 'edit users', 'delete users',
            'view roles', 'create roles', 'edit roles', 'delete roles',

            // HR / Staff
            'view staff', 'create staff', 'edit staff', 'delete staff',
            'view attendance', 'manage attendance',
            'view timesheets', 'view leave requests',

            // Reports
            'view reports', 'view appointment reports', 'view financial reports',
            'view patient visit reports', 'view inventory reports',

            // Settings
            'manage settings',

            // Patient-specific
            'book appointment',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin gets everything (including all dashboards)
        Role::firstOrCreate(['name' => 'admin'])->syncPermissions(Permission::all());

        // Doctor - own dashboard + other permissions
        Role::firstOrCreate(['name' => 'doctor'])->syncPermissions([
            'view doctor dashboard',
            'view patients', 'create patients', 'edit patients',
            'view appointments', 'create appointments', 'edit appointments',
            'view own appointments', 'view appointment calendar',
            'view prescriptions', 'create prescriptions', 'edit prescriptions',
            'view medicine templates', 'create medicine templates', 'edit medicine templates',
        ]);

        // Receptionist
        Role::firstOrCreate(['name' => 'receptionist'])->syncPermissions([
            'view receptionist dashboard',
            'view patients', 'create patients', 'edit patients',
            'view appointments', 'create appointments', 'edit appointments', 'view appointment calendar',
            'view invoices', 'create invoices', 'issue invoices',
            'view payments', 'create payments',
        ]);

        // Nurse
        Role::firstOrCreate(['name' => 'nurse'])->syncPermissions([
            'view nurse dashboard',
            'view patients',
            'view appointments', 'view own appointments',
        ]);

        // HR
        Role::firstOrCreate(['name' => 'hr'])->syncPermissions([
            'view hr dashboard',
            'view staff', 'create staff', 'edit staff', 'delete staff',
            'view attendance', 'manage attendance',
            'view timesheets',
            'view leave requests',
            'view users', 'create users', 'edit users',
            'view roles', 'create roles', 'edit roles',
        ]);

        // Primary Care Provider
        Role::firstOrCreate(['name' => 'primary_care_provider'])->syncPermissions([
            'view primary care provider dashboard',
            'view patients', 'create patients', 'edit patients',
            'view appointments', 'create appointments', 'edit appointments',
            'view own appointments', 'view appointment calendar',
            'view prescriptions', 'create prescriptions', 'edit prescriptions',
            'view medicine templates',
            'book appointment',
        ]);

        // Support - room maintenance focused
        Role::firstOrCreate(['name' => 'support'])->syncPermissions([
            'view support dashboard',
            'view departments',
            'view rooms', 'edit rooms',  // For updating cleaning/maintenance status
        ]);
    }
}