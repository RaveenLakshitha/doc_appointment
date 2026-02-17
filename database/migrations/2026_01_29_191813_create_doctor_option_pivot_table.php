<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->foreignId('option_id')->constrained('option_lists')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['doctor_id', 'option_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_option');
    }
};