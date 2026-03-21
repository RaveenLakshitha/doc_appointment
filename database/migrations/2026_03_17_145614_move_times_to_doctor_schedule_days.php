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
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
        });

        // Migrate existing data
        \Illuminate\Support\Facades\DB::statement('
            UPDATE doctor_schedule_days dsd
            JOIN doctor_schedules ds ON ds.id = dsd.doctor_schedule_id
            SET dsd.start_time = ds.start_time,
                dsd.end_time = ds.end_time
        ');

        Schema::table('doctor_schedule_days', function (Blueprint $table) {
            $table->time('start_time')->nullable(false)->change();
            $table->time('end_time')->nullable(false)->change();
        });

        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropUnique('doctor_sched_time_unique');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->dropColumn(['start_time', 'end_time']);
        });
    }

    public function down(): void
    {
        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
        });

        \Illuminate\Support\Facades\DB::statement('
            UPDATE doctor_schedules ds
            JOIN (
                SELECT doctor_schedule_id, MAX(start_time) as start_time, MAX(end_time) as end_time 
                FROM doctor_schedule_days 
                GROUP BY doctor_schedule_id
            ) dsd ON dsd.doctor_schedule_id = ds.id
            SET ds.start_time = dsd.start_time,
                ds.end_time = dsd.end_time
        ');

        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->time('start_time')->nullable(false)->change();
            $table->time('end_time')->nullable(false)->change();
            $table->unique(['doctor_id', 'start_time', 'end_time'], 'doctor_sched_time_unique');
        });

        Schema::table('doctor_schedule_days', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });
    }
};
