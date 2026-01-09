<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateMedication extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_template_id',
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
}