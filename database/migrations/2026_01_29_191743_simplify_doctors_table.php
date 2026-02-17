<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            // Make fields nullable / optional
            $table->string('middle_name')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('zip_code')->nullable()->change();

            $table->string('email')->nullable()->change();
            $table->string('phone')->nullable()->change();

            $table->string('emergency_contact_name')->nullable()->change();
            $table->string('emergency_contact_phone')->nullable()->change();

            $table->string('license_number')->nullable()->change();
            $table->date('license_expiry_date')->nullable()->change();

            $table->string('qualifications')->nullable()->change();
            $table->unsignedInteger('years_experience')->nullable()->default(0)->change();
            $table->text('education')->nullable()->change();
            $table->text('certifications')->nullable()->change();

            $table->string('profile_photo')->nullable()->change();

            // Add position_id if missing (assuming you want to switch from string 'position')
            if (!Schema::hasColumn('doctors', 'position_id')) {
                $table->foreignId('position_id')
                      ->nullable()
                      ->constrained('option_lists')
                      ->onDelete('set null');
            }

            // Ensure required fields stay NOT NULL
            // (first_name, last_name, gender, department_id, primary_specialization_id)
        });
    }

    public function down(): void
    {
        // Optional revert – usually not needed
    }
};