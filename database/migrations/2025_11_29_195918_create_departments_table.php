<?php
// 2024_01_01_000001_create_departments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('location')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            
            // head_doctor_id will be added in a separate migration after doctors table exists
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};