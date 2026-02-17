<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('employee_leave_entitlements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('employee_id')->constrained()->onDelete('cascade');
        $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
        $table->year('year');                           
        $table->decimal('entitled_days', 5, 2)->default(0);   
        $table->decimal('used_days', 5, 2)->default(0);
        $table->decimal('remaining_days', 5, 2)->virtualAs('entitled_days - used_days'); 
        $table->decimal('accrual_rate', 4, 2)->nullable();    
        $table->date('last_accrued_at')->nullable();
        $table->text('notes')->nullable();

        // ← This is the fixed line
        $table->unique(
            ['employee_id', 'leave_type_id', 'year'],
            'emp_leave_ent_year_unique'
        );

        $table->timestamps();
        $table->softDeletes();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('employee_leave_entitlements');
    }
};