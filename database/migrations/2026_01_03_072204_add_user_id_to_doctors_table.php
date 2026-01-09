<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->nullable()->constrained('users')->onDelete('cascade');
            $table->unique('user_id'); // Ensures one-to-one (optional but recommended)
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
