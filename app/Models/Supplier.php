<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'description',
        'status',
        'contact_person',
        'email',
        'phone',
        'location',
        'website',
    ];

    protected $casts = [
        'status'     => 'boolean',
        'deleted_at' => 'datetime',
    ];
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'primary_supplier_id');
    }

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

    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }
}