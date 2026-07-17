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
        if (!Schema::hasColumn('doctors', 'position_id')) {
            Schema::table('doctors', function (Blueprint $table) {
                // Determine 'after' column
                $afterColumn = Schema::hasColumn('doctors', 'position') ? 'position' : 'department_id';
                
                $table->foreignId('position_id')
                      ->nullable()
                      ->after($afterColumn)
                      ->constrained('option_lists')
                      ->onDelete('set null');
            });
            
            // Migrate data from 'position' string to 'position_id'
            if (Schema::hasColumn('doctors', 'position')) {
                $doctors = DB::table('doctors')->whereNotNull('position')->get(['id', 'position']);
                foreach ($doctors as $doctor) {
                    $option = DB::table('option_lists')
                        ->where('type', 'doctor_position')
                        ->where('name', $doctor->position)
                        ->first(['id']);
                    
                    if ($option) {
                        DB::table('doctors')->where('id', $doctor->id)->update(['position_id' => $option->id]);
                    }
                }
                
                // Now drop the old column
                Schema::table('doctors', function (Blueprint $table) {
                    $table->dropColumn('position');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            if (Schema::hasColumn('doctors', 'position_id')) {
                $table->dropForeign(['position_id']);
                $table->dropColumn('position_id');
            }
            if (!Schema::hasColumn('doctors', 'position')) {
                $table->string('position')->nullable();
            }
        });
    }
};
