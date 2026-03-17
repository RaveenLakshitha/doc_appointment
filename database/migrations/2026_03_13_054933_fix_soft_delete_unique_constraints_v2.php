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
        try { \Illuminate\Support\Facades\DB::statement("ALTER TABLE departments DROP INDEX departments_name_unique"); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement("ALTER TABLE departments DROP INDEX name"); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement("ALTER TABLE departments ADD INDEX departments_name_index (name)"); } catch (\Exception $e) {}

        try { \Illuminate\Support\Facades\DB::statement("ALTER TABLE patients DROP INDEX patients_email_unique"); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement("ALTER TABLE patients DROP INDEX patients_phone_unique"); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement("ALTER TABLE patients ADD INDEX patients_email_index (email)"); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement("ALTER TABLE patients ADD INDEX patients_phone_index (phone)"); } catch (\Exception $e) {}

        try { \Illuminate\Support\Facades\DB::statement("ALTER TABLE doctors DROP INDEX doctors_email_unique"); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement("ALTER TABLE doctors DROP INDEX doctors_license_number_unique"); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement("ALTER TABLE doctors ADD INDEX doctors_email_index (email)"); } catch (\Exception $e) {}
        try { \Illuminate\Support\Facades\DB::statement("ALTER TABLE doctors ADD INDEX doctors_license_number_index (license_number)"); } catch (\Exception $e) {}
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->unique(['name']);
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
            $table->unique(['email']);
            $table->unique(['phone']);
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropIndex(['email']);
            if (collect(DB::select("SHOW INDEXES FROM departments"))->pluck('Key_name')->contains('departments_name_unique')) {
                $table->dropIndex('departments_name_unique');
            }
            $table->dropIndex(['license_number']);
            $table->unique(['email']);
            $table->unique(['license_number']);
        });
    }
};
