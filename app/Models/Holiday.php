<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Holiday extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Check if a given date is a holiday.
     *
     * @param string|Carbon $date
     * @return bool
     */
    public static function isHoliday($date): bool
    {
        $dateString = Carbon::parse($date)->format('Y-m-d');

        return self::where('start_date', '<=', $dateString)
            ->where('end_date', '>=', $dateString)
            ->exists();
    }
}
