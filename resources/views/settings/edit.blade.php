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
                <div>
                    <label class="block mb-2 text-sm font-medium">Clinic Name</label>
                    <input type="text" name="clinic_name" value="{{ old('clinic_name', $setting->clinic_name) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">Clinic ID/Registration Number</label>
                    <input type="text" name="clinic_id" value="{{ old('clinic_id', $setting->clinic_id) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $setting->email) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">Address</label>
                    <textarea name="address" rows="3" class="w-full border-gray-300 rounded-md shadow-sm">{{ old('address', $setting->address) }}</textarea>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">Website</label>
                    <input type="text" name="website" value="{{ old('website', $setting->website) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">Tax ID</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id', $setting->tax_id) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>
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
                <div>
                    <label class="block mb-2 text-sm font-medium">Timezone</label>
                    <select name="timezone" class="w-full border-gray-300 rounded-md shadow-sm">
                        @foreach(\DateTimeZone::listIdentifiers(\DateTimeZone::ALL) as $tz)
                            <option value="{{ $tz }}" {{ old('timezone', $setting->timezone) == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">Date Format</label>
                    <select name="date_format" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="MM/DD/YYYY" {{ $setting->date_format == 'MM/DD/YYYY' ? 'selected' : '' }}>MM/DD/YYYY</option>
                        <option value="DD/MM/YYYY" {{ $setting->date_format == 'DD/MM/YYYY' ? 'selected' : '' }}>DD/MM/YYYY</option>
                        <option value="YYYY-MM-DD" {{ $setting->date_format == 'YYYY-MM-DD' ? 'selected' : '' }}>YYYY-MM-DD</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">Time Format</label>
                    <select name="time_format" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="12-hour" {{ $setting->time_format == '12-hour' ? 'selected' : '' }}>12-hour (AM/PM)</option>
                        <option value="24-hour" {{ $setting->time_format == '24-hour' ? 'selected' : '' }}>24-hour</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">First Day of Week</label>
                    <select name="first_day_of_week" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="Sunday" {{ $setting->first_day_of_week == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                        <option value="Monday" {{ $setting->first_day_of_week == 'Monday' ? 'selected' : '' }}>Monday</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Branding & Appearance -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 text-blue-700">Branding & Appearance</h3>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label>Clinic Logo (512x512px, PNG/JPG/SVG)</label>
                    @if($setting->logo_path)
                        <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="Logo" class="w-32 h-32 object-contain border mb-2">
                    @endif
                    <input type="file" name="logo" accept=".png,.jpg,.jpeg,.svg" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div>
                    <label>Favicon (32x32px, PNG/ICO)</label>
                    @if($setting->favicon_path)
                        <img src="{{ asset('storage/' . $setting->favicon_path) }}" alt="Favicon" class="w-8 h-8 mb-2">
                    @endif
                    <input type="file" name="favicon" accept=".png,.ico" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </div>

            <div class="mt-4">
                <label class="block mb-2 text-sm font-medium">Primary Color</label>
                <input type="color" name="primary_color" value="{{ old('primary_color', $setting->primary_color) }}" class="w-full border-gray-300 rounded-md shadow-sm h-10 p-1">
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