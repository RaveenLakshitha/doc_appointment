<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InventoryItem extends Model
{
    use HasFactory;

    /** -----------------------------------------------------------------
     *  Fillable / Casts
     * ----------------------------------------------------------------- */
    protected $fillable = [
        'name',
        'sku',
        'category_id',
        'description',
        'unit_of_measure',
        'unit_quantity',
        'storage_location',
        'additional_info',
        'manufacturer',
        'brand',
        'model_version',
        'expiry_tracking',
        'requires_refrigeration',
        'controlled_substance',
        'hazardous_material',
        'sterile',
        'current_stock',
        'minimum_stock_level',
        'maximum_stock_level',
        'reorder_point',
        'reorder_quantity',
        'unit_cost',
        'unit_price',
        'primary_supplier_id',
        'supplier_item_code',
        'supplier_price',
        'lead_time_days',
        'minimum_order_quantity',
    ];

    protected $casts = [
        'requires_refrigeration' => 'boolean',
        'controlled_substance'   => 'boolean',
        'hazardous_material'     => 'boolean',
        'sterile'                => 'boolean',
        'unit_quantity'          => 'integer',
        'current_stock'          => 'integer',
        'minimum_stock_level'    => 'integer',
        'maximum_stock_level'    => 'integer',
        'reorder_point'          => 'integer',
        'reorder_quantity'       => 'integer',
        'lead_time_days'         => 'integer',
        'minimum_order_quantity'=> 'integer',
        'unit_cost'              => 'decimal:2',
        'unit_price'             => 'decimal:2',
        'supplier_price'         => 'decimal:2',
    ];

    /** -----------------------------------------------------------------
     *  Relationships
     * ----------------------------------------------------------------- */

    /** Primary supplier (the one stored in primary_supplier_id) */
    public function primarySupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'primary_supplier_id');
    }

    /** All suppliers – primary **and** alternatives */
    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'inventory_item_supplier')
            ->withPivot([
                'supplier_item_code',
                'supplier_price',
                'lead_time_days',
                'minimum_order_quantity',
                'is_primary',               // <-- flag column
            ])
            ->withTimestamps();
    }

    /** Helper: only the **alternative** suppliers (not primary) */
    public function secondaryItems(): BelongsToMany
    {
        return $this->suppliers()->wherePivot('is_primary', false);
    }

    public function unitOfMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unit_of_measure', 'name');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** -----------------------------------------------------------------
     *  Scopes
     * ----------------------------------------------------------------- */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'reorder_point');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', 0);
    }

    /** -----------------------------------------------------------------
     *  Accessors
     * ----------------------------------------------------------------- */
    public function getTotalValueAttribute(): float
    {
        return round($this->current_stock * $this->unit_cost, 2);
    }

    public function getProfitMarginAttribute(): float
    {
        return $this->unit_cost > 0
            ? round((($this->unit_price - $this->unit_cost) / $this->unit_cost) * 100, 2)
            : 0;
    }
}