<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->foreignId('primary_specialization_id')->nullable()->after('primary_specialty')->constrained('specializations')->onDelete('set null');
        });
    }
    public function down()
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropForeign(['primary_specialization_id']);
            $table->dropColumn('primary_specialization_id');
        });
    }
};
