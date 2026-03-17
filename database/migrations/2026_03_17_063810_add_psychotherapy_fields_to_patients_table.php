<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->boolean('attended_psychotherapy')->default(false)->nullable();
            $table->string('preferred_session_time')->nullable();
            $table->string('recommended_by')->nullable();
            $table->string('recommended_document')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'attended_psychotherapy',
                'preferred_session_time',
                'recommended_by',
                'recommended_document'
            ]);
        });
    }
};
