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
        $settingId = Setting::first()?->id;

        return [
            'clinic_name'     => 'required|string|max:255',
            'clinic_id'       => [
                'required',
                'string',
                'max:50',
                Rule::unique('settings', 'clinic_id')->ignore($settingId),
            ],
            'email'           => 'required|email',
            'phone'           => 'required|string|min:7|max:15',
            'address'         => 'required|string',
            'website'         => 'nullable|url',
            'tax_id'          => 'nullable|string|max:50',
            'currency' => 'required|string|size:3|alpha',

            'timezone'        => 'required|timezone',
            'tax_percentage'  => 'nullable|numeric|min:0|max:100',

            'logo'            => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'primary_color'   => 'required|string|regex:/^#[a-f0-9]{6}$/i',

            'invoice_paper_size'      => 'required|in:A4,80mm',
            'prescription_paper_size' => 'required|in:A4,80mm',
        ];
    }
}
