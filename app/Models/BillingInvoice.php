<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class BillingInvoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'patient_id',
        'invoice_date',
        'due_date',
        'appointment_id',
        'cash_register_id',
        'type',               // e.g. 'POS', 'Appointment', 'Insurance', 'Manual'
        'reference_po',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total',
        'paid_amount',
        'balance_due',
        'status',             // pending, partially_paid, paid, held, cancelled, refunded, etc.
        'notes',
        'is_printed',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Default values when creating new instance
    protected $attributes = [
        'paid_amount' => 0.00,
        'balance_due' => 0.00,
        'status' => 'pending',     // changed from 'sent' → more suitable for POS flow
    ];

    // ────────────────────────────────────────────────
    //  Relationships
    // ────────────────────────────────────────────────

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillingInvoiceItem::class, 'invoice_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    // ────────────────────────────────────────────────
    //  Accessors & Mutators
    // ────────────────────────────────────────────────

    public function getIsPaidAttribute(): bool
    {
        return $this->balance_due <= 0.01; // small tolerance for rounding errors
    }

    public function getIsPartiallyPaidAttribute(): bool
    {
        return $this->paid_amount > 0 && $this->balance_due > 0.01;
    }

    public function getIsOverdueAttribute(): bool
    {
        return !$this->is_paid && $this->due_date?->isPast();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'Paid',
            'partially_paid' => 'Partially Paid',
            'pending' => 'Pending',
            'held' => 'On Hold',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
            default => ucfirst($this->status ?? 'Unknown'),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'green',
            'partially_paid' => 'blue',
            'pending' => 'yellow',
            'held' => 'orange',
            'cancelled' => 'red',
            'refunded' => 'purple',
            default => 'gray',
        };
    }

    // ────────────────────────────────────────────────
    //  Scopes
    // ────────────────────────────────────────────────

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
            ->where('due_date', '<', Carbon::today());
    }

    public function scopePos($query)
    {
        return $query->where('type', 'POS');
    }

    public function scopePendingOrPartial($query)
    {
        return $query->whereIn('status', ['pending', 'partially_paid']);
    }

    // ────────────────────────────────────────────────
    //  Business Logic Helpers
    // ────────────────────────────────────────────────

    /**
     * Recalculate paid_amount from payments and update balance + status
     */
    public function refreshPaymentStatus(): void
    {
        $this->paid_amount = $this->payments()
            ->where('status', '!=', 'failed') // if you add payment status later
            ->sum('amount');

        $this->balance_due = max(0, $this->total - $this->paid_amount);

        $this->status = $this->determineStatus();

        $this->saveQuietly(); // avoid firing events if not needed
    }

    /**
     * Determine appropriate status based on payment state
     */
    protected function determineStatus(): string
    {
        if ($this->balance_due <= 0.01) {
            return 'paid';
        }

        if ($this->paid_amount > 0) {
            return 'partially_paid';
        }

        return 'pending'; // or 'held' if you want to differentiate unpaid POS holds
    }

    /**
     * Quick helper: is this invoice fully paid?
     */
    public function isFullyPaid(): bool
    {
        return $this->balance_due <= 0.01;
    }

    /**
     * Quick helper: has any payment been made?
     */
    public function hasAnyPayment(): bool
    {
        return $this->paid_amount > 0 || $this->payments()->exists();
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function cashTransactions()
    {
        return $this->hasMany(CashRegisterTransaction::class, 'billing_invoice_id');
    }
}