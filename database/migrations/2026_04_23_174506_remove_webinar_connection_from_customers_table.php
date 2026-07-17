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
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (Schema::hasColumn('customers', 'webinar_id')) {
                    $table->dropForeign(['webinar_id']);
                    $table->dropColumn('webinar_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (!Schema::hasColumn('customers', 'webinar_id')) {
                    $table->unsignedBigInteger('webinar_id')->nullable();
                    $table->foreign('webinar_id')->references('id')->on('webinars')->nullOnDelete();
                }
            });
        }
    }
};
