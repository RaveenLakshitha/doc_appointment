<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();

            $table->string('email')->unique();
            $table->string('phone');
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            $table->string('license_number')->unique();
            $table->date('license_expiry_date');
            $table->string('qualifications')->nullable();
            $table->unsignedInteger('years_experience')->default(0);
            $table->text('education')->nullable();
            $table->text('certifications')->nullable();
            $table->string('position');
            $table->decimal('hourly_rate', 10, 2);

            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('primary_specialization_id')->nullable()->constrained('specializations')->onDelete('set null');

            $table->string('profile_photo')->nullable();
            $table->boolean('is_active')->default(true);

            $table->softDeletes();
            $table->timestamps();

            // Indexes (added here = no duplicate key error ever)
            $table->index(['last_name', 'first_name']);                    // Search by name
            $table->index('is_active');                                    // Filter active doctors
            $table->index('department_id');                                // Doctors by department
            $table->index('primary_specialization_id');                    // Doctors by specialization
            $table->index('deleted_at');                                   // Soft delete performance
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};