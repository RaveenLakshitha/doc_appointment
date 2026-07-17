<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'payable_id',
        'payable_type',
        'date',
        'amount',
        'therapist_amount',
        'caped_amount',
        'payment_method',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'therapist_amount' => 'decimal:2',
        'caped_amount' => 'decimal:2',
    ];

    public function payable()
    {
        return $this->morphTo()->withTrashed();
    }

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class)
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
