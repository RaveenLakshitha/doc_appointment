<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('billing_invoices')->onDelete('cascade');

            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('method')->nullable(); // Cash, Card, Bank Transfer, Insurance, etc.
            $table->string('reference')->nullable(); // Transaction ID, Check No.
            $table->text('notes')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
