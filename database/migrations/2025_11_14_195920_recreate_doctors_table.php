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
            $table->string('gender');               // male|female|other
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();

            $table->string('email')->unique();
            $table->string('phone');
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            // Professional
            $table->string('primary_specialty');
            $table->string('secondary_specialty')->nullable();
            $table->string('license_number')->unique();
            $table->date('license_expiry_date');
            $table->string('qualifications')->nullable();
            $table->unsignedInteger('years_experience');
            $table->text('education')->nullable();
            $table->text('certifications')->nullable();
            $table->string('department');
            $table->string('position');
            $table->decimal('hourly_rate', 8, 2);

            $table->string('profile_photo')->nullable();

            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
