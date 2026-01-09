<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptionList extends Model
{
    protected $fillable = ['type', 'name', 'slug', 'order', 'status'];

    protected $casts = ['status' => 'boolean'];

    // Helper to get all active options for a type
    public static function getOptions(string $type): array
    {
        return self::where('type', $type)
            ->where('status', true)
            ->orderBy('order')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }
}