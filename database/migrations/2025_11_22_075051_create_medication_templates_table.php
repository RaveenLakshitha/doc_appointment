<?php
// database/migrations/2025_11_22_000002_create_medication_templates_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('medication_template_categories')
                  ->onDelete('set null');
            $table->text('description')->nullable();
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->timestamp('last_used_at')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('created_by');
            $table->index('last_used_at');
            $table->index('usage_count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_templates');
    }
};