{{-- resources/views/patients/create.blade.php --}}
@extends('layouts.app')
@section('title', __('file.create_title'))

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('patients.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">{{ __('file.patients') }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">{{ __('file.add_patient') }}</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.add_new_patient') }}</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.create_description') }}</p>
    </div>

    <form method="POST" action="{{ route('patients.store') }}" class="space-y-8" enctype="multipart/form-data">
        @csrf

        <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex overflow-x-auto scrollbar-hide" aria-label="Tabs">
                    <button type="button" onclick="switchTab('personal')" id="tab-personal"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-gray-400 bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="hidden sm:inline">{{ __('file.personal_information') }}</span>
                            <span class="sm:hidden">{{ __('file.personal') }}</span>
                        </div>
                    </button>
                    <button type="button" onclick="switchTab('medical')" id="tab-medical"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="hidden sm:inline">{{ __('file.medical_information') }}</span>
                            <span class="sm:hidden">{{ __('file.medical') }}</span>
                        </div>
                    </button>
                    <button type="button" onclick="switchTab('insurance')" id="tab-insurance"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <span class="hidden sm:inline">{{ __('file.insurance_billing') }}</span>
                            <span class="sm:hidden">{{ __('file.insurance') }}</span>
                        </div>
                    </button>
                    <button type="button" onclick="switchTab('consent')" id="tab-consent"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="hidden sm:inline">{{ __('file.consent_documents') }}</span>
                            <span class="sm:hidden">{{ __('file.consent') }}</span>
                        </div>
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <div id="content-personal" class="tab-content">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.first_name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.first_name_ph') }}">
                                @error('first_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.middle_name') }}</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.middle_name_ph') }}">
                                @error('middle_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.last_name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.last_name_ph') }}">
                                @error('last_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.date_of_birth') }} <span class="text-red-500">*</span></label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white [color-scheme:light] dark:[color-scheme:dark] transition-shadow">
                                @error('date_of_birth') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                @error('date_of_birth') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.gender') }} <span class="text-red-500">*</span></label>
                                <select name="gender" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                    <option value="">{{ __('file.select_gender') }}</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('file.male') }}</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('file.female') }}</option>
                                </select>
                                @error('gender') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.marital_status') }}</label>
                            <select name="marital_status" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                <option value="">{{ __('file.select_status') }}</option>
                                <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>{{ __('file.single') }}</option>
                                <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>{{ __('file.married') }}</option>
                                <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>{{ __('file.divorced') }}</option>
                                <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>{{ __('file.widowed') }}</option>
                            </select>
                            @error('marital_status') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.address') }}</label>
                            <textarea name="address" rows="3" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none" placeholder="{{ __('file.address_ph') }}">{{ old('address') }}</textarea>
                            @error('address') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.city') }}</label>
                                <input type="text" name="city" value="{{ old('city') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.city') }}">
                                @error('city') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.state') }}</label>
                                <input type="text" name="state" value="{{ old('state') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.state') }}">
                                @error('state') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.zip_code') }}</label>
                                <input type="text" name="zip_code" value="{{ old('zip_code') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.zip_code') }}">
                                @error('zip_code') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.phone') }}</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="+1234567890">
                                @error('phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.alternative_phone') }}</label>
                                <input type="text" name="alternative_phone" value="{{ old('alternative_phone') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="+1234567890">
                                @error('alternative_phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.email') }}</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="email@example.com">
                            @error('email') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.preferred_contact_method') }}</label>
                            <select name="preferred_contact_method" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="phone" {{ old('preferred_contact_method') == 'phone' ? 'selected' : '' }}>{{ __('file.phone') }}</option>
                                <option value="email" {{ old('preferred_contact_method') == 'email' ? 'selected' : '' }}>{{ __('file.email') }}</option>
                                <option value="sms" {{ old('preferred_contact_method') == 'sms' ? 'selected' : '' }}>{{ __('file.sms') }}</option>
                            </select>
                            @error('preferred_contact_method') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.emergency_contact') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.emergency_name') }}</label>
                                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.full_name_ph') }}">
                                    @error('emergency_contact_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.emergency_relationship') }}</label>
                                    <input type="text" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.relationship_ph') }}">
                                    @error('emergency_contact_relationship') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.phone') }}</label>
                                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="+1234567890">
                                    @error('emergency_contact_phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.email') }}</label>
                                    <input type="email" name="emergency_contact_email" value="{{ old('emergency_contact_email') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="contact@example.com">
                                    @error('emergency_contact_email') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="content-medical" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.blood_type') }}</label>
                                <select name="blood_type" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                    <option value="">{{ __('file.select') }}</option>
                                    <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                                </select>
                                @error('blood_type') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.height_cm') }}</label>
                                <input type="number" name="height_cm" value="{{ old('height_cm') }}" min="50" max="250" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="170">
                                @error('height_cm') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.weight_kg') }}</label>
                                <input type="number" name="weight_kg" value="{{ old('weight_kg') }}" min="20" max="300" step="0.1" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="70.5">
                                @error('weight_kg') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.allergies') }}</label>
                            <textarea name="allergies" rows="3" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none" placeholder="{{ __('file.allergies_ph') }}">{{ old('allergies') ? implode(', ', old('allergies')) : '' }}</textarea>
                            @error('allergies') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.current_medications') }}</label>
                            <textarea name="current_medications" rows="3" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none" placeholder="{{ __('file.medications_ph') }}">{{ old('current_medications') ? implode(', ', old('current_medications')) : '' }}</textarea>
                            @error('current_medications') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.chronic_conditions') }}</label>
                            <textarea name="chronic_conditions" rows="3" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none" placeholder="{{ __('file.conditions_ph') }}">{{ old('chronic_conditions') ? implode(', ', old('chronic_conditions')) : '' }}</textarea>
                            @error('chronic_conditions') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.past_surgeries') }}</label>
                            <textarea name="past_surgeries" rows="4" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none" placeholder="{{ __('file.surgeries_ph') }}">{{ old('past_surgeries') ? implode("\n", old('past_surgeries')) : '' }}</textarea>
                            @error('past_surgeries') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.previous_hospitalizations') }}</label>
                            <textarea name="previous_hospitalizations" rows="4" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none" placeholder="{{ __('file.hospitalizations_ph') }}">{{ old('previous_hospitalizations') ? implode("\n", old('previous_hospitalizations')) : '' }}</textarea>
                            @error('previous_hospitalizations') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.lifestyle') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.smoking_status') }}</label>
                                    <select name="smoking_status" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                        <option value="never" {{ old('smoking_status') == 'never' ? 'selected' : '' }}>{{ __('file.never') }}</option>
                                        <option value="former" {{ old('smoking_status') == 'former' ? 'selected' : '' }}>{{ __('file.former') }}</option>
                                        <option value="current" {{ old('smoking_status') == 'current' ? 'selected' : '' }}>{{ __('file.current') }}</option>
                                    </select>
                                    @error('smoking_status') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.alcohol_consumption') }}</label>
                                    <select name="alcohol_consumption" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                        <option value="none" {{ old('alcohol_consumption') == 'none' ? 'selected' : '' }}>{{ __('file.none') }}</option>
                                        <option value="occasional" {{ old('alcohol_consumption') == 'occasional' ? 'selected' : '' }}>{{ __('file.occasional') }}</option>
                                        <option value="moderate" {{ old('alcohol_consumption') == 'moderate' ? 'selected' : '' }}>{{ __('file.moderate') }}</option>
                                        <option value="heavy" {{ old('alcohol_consumption') == 'heavy' ? 'selected' : '' }}>{{ __('file.heavy') }}</option>
                                    </select>
                                    @error('alcohol_consumption') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.exercise_frequency') }}</label>
                                    <select name="exercise_frequency" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                        <option value="never" {{ old('exercise_frequency') == 'never' ? 'selected' : '' }}>{{ __('file.never') }}</option>
                                        <option value="rarely" {{ old('exercise_frequency') == 'rarely' ? 'selected' : '' }}>{{ __('file.rarely') }}</option>
                                        <option value="weekly" {{ old('exercise_frequency') == 'weekly' ? 'selected' : '' }}>{{ __('file.weekly') }}</option>
                                        <option value="daily" {{ old('exercise_frequency') == 'daily' ? 'selected' : '' }}>{{ __('file.daily') }}</option>
                                    </select>
                                    @error('exercise_frequency') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.dietary_habits') }}</label>
                                    <input type="text" name="dietary_habits" value="{{ old('dietary_habits') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.dietary_ph') }}">
                                    @error('dietary_habits') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="content-insurance" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.primary_insurance') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.provider') }}</label>
                                    <input type="text" name="primary_insurance_provider" value="{{ old('primary_insurance_provider') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.insurance_company') }}">
                                    @error('primary_insurance_provider') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.policy_number') }}</label>
                                    <input type="text" name="primary_policy_number" value="{{ old('primary_policy_number') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="POL123456789">
                                    @error('primary_policy_number') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.group_number') }}</label>
                                    <input type="text" name="primary_group_number" value="{{ old('primary_group_number') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="GRP987">
                                    @error('primary_group_number') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.policy_holder_name') }}</label>
                                    <input type="text" name="primary_policy_holder_name" value="{{ old('primary_policy_holder_name') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.full_name_ph') }}">
                                    @error('primary_policy_holder_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.relationship_to_patient') }}</label>
                                    <input type="text" name="primary_relationship_to_patient" value="{{ old('primary_relationship_to_patient') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.relationship_ph') }}">
                                    @error('primary_relationship_to_patient') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.insurance_phone') }}</label>
                                    <input type="text" name="primary_insurance_phone" value="{{ old('primary_insurance_phone') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="+1-800-555-1234">
                                    @error('primary_insurance_phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.secondary_insurance') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.provider') }}</label>
                                    <input type="text" name="secondary_insurance_provider" value="{{ old('secondary_insurance_provider') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.insurance_company') }}">
                                    @error('secondary_insurance_provider') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.policy_number') }}</label>
                                    <input type="text" name="secondary_policy_number" value="{{ old('secondary_policy_number') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="POL987654321">
                                    @error('secondary_policy_number') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.billing_preferences') }}</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.preferred_billing_method') }}</label>
                                <select name="preferred_billing_method" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                    <option value="insurance_first" {{ old('preferred_billing_method') == 'insurance_first' ? 'selected' : '' }}>{{ __('file.insurance_first') }}</option>
                                    <option value="self_pay" {{ old('preferred_billing_method') == 'self_pay' ? 'selected' : '' }}>{{ __('file.self_pay') }}</option>
                                </select>
                                @error('preferred_billing_method') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.payment_methods') }}</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="payment_methods[]" value="credit_card" {{ is_array(old('payment_methods')) && in_array('credit_card', old('payment_methods')) ? 'checked' : '' }} class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('file.credit_card') }}</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="payment_methods[]" value="debit_card" {{ is_array(old('payment_methods')) && in_array('debit_card', old('payment_methods')) ? 'checked' : '' }} class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('file.debit_card') }}</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="payment_methods[]" value="cash" {{ is_array(old('payment_methods')) && in_array('cash', old('payment_methods')) ? 'checked' : '' }} class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('file.cash') }}</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="payment_methods[]" value="bank_transfer" {{ is_array(old('payment_methods')) && in_array('bank_transfer', old('payment_methods')) ? 'checked' : '' }} class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('file.bank_transfer') }}</span>
                                    </label>
                                </div>
                                @error('payment_methods') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div id="content-consent" class="tab-content hidden">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.profile_photo') }}</label>
                            <input type="file" name="profile_photo" accept="image/*" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-gray-900 file:text-white hover:file:bg-gray-800 dark:file:bg-gray-700 dark:hover:file:bg-gray-600">
                            @error('profile_photo') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.communication_preferences') }}</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="receive_appointment_reminders" value="1" {{ old('receive_appointment_reminders') ? 'checked' : '' }} class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('file.receive_appointment_reminders') }}</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="receive_lab_results" value="1" {{ old('receive_lab_results') ? 'checked' : '' }} class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('file.receive_lab_results') }}</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="receive_prescription_notifications" value="1" {{ old('receive_prescription_notifications') ? 'checked' : '' }} class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('file.receive_prescription_notifications') }}</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="receive_newsletter" value="1" {{ old('receive_newsletter') ? 'checked' : '' }} class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('file.receive_newsletter') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.additional_documents') }}</h3>
                            <input type="file" name="additional_documents[]" multiple accept=".pdf,.jpg,.jpeg,.png" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-gray-900 file:text-white hover:file:bg-gray-800 dark:file:bg-gray-700 dark:hover:file:bg-gray-600">
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('file.upload_documents_hint') }}</p>
                            @error('additional_documents') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 border border-gray-300 dark:border-gray-600 dark:bg-white dark:text-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('file.create_patient') }}
            </button>
            <a href="{{ route('patients.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ __('file.cancel') }}
            </a>
        </div>
    </form>
</div>

<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(b => {
        b.classList.remove('text-gray-900','dark:text-white','border-b-2','border-gray-900','dark:border-gray-400','bg-gray-50','dark:bg-gray-700/50');
        b.classList.add('text-gray-500','dark:text-gray-400','hover:text-gray-700','dark:hover:text-gray-300','hover:bg-gray-50','dark:hover:bg-gray-700/30');
    });
    document.getElementById('content-' + tabName).classList.remove('hidden');
    const btn = document.getElementById('tab-' + tabName);
    btn.classList.add('text-gray-900','dark:text-white','border-b-2','border-gray-900','dark:border-gray-400','bg-gray-50','dark:bg-gray-700/50');
    btn.classList.remove('text-gray-500','dark:text-gray-400');
}
</script>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection