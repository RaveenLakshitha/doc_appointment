<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class BillingInvoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'patient_id',
        'invoice_date',
        'due_date',
        'type',
        'reference_po',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total',
        'paid_amount',
        'balance_due',
        'status',
        'notes',
    ];

    protected $casts = [
        'invoice_date'    => 'date',
        'due_date'        => 'date',
        'subtotal'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total'           => 'decimal:2',
        'paid_amount'     => 'decimal:2',
        'balance_due'     => 'decimal:2',
    ];

    protected $attributes = [
        'paid_amount' => 0,
        'balance_due' => 0,
        'status'      => 'sent',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillingInvoiceItem::class, 'invoice_id');
        // Explicit foreign key to avoid Laravel guessing 'billing_invoice_id'
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    // Accessors
    public function getIsPaidAttribute(): bool
    {
        return $this->balance_due <= 0;
    }

    public function getIsPartiallyPaidAttribute(): bool
    {
        return $this->paid_amount > 0 && $this->balance_due > 0;
    }

    public function getIsOverdueAttribute(): bool
    {
        return !$this->is_paid && $this->due_date < Carbon::today();
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
                     ->where('due_date', '<', Carbon::today());
    }
}