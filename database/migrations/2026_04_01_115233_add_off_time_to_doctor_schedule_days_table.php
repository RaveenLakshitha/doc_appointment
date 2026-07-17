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
        Schema::table('doctor_schedule_days', function (Blueprint $table) {
            $table->time('off_time_start')->nullable()->after('end_time');
            $table->time('off_time_end')->nullable()->after('off_time_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_schedule_days', function (Blueprint $table) {
            $table->dropColumn(['off_time_start', 'off_time_end']);
        });
    }
};
