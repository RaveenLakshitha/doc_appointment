<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the services table
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('type'); // e.g., Diagnostic, Therapeutic
            $table->integer('duration_minutes');
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->text('patient_preparation')->nullable();
            $table->boolean('requires_insurance')->default(false);
            $table->boolean('requires_referral')->default(false);
            $table->timestamps();
        });

        Schema::create('doctor_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['service_id', 'doctor_id']);
        });

        // Create equipment table
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('Operational');
            $table->date('last_maintenance')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Pivot table for many-to-many relationship between services and equipment
        Schema::create('equipment_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['service_id', 'equipment_id']);
        });

        Schema::create('service_availability_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            // Prevents duplicate exact slots (recommended)
            $table->unique(
                ['service_id', 'day_of_week', 'start_time'],
                'slot_service_day_start_unique'
            );

            // Optional: index for faster queries
            $table->index(['service_id', 'day_of_week']);
        });
            }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_availability_slots');
        Schema::dropIfExists('equipment_service');
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('doctor_service');
        Schema::dropIfExists('services');
    }
};