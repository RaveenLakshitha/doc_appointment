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
            ['name' => 'Richard Collins', 'phone' => '555-0101'],
            ['name' => 'Andrew Hughes', 'phone' => '555-0102'],
            ['name' => 'Crystal Taylor', 'phone' => '555-0103'],
            ['name' => 'Oliver Davis', 'phone' => '555-0104'],
            ['name' => 'Amanda Cooper', 'phone' => '555-0105'],
            ['name' => 'Caroline Garcia', 'phone' => '555-0106'],
            ['name' => 'Melissa Porter', 'phone' => '555-0107'],
            ['name' => 'Catherine Allens', 'phone' => '555-0108'],
            ['name' => 'Rosemary Gomez', 'phone' => '555-0109'],
            ['name' => 'Anthony Brown', 'phone' => '555-0110'],
            ['name' => 'Alexandra Miller', 'phone' => '555-0111'],
            ['name' => 'Charles Baker', 'phone' => '555-0112'],
        ];

        foreach ($therapists as $therapist) {
            $email = Str::slug($therapist['name'], '.') . '@example.com';

            $user = User::withTrashed()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $therapist['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $therapist['phone'],
                    'is_active' => true,
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