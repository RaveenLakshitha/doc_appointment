<?php
// app/Models/MedicationTemplateCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicationTemplateCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    // Relationships
    public function templates()
    {
        return $this->hasMany(MedicationTemplate::class, 'category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order')->orderBy('name');
    }

    // Auto-generate slug
    protected static function booted()
    {
        static::saving(function ($category) {
            if (empty($category->slug) || $category->isDirty('name')) {
                $category->slug = \Str::slug($category->name);
            }
        });
    }

    // Accessor for Tailwind color class
    public function getColorClassAttribute(): string
    {
        return $this->color ?? 'gray-600';
    }
}