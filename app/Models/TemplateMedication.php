<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateMedication extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_template_id',
        'inventory_item_id',
        'name',
        'dosage',
        'route',
        'frequency',
        'instructions',
    ];

    public function template()
    {
        return $this->belongsTo(MedicineTemplate::class, 'medicine_template_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function getDisplayNameAttribute()
    {
        return $this->inventoryItem ? $this->inventoryItem->name : $this->name;
    }
}