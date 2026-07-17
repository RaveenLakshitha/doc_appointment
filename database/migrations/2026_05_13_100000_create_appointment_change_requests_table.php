<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_change_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('appointment_id')
                  ->constrained('appointments')
                  ->cascadeOnDelete();

            $table->foreignId('patient_id')
                  ->constrained('patients')
                  ->cascadeOnDelete();

            // 'reschedule' | 'cancel'
            $table->enum('request_type', ['reschedule', 'cancel']);

            // 'pending' | 'approved' | 'rejected'
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Patient-provided fields
            $table->text('reason');
            $table->date('requested_date')->nullable();          // for reschedule
            $table->string('requested_time')->nullable();        // for reschedule (HH:MM)
            $table->string('preferred_time')->nullable();        // morning | evening

            // Admin/staff response
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_change_requests');
    }
};
