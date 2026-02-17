<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_treatment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('treatment_id')->constrained()->cascadeOnDelete();
            
            // Doctor-specific price for this treatment
            $table->decimal('price', 10, 2)->nullable()->comment('Doctor-specific override price');
            
            $table->timestamps();

            $table->unique(['doctor_id', 'treatment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_treatment');
    }
};