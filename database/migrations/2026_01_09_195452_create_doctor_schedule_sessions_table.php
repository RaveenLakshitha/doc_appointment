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
            Schema::create('doctor_schedule_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_schedule_id')->constrained()->cascadeOnDelete();
            $table->string('name')->comment('Morning OPD, Evening Clinic, etc');
            $table->string('key', 40)->index()->comment('morning, evening, night, special');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedTinyInteger('slot_duration_minutes')->default(15);
            $table->unsignedSmallInteger('max_patients')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_schedule_sessions');
    }
};
