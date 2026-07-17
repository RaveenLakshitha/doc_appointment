<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 3. Doctors
        if (!Schema::hasColumn('doctors', 'position_id')) {
            Schema::table('doctors', function (Blueprint $table) {
                $table->foreignId('position_id')->nullable()->after('position')->constrained('option_lists')->nullOnDelete();
            });
            $doctors = \DB::table('doctors')->get(['id', 'position']);
            foreach ($doctors as $doctor) {
                if ($doctor->position) {
                    $option = \DB::table('option_lists')
                        ->where('name', $doctor->position)
                        ->where('type', 'doctor_position')
                        ->first(['id']);
                    if ($option) {
                        \DB::table('doctors')->where('id', $doctor->id)->update(['position_id' => $option->id]);
                    }
                }
            }
        }
        if (Schema::hasColumn('doctors', 'position')) {
            Schema::table('doctors', function (Blueprint $table) {
                $table->dropColumn('position');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

