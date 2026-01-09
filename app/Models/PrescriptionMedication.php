<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionMedication extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'name',
        'dosage',
        'route',
        'frequency',
        'instructions',
        'duration_days',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}