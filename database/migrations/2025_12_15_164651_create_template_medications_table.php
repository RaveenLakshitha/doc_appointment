<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_template_id')->constrained('medicine_templates')->onDelete('cascade');
            $table->string('name');
            $table->string('dosage');
            $table->string('route')->default('Oral');
            $table->string('frequency');
            $table->text('instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_medications');
    }
};