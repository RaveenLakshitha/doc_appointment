<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'batch_number',
        'manufacturing_date',
        'expiry_date',
        'initial_quantity',
        'current_quantity',
    ];

    protected $casts = [
        'manufacturing_date' => 'date',
        'expiry_date'        => 'date',
        'initial_quantity'   => 'integer',
        'current_quantity'   => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}