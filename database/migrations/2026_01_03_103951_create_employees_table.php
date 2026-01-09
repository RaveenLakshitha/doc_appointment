<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            // Link to User account (optional)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('photo')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            // Professional Information
            $table->string('profession')->nullable();
            $table->string('specialization')->nullable();
            $table->text('professional_bio')->nullable();

            // Qualifications (now embedded)
            $table->string('degree')->nullable();
            $table->string('institution')->nullable();
            $table->year('year_completed')->nullable();

            // Licenses (now embedded)
            $table->string('license_type')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_issue_date')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->string('license_issuing_authority')->nullable();

            // Employment Details
            $table->string('employee_code')->unique();
            $table->foreignId('department_id')->constrained()->onDelete('restrict');
            $table->string('position');
            $table->foreignId('reporting_to')->nullable()->constrained('employees')->onDelete('set null');
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('work_schedule')->nullable();
            $table->unsignedInteger('work_hours_weekly')->nullable();
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->text('contract_notes')->nullable();

            // Compensation & Benefits
            $table->decimal('salary', 12, 2)->nullable();
            $table->string('payment_frequency')->nullable();

            // Status
            $table->boolean('status')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};