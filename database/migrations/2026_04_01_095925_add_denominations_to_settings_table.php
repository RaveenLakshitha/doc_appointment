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
            $table->decimal('denomination_1', 10, 2)->nullable();
            $table->decimal('denomination_2', 10, 2)->nullable();
            $table->decimal('denomination_3', 10, 2)->nullable();
            $table->decimal('denomination_4', 10, 2)->nullable();
            $table->decimal('denomination_5', 10, 2)->nullable();
            $table->decimal('denomination_6', 10, 2)->nullable();
            $table->decimal('denomination_7', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'denomination_1',
                'denomination_2',
                'denomination_3',
                'denomination_4',
                'denomination_5',
                'denomination_6',
                'denomination_7',
            ]);
        });
    }
};
