<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionMedication extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'inventory_item_id',
        'name',
        'dosage',
        'route',
        'frequency',
        'per_day',
        'instructions',
        'duration_days',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->inventoryItem) {
            $name = $this->inventoryItem->name;
            if ($this->inventoryItem->generic_name) {
                $name .= " ({$this->inventoryItem->generic_name})";
            }
            return $name;
        }

        return $this->name ?? 'Unnamed Medication';
    }
}