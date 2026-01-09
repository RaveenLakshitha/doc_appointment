<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->foreignId('position_id')->nullable()->after('department_id')->constrained('option_lists');
            $table->dropColumn('position');
        });
    }

    public function down()
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('position')->nullable()->after('department_id');
            $table->dropConstrainedForeignId('position_id');
        });
    }
};