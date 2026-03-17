<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

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
        'unit_cost',
        'unit_price',
        'primary_supplier_id',
        'supplier_item_code',
        'supplier_price',
        'lead_time_days',
        'expiry_date',
        'minimum_order_quantity',

        // === Medicine-specific fields ===
        'generic_name',
        'medicine_type',           // Tablet, Capsule, Syrup, etc.
        'dosage',
        'side_effects',
        'precautions_warnings',
        'tax_rate',
        'storage_conditions',      // JSON array
        'is_active',
        'medicine_image',          // path to uploaded medicine image
        'package_image',           // path to uploaded package image
    ];

    protected $casts = [
        'requires_refrigeration' => 'boolean',
        'controlled_substance' => 'boolean',
        'hazardous_material' => 'boolean',
        'sterile' => 'boolean',
        'expiry_tracking' => 'boolean',
        'is_active' => 'boolean',

        'unit_quantity' => 'integer',
        'current_stock' => 'integer',
        'minimum_stock_level' => 'integer',
        'lead_time_days' => 'integer',
        'minimum_order_quantity' => 'integer',

        'unit_cost' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'supplier_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'expiry_date' => 'date',

        'storage_conditions' => 'array', // JSON -> array
    ];

    /** -----------------------------------------------------------------
     *  Relationships
     * ----------------------------------------------------------------- */

    public function primarySupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'primary_supplier_id');
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'inventory_item_supplier')
            ->withPivot([
                'supplier_item_code',
                'supplier_price',
                'lead_time_days',
                'minimum_order_quantity',
                'is_primary',
            ])
            ->withTimestamps();
    }

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

    // Medicine-specific relationship
    public function batches(): HasMany
    {
        return $this->hasMany(MedicineBatch::class);
    }

    /** -----------------------------------------------------------------
     *  Scopes
     * ----------------------------------------------------------------- */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock_level')
            ->where('current_stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', 0);
    }

    // Optional: Scope to get only medicines
    public function scopeMedicines($query)
    {
        return $query->whereNotNull('generic_name')
            ->orWhere('expiry_tracking', true);
    }

    /** -----------------------------------------------------------------
     *  Accessors / Mutators
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

    // Medicine image URL
    public function getMedicineImageUrlAttribute(): ?string
    {
        return $this->medicine_image ? Storage::disk('public')->url($this->medicine_image) : null;
    }

    // Package image URL
    public function getPackageImageUrlAttribute(): ?string
    {
        return $this->package_image ? Storage::disk('public')->url($this->package_image) : null;
    }

    // Backward compatibility for old image field (if any item still uses it)
    public function getImageUrlAttribute(): ?string
    {
        return $this->medicine_image
            ? Storage::disk('public')->url($this->medicine_image)
            : ($this->image ? Storage::url($this->image) : null);
    }

    // Human-readable storage conditions
    public function getStorageConditionsLabelAttribute(): string
    {
        if (!$this->storage_conditions) {
            return '-';
        }

        return collect($this->storage_conditions)->implode(', ');
    }

    // Status label for Active/Inactive
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active
            ? '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>'
            : '<span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Inactive</span>';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}