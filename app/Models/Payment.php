<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_date',
        'method',
        'reference',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(BillingInvoice::class, 'invoice_id');
        // Ensures correct foreign key usage
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}