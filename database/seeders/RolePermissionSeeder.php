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

        $permissions = [
            'view dashboard',
            'manage users',
            'manage doctors',
            'manage specializations',
            'manage patients',
            'manage appointments',
            'view appointments',
            'view own appointments',
            'manage prescriptions',
            'create prescription',
            'manage medicine templates',
            'manage ambulance calls',
            'manage ambulances',
            'manage pharmacy',
            'manage invoices',
            'create invoice',
            'issue invoice',
            'manage payments',
            'manage departments',
            'manage services',
            'manage inventory',
            'manage suppliers',
            'manage categories',
            'manage unit of measures',
            'manage staff',
            'manage roles and permissions',
            'manage attendance',
            'view reports',
            'manage settings',
            'book appointment',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        Role::firstOrCreate(['name' => 'admin'])->syncPermissions(Permission::all());

        Role::firstOrCreate(['name' => 'doctor'])->syncPermissions([
            'view dashboard', 'manage patients', 'manage appointments', 'view appointments',
            'view own appointments', 'manage prescriptions', 'create prescription', 'manage medicine templates'
        ]);

        Role::firstOrCreate(['name' => 'receptionist'])->syncPermissions([
            'view dashboard', 'manage patients', 'manage appointments', 'view appointments',
            'manage invoices', 'create invoice', 'issue invoice', 'manage payments'
        ]);

        Role::firstOrCreate(['name' => 'nurse'])->syncPermissions([
            'view dashboard', 'manage patients', 'view appointments'
        ]);

        Role::firstOrCreate(['name' => 'hr'])->syncPermissions([
            'view dashboard', 'manage staff', 'manage attendance', 'manage roles and permissions'
        ]);

        Role::firstOrCreate(['name' => 'patient'])->syncPermissions([
            'book appointment', 'view own appointments'
        ]);
    }
}