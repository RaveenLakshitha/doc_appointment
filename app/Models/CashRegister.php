<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\SoftDeletes;

class CashRegister extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'opening_balance',
        'expected_closing_balance',
        'actual_closing_balance',
        'difference',
        'opened_at',
        'closed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'opening_balance'          => 'decimal:2',
        'expected_closing_balance' => 'decimal:2',
        'actual_closing_balance'   => 'decimal:2',
        'difference'               => 'decimal:2',
        'opened_at'                => 'datetime',
        'closed_at'                => 'datetime',
    ];

    // ────────────────────────────────────────────────
    //  Relationships
    // ────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashRegisterTransaction::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(BillingInvoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    // ────────────────────────────────────────────────
    //  Helpers & Business Logic
    // ────────────────────────────────────────────────

    public function isOpen(): bool
    {
        return $this->status === 'open' || $this->closed_at === null;
    }

    /**
     * Calculate what the closing balance *should* be based on recorded cash movements
     */
    public function calculateExpectedClosingBalance(): float
    {
        $cashIn = $this->transactions()
            ->whereIn('type', ['cash_sale', 'payment_received', 'cash_in', 'correction_in'])
            ->sum('amount');

        $cashOut = $this->transactions()
            ->whereIn('type', ['refund', 'cash_out', 'petty_cash', 'correction_out', 'expense', 'purchase'])
            ->sum('amount');

        return round($this->opening_balance + $cashIn - $cashOut, 2);
    }

    public function hasDiscrepancy(): bool
    {
        if (!$this->actual_closing_balance) {
            return false;
        }

        return abs($this->actual_closing_balance - $this->expected_closing_balance) > 0.01;
    }
}