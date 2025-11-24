<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('patient_id')
                  ->constrained('patients')
                  ->onDelete('cascade');

            $table->foreignId('doctor_id')
                  ->constrained('doctors')
                  ->onDelete('cascade');

            // Appointment Details
            $table->dateTime('appointment_datetime');                    // e.g. 2025-11-17 14:30:00
            $table->unsignedSmallInteger('duration_minutes')->default(60); // 30, 45, 60, 90 etc.
            
            // Status: scheduled, tentative, waitlist, completed, cancelled, no_show
            $table->enum('status', ['scheduled', 'tentative', 'waitlist', 'completed', 'cancelled', 'no_show'])
                  ->default('scheduled');

            // New fields from your model
            $table->string('appointment_type')->nullable();  // consultation, follow_up, procedure, checkup, etc.
            $table->text('reason_for_visit')->nullable();    // Patient's reason (required in form)

            // Optional notes
            $table->text('notes')->nullable();

            // Timestamps & Soft Delete
            $table->timestamps();
            $table->softDeletes(); // deleted_at column
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};