<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::unguard();

        $therapists = [
            ['name' => 'Ricardo Contreras', 'phone' => '555-0101'],
            ['name' => 'Andros Hangis', 'phone' => '555-0102'],
            ['name' => 'Kristal Aguilera', 'phone' => '555-0103'],
            ['name' => 'Octavio Dominguez', 'phone' => '555-0104'],
            ['name' => 'Arantza Corral', 'phone' => '555-0105'],
            ['name' => 'Carolina Garcia', 'phone' => '555-0106'],
            ['name' => 'Mercedes Porras', 'phone' => '555-0107'],
            ['name' => 'Carolina Alvarez', 'phone' => '555-0108'],
            ['name' => 'Rosalinda Gamez', 'phone' => '555-0109'],
            ['name' => 'Andres Bravo', 'phone' => '555-0110'],
            ['name' => 'Alejandra Medina', 'phone' => '555-0111'],
            ['name' => 'Carlos Bush', 'phone' => '555-0112'],
        ];

        foreach ($therapists as $therapist) {
            $email = Str::slug($therapist['name'], '.') . '@example.com';

            $user = User::withTrashed()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $therapist['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $therapist['phone'],
                    'is_active' => true, // Based on "Activo" status in document 
                    'is_deleted' => false,
                    'deleted_at' => null,
                ]
            );
            
            // Assign therapist/doctor role if it exists
            if (Role::where('name', 'doctor')->exists()) {
                $user->assignRole('doctor');
            }
        }

        User::reguard();
    }
}