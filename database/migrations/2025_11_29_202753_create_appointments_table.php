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

            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');

            $table->dateTime('appointment_datetime');
            $table->unsignedSmallInteger('duration_minutes')->default(30);

            $table->enum('status', [
                'scheduled',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled',
                'no_show',
                'rescheduled'
            ])->default('scheduled');

            $table->string('appointment_type')->nullable();
            $table->text('reason_for_visit')->nullable();

            // These are the correct note columns — no generic "notes" column
            $table->text('doctor_notes')->nullable();
            $table->text('patient_notes')->nullable();
            $table->text('admin_notes')->nullable();

            $table->dateTime('cancelled_at')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('appointment_datetime');
            $table->index('status');
            $table->index(['doctor_id', 'appointment_datetime']);
            $table->index(['patient_id', 'appointment_datetime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};