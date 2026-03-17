<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'parent_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class , 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class , 'parent_id');
    }

    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $node = $this;
        $visited = [$this->id];

        while ($node->parent) {
            $node = $node->parent;
            if (in_array($node->id, $visited)) {
                array_unshift($path, '... (Circular)');
                break;
            }
            $visited[] = $node->id;
            array_unshift($path, $node->name);
        }

        return implode(' > ', $path);
    }

    public function getLevelAttribute(): int
    {
        $level = 0;
        $node = $this->parent;
        $visited = [$this->id];

        while ($node) {
            if (in_array($node->id, $visited))
                break;
            $visited[] = $node->id;
            $level++;
            $node = $node->parent;
        }

        return $level;
    }
}