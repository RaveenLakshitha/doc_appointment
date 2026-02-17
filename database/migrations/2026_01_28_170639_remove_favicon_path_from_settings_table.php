<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Drop the column if it exists
            $table->dropColumn('favicon_path');
            $table->dropColumn('operating_hours');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Recreate it in case you need to rollback
            $table->string('favicon_path')->nullable()->after('logo_path');
            $table->json('operating_hours')->nullable()->after('tax_id');
        });
    }
};