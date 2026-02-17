<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name',             
        'code',             
        'description',
        'days_allowed',     
        'is_paid',         
        'requires_approval',
        'active',
    ];

    public function entitlements()
    {
        return $this->hasMany(EmployeeLeaveEntitlement::class);
    }

    public function requests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}