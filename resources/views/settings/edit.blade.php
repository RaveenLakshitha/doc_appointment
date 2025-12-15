{{-- resources/views/settings/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">Application Settings</h2>

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Clinic Information -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 text-blue-700">Clinic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Clinic Name" name="clinic_name" :value="old('clinic_name', $setting->clinic_name)" />
                <x-input label="Clinic ID/Registration Number" name="clinic_id" :value="old('clinic_id', $setting->clinic_id)" />
                <x-input label="Email Address" type="email" name="email" :value="old('email', $setting->email)" />
                <x-input label="Phone Number" name="phone" :value="old('phone', $setting->phone)" />
                <x-textarea label="Address" name="address" rows="3">{{ old('address', $setting->address) }}</x-textarea>
                <x-input label="Website" name="website" :value="old('website', $setting->website)" />
                <x-input label="Tax ID" name="tax_id" :value="old('tax_id', $setting->tax_id)" />
            </div>
        </div>

        <!-- Operating Hours -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 text-blue-700">Operating Hours</h3>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium">Weekdays</label>
                    <div class="flex gap-2 mt-1">
                        <input type="time" name="weekday_open" value="{{ $setting->operating_hours['weekdays'][0] ?? '08:00' }}" class="border rounded px-3 py-2" required>
                        <span class="self-center">to</span>
                        <input type="time" name="weekday_close" value="{{ $setting->operating_hours['weekdays'][1] ?? '18:00' }}" class="border rounded px-3 py-2" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium">Weekends</label>
                    <div class="flex gap-2 mt-1">
                        <input type="time" name="weekend_open" value="{{ $setting->operating_hours['weekends'][0] === 'closed' ? '' : $setting->operating_hours['weekends'][0] }}" class="border rounded px-3 py-2">
                        <span class="self-center">to</span>
                        <input type="time" name="weekend_close" value="{{ $setting->operating_hours['weekends'][1] === 'closed' ? '' : $setting->operating_hours['weekends'][1] }}" class="border rounded px-3 py-2">
                    </div>
                </div>
            </div>
        </div>

        <!-- Regional Settings -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 text-blue-700">Regional Settings</h3>
            <div class="grid grid-cols-2 gap-4">
                <x-select label="Timezone" name="timezone" :options="\DateTimeZone::listIdentifiers(\DateTimeZone::ALL)" :selected="old('timezone', $setting->timezone)" />
                <x-select label="Date Format" name="date_format">
                    <option value="MM/DD/YYYY" {{ $setting->date_format == 'MM/DD/YYYY' ? 'selected' : '' }}>MM/DD/YYYY</option>
                    <option value="DD/MM/YYYY" {{ $setting->date_format == 'DD/MM/YYYY' ? 'selected' : '' }}>DD/MM/YYYY</option>
                    <option value="YYYY-MM-DD" {{ $setting->date_format == 'YYYY-MM-DD' ? 'selected' : '' }}>YYYY-MM-DD</option>
                </x-select>
                <x-select label="Time Format" name="time_format">
                    <option value="12-hour" {{ $setting->time_format == '12-hour' ? 'selected' : '' }}>12-hour (AM/PM)</option>
                    <option value="24-hour" {{ $setting->time_format == '24-hour' ? 'selected' : '' }}>24-hour</option>
                </x-select>
                <x-select label="First Day of Week" name="first_day_of_week">
                    <option value="Sunday" {{ $setting->first_day_of_week == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                    <option value="Monday" {{ $setting->first_day_of_week == 'Monday' ? 'selected' : '' }}>Monday</option>
                </x-select>
            </div>
        </div>

        <!-- Branding & Appearance -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 text-blue-700">Branding & Appearance</h3>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label>Clinic Logo (512x512px, PNG/JPG/SVG)</label>
                    @if($setting->logo_path)
                        <img src="{{ Storage::url($setting->logo_path) }}" alt="Logo" class="w-32 h-32 object-contain border mb-2">
                    @endif
                    <input type="file" name="logo" accept=".png,.jpg,.jpeg,.svg" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div>
                    <label>Favicon (32x32px, PNG/ICO)</label>
                    @if($setting->favicon_path)
                        <img src="{{ Storage::url($setting->favicon_path) }}" alt="Favicon" class="w-8 h-8 mb-2">
                    @endif
                    <input type="file" name="favicon" accept=".png,.ico" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </div>

            <div class="mt-4">
                <x-input label="Primary Color" name="primary_color" type="color" :value="old('primary_color', $setting->primary_color)" />
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection