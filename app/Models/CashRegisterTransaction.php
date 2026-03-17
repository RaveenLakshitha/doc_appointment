<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRegisterTransaction extends Model
{
    protected $fillable = [
        'cash_register_id',
        'user_id',
        'billing_invoice_id',
        'payment_id',
        'expense_id',
        'purchase_id',
        'type',
        'payment_method',
        'amount',
        'happened_at',
        'notes',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'happened_at' => 'datetime',
    ];

    // ────────────────────────────────────────────────
    //  Relationships
    // ────────────────────────────────────────────────

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(BillingInvoice::class, 'billing_invoice_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    // ────────────────────────────────────────────────
    //  Helpers
    // ────────────────────────────────────────────────

    public function isCashMovement(): bool
    {
        return in_array($this->payment_method, ['cash', 'cash_hand']);
    }

    public function isInflow(): bool
    {
        return in_array($this->type, ['cash_sale', 'payment_received', 'cash_in', 'correction_in']);
    }

    public function isOutflow(): bool
    {
        return in_array($this->type, ['refund', 'cash_out', 'petty_cash', 'correction_out', 'expense', 'purchase']);
    }
}