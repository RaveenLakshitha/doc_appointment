<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Get all existing equipment
        if (Schema::hasTable('equipment')) {
            $equipments = DB::table('equipment')->get();

            foreach ($equipments as $eq) {
                // Check if already migrated by name (to avoid duplicates if re-run)
                $optionId = DB::table('option_lists')
                    ->where('type', 'equipment')
                    ->where('name', $eq->name)
                    ->value('id');

                if (!$optionId) {
                    // 2. Insert into option_lists
                    $optionId = DB::table('option_lists')->insertGetId([
                        'type' => 'equipment',
                        'name' => $eq->name,
                        'status' => $eq->status === 'Operational',
                        'created_at' => $eq->created_at,
                        'updated_at' => $eq->updated_at,
                    ]);
                }

                // 3. Update equipment_service pivot table
                DB::table('equipment_service')
                    ->where('equipment_id', $eq->id)
                    ->update(['equipment_id' => $optionId]);
            }
        }

        // 4. Update the foreign key constraint on equipment_service
        if (Schema::hasTable('equipment_service')) {
            $foreignKeys = Schema::getForeignKeys('equipment_service');
            $fkExists = collect($foreignKeys)->contains(fn($fk) => $fk['columns'][0] === 'equipment_id' || $fk['name'] === 'equipment_service_equipment_id_foreign');

            Schema::table('equipment_service', function (Blueprint $table) use ($fkExists) {
                if ($fkExists) {
                    $table->dropForeign(['equipment_id']);
                }
                
                // Add new foreign key pointing to option_lists
                $table->foreign('equipment_id')
                    ->references('id')
                    ->on('option_lists')
                    ->onDelete('cascade');
            });
        }

        // 5. Drop the equipment table
        Schema::dropIfExists('equipment');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create equipment table
        if (!Schema::hasTable('equipment')) {
            Schema::create('equipment', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('status')->default('Operational');
                $table->date('last_maintenance')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Migrate back
        $options = DB::table('option_lists')->where('type', 'equipment')->get();

        foreach ($options as $opt) {
            $eqId = DB::table('equipment')->where('name', $opt->name)->value('id');
            
            if (!$eqId) {
                $eqId = DB::table('equipment')->insertGetId([
                    'name' => $opt->name,
                    'status' => $opt->status ? 'Operational' : 'Retired',
                    'created_at' => $opt->created_at,
                    'updated_at' => $opt->updated_at,
                ]);
            }

            DB::table('equipment_service')
                ->where('equipment_id', $opt->id)
                ->update(['equipment_id' => $eqId]);
        }

        // Update foreign key back
        if (Schema::hasTable('equipment_service')) {
            $foreignKeys = Schema::getForeignKeys('equipment_service');
            $fkExists = collect($foreignKeys)->contains(fn($fk) => $fk['columns'][0] === 'equipment_id');

            Schema::table('equipment_service', function (Blueprint $table) use ($fkExists) {
                if ($fkExists) {
                    $table->dropForeign(['equipment_id']);
                }
                $table->foreign('equipment_id')
                    ->references('id')
                    ->on('equipment')
                    ->onDelete('cascade');
            });
        }

        // Delete from option_lists
        DB::table('option_lists')->where('type', 'equipment')->delete();
    }
};
