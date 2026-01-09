<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Basic Information - added after existing columns
            $table->string('room_type')->nullable()->after('room_number');
            $table->string('floor')->nullable()->after('department_id');

            // Additional Details
            $table->unsignedTinyInteger('capacity')->nullable()->after('name'); // 1–255, sufficient for beds
            $table->decimal('price_per_day', 10, 2)->nullable()->after('capacity'); // e.g., 250.00 USD
            $table->json('facilities')->nullable()->after('description'); // stores array of selected facilities
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'room_type',
                'floor',
                'capacity',
                'price_per_day',
                'facilities'
            ]);
        });
    }
};
