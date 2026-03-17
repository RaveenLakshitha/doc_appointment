<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_active',
        'is_deleted',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_deleted' => 'boolean',
        ];
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class , 'user_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function cashRegisters()
    {
        return $this->hasMany(CashRegister::class);
    }

    public function openCashRegister()
    {
        return $this->cashRegisters()
            ->whereNull('closed_at')
            ->latest('opened_at')
            ->first();
    }

    public function hasOpenCashRegister(): bool
    {
        return $this->openCashRegister() !== null;
    }

    public function getAvatarAttribute()
    {
        if ($this->employee && $this->employee->photo) {
            return asset('storage/' . $this->employee->photo);
        }

        if ($this->doctor && $this->doctor->profile_photo) {
            return asset('storage/' . $this->doctor->profile_photo);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6366f1&color=fff';
    }
}