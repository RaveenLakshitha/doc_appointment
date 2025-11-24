<?php
// app/Models/TemplateMedication.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateMedication extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'template_medications';

    protected $fillable = [
        'medication_template_id',
        'name',
        'dosage',
        'route',
        'frequency',
        'instructions',
        'duration',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // Default values
    protected $attributes = [
        'route' => 'Oral',
        'order' => 0,
    ];

    // Relationships
    public function template()
    {
        return $this->belongsTo(MedicationTemplate::class, 'medication_template_id');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->name,
            $this->dosage,
            $this->route ? "({$this->route})" : null,
        ]);
        return implode(' ', $parts);
    }

    public function getSummaryAttribute(): string
    {
        $parts = array_filter([
            $this->frequency,
            $this->instructions,
            $this->duration ? "for {$this->duration}" : null,
        ]);
        return implode(' • ', $parts);
    }
}