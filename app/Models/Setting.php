<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'clinic_name',
        'clinic_id',
        'address',
        'email',
        'phone',
        'website',
        'tax_id',
        'timezone',
        'tax_percentage',
        'first_day_of_week',
        'language',
        'logo_path',
        'primary_color',
        'secondary_color',
        'currency',
        'invoice_paper_size',
        'prescription_paper_size',
    ];


    public static function getAll()
    {
        return static::first() ?? new static();
    }
}
