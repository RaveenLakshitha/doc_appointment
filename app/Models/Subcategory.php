<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $table = 'subcategories'; // Optional — Laravel auto-detects

    protected $fillable = ['category_id', 'name', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // FIXED: Use subcategory_id, not string
    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'subcategory_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCategory($query, $categoryName)
    {
        return $query->whereHas('category', fn($q) => $q->where('name', $categoryName));
    }

    public function getFullNameAttribute(): string
    {
        return $this->category?->name ? "{$this->category->name} > {$this->name}" : $this->name;
    }

    public function getItemCountAttribute(): int
    {
        return $this->inventoryItems()->count();
    }
}