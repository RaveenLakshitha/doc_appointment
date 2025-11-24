<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'manage users',
            'view dashboard',
        ];

        foreach ($permissions as $perm) {
            Permission::create(['name' => $perm]);
        }

        // Create Roles and Assign Permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $editor = Role::create(['name' => 'editor']);
        $editor->givePermissionTo(['view posts', 'create posts', 'edit posts']);

        $viewer = Role::create(['name' => 'viewer']);
        $viewer->givePermissionTo('view posts');
    }
}
