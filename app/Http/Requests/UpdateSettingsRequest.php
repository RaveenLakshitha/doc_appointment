<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Setting;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        // Get the only settings row ID (there's always just one)
        $settingId = Setting::first()?->id;

        return [
            'clinic_name'     => 'required|string|max:255',
            'clinic_id'       => [
                'required',
                'string',
                'max:50',
                Rule::unique('settings', 'clinic_id')->ignore($settingId), // ← THIS FIXES IT
            ],
            'email'           => 'required|email',
            'phone'           => 'required|string|max:20',
            'address'         => 'required|string',
            'website'         => 'nullable|url',
            'tax_id'          => 'nullable|string|max:50',

            'weekday_open'    => 'required',
            'weekday_close'   => 'required',
            'weekend_open'    => 'nullable',
            'weekend_close'   => 'nullable',

            'timezone'        => 'required|timezone',
            'date_format'     => 'required|in:MM/DD/YYYY,DD/MM/YYYY,YYYY-MM-DD',
            'time_format'     => 'required|in:12-hour,24-hour',

            'logo'            => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'favicon'         => 'nullable|image|mimes:png,ico|max:100',
            'primary_color'   => 'required|string|regex:/^#[a-f0-9]{6}$/i',
        ];
    }
}