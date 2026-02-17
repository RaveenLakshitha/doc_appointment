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
        'date_format',
        'time_format',
        'first_day_of_week',
        'language',
        'logo_path',
        'primary_color',
        'secondary_color',
        'currency',
    ];


    public static function getAll()
    {
        return static::first() ?? new static();
    }
}