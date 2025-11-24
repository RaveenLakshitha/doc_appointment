<?php
// app/Models/MedicationTemplate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicationTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'created_by',
        'last_used_at',
        'usage_count',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'usage_count'  => 'integer',
        'created_by'   => 'integer',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(MedicationTemplateCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(Doctor::class, 'created_by');
    }

    public function medications()
    {
        return $this->hasMany(TemplateMedication::class, 'medication_template_id')
                    ->orderBy('order')
                    ->orderBy('id');
    }

    // Accessors
    public function getMedicationsCountAttribute(): int
    {
        return $this->medications()->count();
    }

    public function getShortNameAttribute(): string
    {
        return \Str::limit($this->name ?? '', 50);
    }

    public function getUsageTextAttribute(): string
    {
        return $this->usage_count > 0 ? "{$this->usage_count} time" . ($this->usage_count > 1 ? 's' : '') : 'Never used';
    }

    // Methods
    public function markAsUsed(): void
    {
        $this->update([
            'last_used_at' => now(),
            'usage_count'  => $this->usage_count + 1,
        ]);
    }

    // Scopes
    public function scopeWithUsage($query)
    {
        return $query->addSelect([
            'usage_count',
            'last_used_at',
        ]);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', "%{$term}%"))
              ->orWhereHas('medications', fn($mq) => $mq->where('name', 'like', "%{$term}%"));
        });
    }
}