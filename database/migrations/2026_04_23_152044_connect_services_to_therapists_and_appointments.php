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
        // Add price to doctor_service for doctor-specific override
        Schema::table('doctor_service', function (Blueprint $table) {
            if (!Schema::hasColumn('doctor_service', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('doctor_id');
            }
        });

        // Create appointment_service pivot table
        Schema::create('appointment_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('price_at_time', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['appointment_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_service');
        
        Schema::table('doctor_service', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
