<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitOfMeasure extends Model
{
    use HasFactory;

    protected $table = 'unit_of_measures';

    protected $fillable = ['name', 'abbreviation', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->abbreviation ? "{$this->name} ({$this->abbreviation})" : $this->name;
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'unit_of_measure', 'name');
    }
}