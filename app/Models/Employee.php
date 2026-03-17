<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'photo',
        'date_of_birth',
        'gender',
        'department_id',
        'position',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'emergency_contact_name',
        'emergency_contact_phone',
        'profession',
        'specialization',
        'professional_bio',
        'employee_code',
        'reporting_to',
        'hire_date',
        'termination_date',
        'status',
        'employment_type',
        'work_schedule',
        'work_hours_weekly',
        'contract_start',
        'contract_end',
        'contract_notes',
        'salary',
        'payment_frequency',
    ];

    protected $casts = [
        'license_issue_date'  => 'date',
        'license_expiry_date' => 'date',
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'contract_start' => 'date',
        'contract_end' => 'date',
        'status' => 'boolean',
    ];

    // Accessor for full name (optional)
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} " . ($this->middle_name ? "{$this->middle_name} " : '') . "{$this->last_name}");
    }

    // Relationship with User (for login)
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // Department
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // Supervisor (reporting to)
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reporting_to');
    }

    // Subordinates (employees reporting to this one)
    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'reporting_to');
    }

    // Qualifications & Licenses
    public function qualifications(): HasMany
    {
        return $this->hasMany(EmployeeQualification::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(EmployeeLicense::class);
    }

    // Existing relationships
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function approvedLeaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'approved_by');
    }
    
    public static function generateEmployeeCode(): string
    {
        $last = self::withTrashed()
            ->where('employee_code', 'LIKE', 'EMP-%')
            ->orderByRaw("CAST(SUBSTRING(employee_code, 5) AS UNSIGNED) DESC")
            ->first();

        $nextNumber = $last ? ((int) substr($last->employee_code, 4)) + 1 : 1;

        return sprintf('EMP-%04d', $nextNumber);
    }
}