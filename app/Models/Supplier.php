<?php

// app/Models/Supplier.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    // -------------------------------------------------------------------------
    // Fillable fields (matches the "Add New Supplier" form)
    // -------------------------------------------------------------------------
    protected $fillable = [
        'name',
        'category',          // e.g. Medical Supplies, Equipment, etc.
        'description',
        'status',            // Active / Inactive
        'contact_person',
        'email',
        'phone',
        'location',
        'website',
    ];

    // -------------------------------------------------------------------------
    // Casts
    // -------------------------------------------------------------------------
    protected $casts = [
        'status' => 'boolean',   // true = Active, false = Inactive
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------
    /**
     * A supplier can be the primary supplier for many inventory items.
     */
    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'primary_supplier_id');
    }

    /**
     * Many-to-many relationship for secondary/alternative suppliers.
     */
    public function secondaryItems()
    {
        return $this->belongsToMany(
            InventoryItem::class,
            'inventory_item_supplier',
            'supplier_id',
            'inventory_item_id'
        )->withPivot([
            'supplier_item_code',
            'supplier_price',
            'lead_time_days',
            'minimum_order_quantity',
        ])->withTimestamps();
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------
    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }
}