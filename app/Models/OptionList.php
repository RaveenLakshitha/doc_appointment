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

    public function inUse(): bool
    {
        // Check if used as position in active AND enabled Doctors
        if (\App\Models\Doctor::where('position_id', $this->id)
            ->where('is_active', true)
            ->exists()) {
            return true;
        }

        // Check if used in doctor_option pivot (languages)
        // Check if any linked doctor is active AND enabled
        if (\Illuminate\Support\Facades\DB::table('doctor_option')
            ->join('doctors', 'doctor_option.doctor_id', '=', 'doctors.id')
            ->where('doctor_option.option_id', $this->id)
            ->where('doctors.is_active', true)
            ->whereNull('doctors.deleted_at')
            ->exists()) {
            return true;
        }

        // Check if used in active appointments
        if (\App\Models\Appointment::where('preferred_language_id', $this->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Clear references in soft-deleted or inactive records to allow physical deletion of this option.
     */
    public function cleanupReferences(): void
    {
        // Null out in soft-deleted or inactive doctors
        \App\Models\Doctor::withTrashed()
            ->where(function ($query) {
                $query->whereNotNull('deleted_at')
                    ->orWhere('is_active', false);
            })
            ->where('position_id', $this->id)
            ->update(['position_id' => null]);

        // Remove from doctor_option pivot where doctor is soft-deleted or inactive
        $nonActiveDoctorIds = \App\Models\Doctor::withTrashed()
            ->where(function ($query) {
                $query->whereNotNull('deleted_at')
                    ->orWhere('is_active', false);
            })
            ->pluck('id')
            ->toArray();

        if (!empty($nonActiveDoctorIds)) {
            \Illuminate\Support\Facades\DB::table('doctor_option')
                ->where('option_id', $this->id)
                ->whereIn('doctor_id', $nonActiveDoctorIds)
                ->delete();
        }

        // Null out in soft-deleted appointments
        \App\Models\Appointment::onlyTrashed()
            ->where('preferred_language_id', $this->id)
            ->update(['preferred_language_id' => null]);
    }
}