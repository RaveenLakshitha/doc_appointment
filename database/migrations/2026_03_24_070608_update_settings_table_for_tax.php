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
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['date_format', 'time_format']);
            $table->decimal('tax_percentage', 5, 2)->default(0.00)->after('timezone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('date_format')->default('YYYY-MM-DD');
            $table->string('time_format')->default('12-hour');
            $table->dropColumn('tax_percentage');
        });
    }
};
