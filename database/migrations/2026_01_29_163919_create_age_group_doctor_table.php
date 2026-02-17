<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('age_group_doctor', function (Blueprint $table) {
            $table->id();

            $table->foreignId('doctor_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('age_group_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['doctor_id', 'age_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('age_group_doctor');
    }
};