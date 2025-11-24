<?php
// database/migrations/2025_11_22_000003_create_template_medications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_template_id')
                  ->constrained('medication_templates')
                  ->cascadeOnDelete();

            $table->string('name');
            $table->string('dosage')->nullable();
            $table->string('route')->default('Oral');
            $table->string('frequency');
            $table->text('instructions')->nullable();
            $table->string('duration')->nullable();
            $table->unsignedSmallInteger('order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['medication_template_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_medications');
    }
};