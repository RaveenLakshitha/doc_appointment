<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_schedule_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_schedule_id')->constrained()->onDelete('cascade');
            $table->string('day_of_week'); // e.g., 'monday', 'tuesday', etc. (lowercase)
            $table->timestamps();

            // Ensure same day is not added twice to the same schedule
            $table->unique(['doctor_schedule_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_schedule_days');
    }
};