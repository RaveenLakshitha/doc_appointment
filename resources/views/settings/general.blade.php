@extends('layouts.app')

@section('title', __('file.clinic_settings'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" mb-8">
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.clinic_settings') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.update_clinic_info') }}</p>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-12">
            @csrf
            <input type="hidden" name="_method" value="PUT">

            <div
                class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 space-y-12">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                            {{ __('file.general_information') }}
                        </h2>
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.clinic_name') }}
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="clinic_name"
                                        value="{{ old('clinic_name', $setting->clinic_name) }}" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                        placeholder="{{ __('file.clinic_name_placeholder') }}">
                                    @error('clinic_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.clinic_id') }}
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="clinic_id" value="{{ old('clinic_id', $setting->clinic_id) }}"
                                        required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                        placeholder="{{ __('file.clinic_id_placeholder') }}">
                                    @error('clinic_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.email_address') }}
                                        <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" value="{{ old('email', $setting->email) }}" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                        placeholder="{{ __('file.email_placeholder') }}">
                                    @error('email') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                    </p> @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.phone_number') }}
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}" required
                                        minlength="7" maxlength="15"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                        placeholder="{{ __('file.phone_placeholder') }}">
                                    @error('phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                    </p> @enderror
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.address') }}
                                    <span class="text-red-500">*</span></label>
                                <textarea name="address" rows="3" required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                    placeholder="{{ __('file.address_placeholder') }}">{{ old('address', $setting->address) }}</textarea>
                                @error('address') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                </p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.tax_id') }}</label>
                                    <input type="text" name="tax_id" value="{{ old('tax_id', $setting->tax_id) }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                        placeholder="{{ __('file.tax_id_placeholder') }}">
                                    @error('tax_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                    </p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                            {{ __('file.regional_settings') }}
                        </h2>
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.timezone') }}
                                        <span class="text-red-500">*</span></label>
                                    <select name="timezone" required
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                        @foreach(\DateTimeZone::listIdentifiers() as $tz)
                                            <option value="{{ $tz }}" {{ $setting->timezone == $tz ? 'selected' : '' }}>{{ $tz }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('timezone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.date_format') }}
                                        <span class="text-red-500">*</span></label>
                                    <select name="date_format" required
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                        <option value="MM/DD/YYYY" {{ $setting->date_format == 'MM/DD/YYYY' ? 'selected' : '' }}>
                                            {{ __('file.date_format_mm_dd_yyyy') }}
                                        </option>
                                        <option value="DD/MM/YYYY" {{ $setting->date_format == 'DD/MM/YYYY' ? 'selected' : '' }}>
                                            {{ __('file.date_format_dd_mm_yyyy') }}
                                        </option>
                                        <option value="YYYY-MM-DD" {{ $setting->date_format == 'YYYY-MM-DD' ? 'selected' : '' }}>
                                            {{ __('file.date_format_yyyy_mm_dd') }}
                                        </option>
                                    </select>
                                    @error('date_format') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.time_format') }}
                                        <span class="text-red-500">*</span></label>
                                    <select name="time_format" required
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                        <option value="12-hour" {{ $setting->time_format == '12-hour' ? 'selected' : '' }}>
                                            {{ __('file.time_format_12_hour') }}
                                        </option>
                                        <option value="24-hour" {{ $setting->time_format == '24-hour' ? 'selected' : '' }}>
                                            {{ __('file.time_format_24_hour') }}
                                        </option>
                                    </select>
                                    @error('time_format') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.currency_code') }}
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="currency" maxlength="3"
                                        value="{{ old('currency', $setting->currency ?? 'USD') }}"
                                        placeholder="{{ __('file.currency_placeholder') ?? 'USD' }}" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow uppercase font-mono tracking-wider">
                                    @error('currency') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                            {{ __('file.printing_settings') ?? 'Printing Settings' }}
                        </h2>
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.invoice_paper_size') ?? 'Invoice Paper Size' }}
                                        <span class="text-red-500">*</span></label>
                                    <select name="invoice_paper_size" required
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                        <option value="A4" {{ old('invoice_paper_size', $setting->invoice_paper_size ?? 'A4') == 'A4' ? 'selected' : '' }}>A4</option>
                                        <option value="80mm" {{ old('invoice_paper_size', $setting->invoice_paper_size ?? 'A4') == '80mm' ? 'selected' : '' }}>80mm POS Receipt</option>
                                    </select>
                                    @error('invoice_paper_size') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.prescription_paper_size') ?? 'Prescription Paper Size' }}
                                        <span class="text-red-500">*</span></label>
                                    <select name="prescription_paper_size" required
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                        <option value="A4" {{ old('prescription_paper_size', $setting->prescription_paper_size ?? 'A4') == 'A4' ? 'selected' : '' }}>A4
                                        </option>
                                        <option value="80mm" {{ old('prescription_paper_size', $setting->prescription_paper_size ?? 'A4') == '80mm' ? 'selected' : '' }}>80mm POS
                                            Receipt</option>
                                    </select>
                                    @error('prescription_paper_size') <p
                                    class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                            {{ __('file.branding_appearance') }}
                        </h2>
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.clinic_logo') }}</label>
                                    @if($setting->logo_path)
                                        <div
                                            class="mb-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                            <img src="{{ asset('storage/' . $setting->logo_path) }}"
                                                class="h-20 w-auto max-w-full object-contain mx-auto"
                                                alt="{{ __('file.current_logo') }}">
                                            <p class="mt-2 text-xs text-center text-gray-500 dark:text-gray-400">
                                                {{ __('file.current_logo') }}
                                            </p>
                                        </div>
                                    @endif
                                    <input type="file" name="logo" accept=".png,.jpg,.jpeg,.svg"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-gray-900 file:text-white hover:file:bg-gray-800 dark:file:bg-gray-700 dark:hover:file:bg-gray-600">
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('file.logo_recommended') }}
                                    </p>
                                    @error('logo') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                    </p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.primary_color') }}
                                        <span class="text-red-500">*</span></label>
                                    <div class="flex items-center gap-3">
                                        <input type="color" name="primary_color"
                                            value="{{ old('primary_color', $setting->primary_color) }}" required
                                            class="h-12 w-20 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer">
                                        <input type="text" value="{{ old('primary_color', $setting->primary_color) }}"
                                            readonly
                                            class="flex-1 px-3 py-2 text-sm font-mono border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-white">
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('file.primary_color_description') }}
                                    </p>
                                    @error('primary_color') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 border border-gray-300 dark:border-gray-600 dark:bg-white dark:text-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('file.save_changes') }}
                </button>
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ __('file.cancel') }}
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const colorInput = document.querySelector('input[name="primary_color"][type="color"]');
            const colorDisplay = colorInput?.nextElementSibling;

            if (colorInput && colorDisplay) {
                colorInput.addEventListener('input', function () {
                    colorDisplay.value = this.value.toUpperCase();
                });
            }

            const currencyInput = document.querySelector('input[name="currency"]');
            if (currencyInput) {
                currencyInput.addEventListener('input', function () {
                    this.value = this.value.toUpperCase().trim();
                });
            }
        });
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection