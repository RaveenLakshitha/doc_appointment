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
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('age_group_id')->nullable()->constrained('age_groups')->nullOnDelete();
            $table->foreignId('preferred_language_id')->nullable()->constrained('option_lists')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['age_group_id']);
            $table->dropColumn('age_group_id');
            $table->dropForeign(['preferred_language_id']);
            $table->dropColumn('preferred_language_id');
        });
    }
};
