<?php

// app/Models/Setting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'clinic_name', 'clinic_id', 'address', 'email', 'phone', 'website', 'tax_id',
        'operating_hours', 'timezone', 'date_format', 'time_format', 'first_day_of_week',
        'language', 'logo_path', 'favicon_path', 'primary_color', 'secondary_color'
    ];

    protected $casts = [
        'operating_hours' => 'array',
    ];

    // Helper to get the single instance (assuming one row)
    public static function getAll()
    {
        return static::first() ?? new static();
    }
}