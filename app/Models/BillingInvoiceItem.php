<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BillingInvoiceItem extends Model
{
    protected $table = 'billing_invoice_items';

    protected $fillable = [
        'invoice_id',
        'itemable_id',
        'itemable_type',
        'description',
        'quantity',
        'unit_price',
        'total',
    ];

    protected $casts = [
        'quantity'   => 'integer',
        'unit_price' => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(BillingInvoice::class, 'invoice_id');
        // Critical: tells Laravel to use 'invoice_id' column, not 'billing_invoice_id'
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->total = $item->quantity * $item->unit_price;
        });
    }
}