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
        Schema::create('whatsapp_appointment_requests', function (Blueprint $table) {
    $table->id();
    $table->string('flow_token')->nullable();
    $table->string('whatsapp_timestamp');
    $table->string('appointment_type');     // specific / any
    $table->string('visit_type');           // first / followup
    $table->string('preferred_time');       // next / 7days / 15days
    $table->text('reason');
    $table->text('notes')->nullable();
    $table->foreignId('doctor_id')->nullable();
    $table->foreignId('specialization_id')->nullable();
    $table->foreignId('patient_id')->nullable();           // if existing
    $table->json('new_patient_data')->nullable();          // first_name, phone, etc.
    $table->string('status')->default('pending');
    $table->string('source')->default('whatsapp_flow');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
