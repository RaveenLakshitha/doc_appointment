<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_name');
            $table->string('clinic_id')->unique();
            $table->text('address');
            $table->string('email');
            $table->string('phone');
            $table->string('website')->nullable();
            $table->string('tax_id')->nullable();

            // Operating Hours (stored as JSON)
            $table->json('operating_hours')->nullable();

            // Regional Settings
            $table->string('timezone');
            $table->string('date_format');
            $table->string('time_format');
            $table->string('first_day_of_week');
            $table->string('language')->default('en');

            // Branding
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('primary_color')->default('#1e40af');
            $table->string('secondary_color')->nullable();

            $table->timestamps();
        });

        // Insert default clinic data
        DB::table('settings')->insert([
            'clinic_name'       => 'MedicOS Clinic',
            'clinic_id'         => 'MC-12345-XYZ',
            'address'           => '123 Medical Plaza, Healthcare District, City, State, 12345',
            'email'             => 'contact@medicos-clinic.com',
            'phone'             => '+1 (555) 123-4567',
            'website'           => 'https://medicos-clinic.com',
            'tax_id'            => 'TAX-987654321',
            'operating_hours'   => json_encode([
                'weekdays' => ['08:00', '18:00'],
                'weekends' => ['closed', 'closed'],
            ]),
            'timezone'          => 'America/New_York',
            'date_format'       => 'MM/DD/YYYY',
            'time_format'       => '12-hour',
            'first_day_of_week' => 'Sunday',
            'language'          => 'en',
            'primary_color'     => '#1e40af',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};