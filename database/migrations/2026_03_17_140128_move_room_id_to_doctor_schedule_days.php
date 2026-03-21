<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_schedule_days', function (Blueprint $table) {
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('cascade');
        });

        DB::statement('
            UPDATE doctor_schedule_days dsd
            JOIN doctor_schedules ds ON ds.id = dsd.doctor_schedule_id
            SET dsd.room_id = ds.room_id
        ');

        Schema::table('doctor_schedule_days', function (Blueprint $table) {
            $table->unsignedBigInteger('room_id')->nullable(false)->change();
        });

        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
        });
    }

    public function down(): void
    {
        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('cascade');
        });

        DB::statement('
            UPDATE doctor_schedules ds
            JOIN (
                SELECT doctor_schedule_id, MAX(room_id) as room_id 
                FROM doctor_schedule_days 
                GROUP BY doctor_schedule_id
            ) dsd ON dsd.doctor_schedule_id = ds.id
            SET ds.room_id = dsd.room_id
        ');

        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('room_id')->nullable(false)->change();
        });

        Schema::table('doctor_schedule_days', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
        });
    }
};
