<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('option_lists', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();        // e.g., 'doctor_position', 'patient_status', 'room_type', etc.
            $table->string('name');                 // the display name
            $table->string('slug')->nullable();     // optional, auto-generated
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique(['type', 'name']);       // prevent duplicates per type
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('option_lists');
    }
};
