<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Appointment; // For constants

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('patient_id')
                  ->constrained('patients')
                  ->cascadeOnDelete();

            $table->foreignId('doctor_id')
                  ->nullable()
                  ->constrained('doctors')
                  ->nullOnDelete();

            $table->foreignId('room_id')
                  ->nullable()
                  ->constrained('rooms')
                  ->nullOnDelete();

            $table->foreignId('cancelled_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Appointment details
            $table->string('status')
                  ->default(Appointment::STATUS_PENDING)
                  ->index();

            $table->string('appointment_type')
                  ->index();

            $table->text('reason_for_visit')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->text('patient_notes')->nullable();
            $table->text('admin_notes')->nullable();

            // Scheduled time (actual appointment slot)
            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end');

            // Cancellation info
            $table->dateTime('cancelled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Composite indexes for performance
            $table->index(['doctor_id', 'scheduled_start']);
            $table->index(['patient_id', 'scheduled_start']);
            $table->index('scheduled_start'); // useful for upcoming appointments
            $table->index(['status', 'scheduled_start']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};