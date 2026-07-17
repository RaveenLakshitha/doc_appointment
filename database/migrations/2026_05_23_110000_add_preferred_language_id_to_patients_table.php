<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Safely adds preferred_language_id to patients if it doesn't already exist.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('patients', 'preferred_language_id')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->foreignId('preferred_language_id')
                      ->nullable()
                      ->after('preferred_language')
                      ->constrained('option_lists')
                      ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('patients', 'preferred_language_id')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropForeign(['preferred_language_id']);
                $table->dropColumn('preferred_language_id');
            });
        }
    }
};
