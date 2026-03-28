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
            <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 px-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button type="button" id="tab-btn-basic" onclick="switchTab('basic')" class="tab-btn border-indigo-500 text-indigo-600 dark:text-indigo-400 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('file.personal_information') }}
                    </button>
                    <button type="button" id="tab-btn-more" onclick="switchTab('more')" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('file.more_information') }}
                    </button>
                    <button type="button" id="tab-btn-tax" onclick="switchTab('tax')" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('file.tax_information') }}
                    </button>
                </nav>
            </div>
            <div class="p-6">
                
                <div id="tab-basic" class="tab-content block space-y-8">
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

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.date_of_birth') }}</label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white [color-scheme:light] dark:[color-scheme:dark] transition-shadow">
                                @error('date_of_birth') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.age') }}</label>
                                <input type="number" name="age" value="{{ old('age') }}" min="0" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="e.g. 30">
                                @error('age') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
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
                            <select name="marital_status" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.preferred_contact_method') }}</label>
                            <select name="preferred_contact_method" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="phone" {{ old('preferred_contact_method') == 'phone' ? 'selected' : '' }}>{{ __('file.phone') }}</option>
                                <option value="whatsapp" {{ old('preferred_contact_method') == 'whatsapp' ? 'selected' : '' }}>{{ __('file.whatsapp') }}</option>
                                <option value="sms" {{ old('preferred_contact_method') == 'sms' ? 'selected' : '' }}>{{ __('file.sms') }}</option>
                            </select>
                            @error('preferred_contact_method') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Preferences</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Age Group</label>
                                    <select name="age_group_id" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                        <option value="">Select Age Group</option>
                                        @foreach($ageGroups as $group)
                                            <option value="{{ $group->id }}" {{ old('age_group_id') == $group->id ? 'selected' : '' }}>
                                                {{ $group->name }}
                                                @if($group->min_age !== null || $group->max_age !== null)
                                                    ({{ $group->min_age ?? '0' }}–{{ $group->max_age ?? '∞' }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('age_group_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preferred Language</label>
                                    <select name="preferred_language_id" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                        <option value="">Select Language</option>
                                        @foreach($languages as $id => $name)
                                            <option value="{{ $id }}" {{ old('preferred_language_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('preferred_language_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
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
                                
                            </div>
                        </div>

                    </div>

                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-4 mt-12 mb-6">{{ __('file.medical_information') }}</h2>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.attended_psychotherapy') }}</label>
                                <select name="attended_psychotherapy" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                    <option value="0" {{ old('attended_psychotherapy', 0) == '0' ? 'selected' : '' }}>{{ __('file.no') }}</option>
                                    <option value="1" {{ old('attended_psychotherapy', 0) == '1' ? 'selected' : '' }}>{{ __('file.yes') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.preferred_session_time') }}</label>
                                <input type="text" id="preferred_session_time_picker" name="preferred_session_time" value="{{ old('preferred_session_time') ? date('h:i A', strtotime(old('preferred_session_time'))) : '' }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.select_time') ?? 'Select Time' }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.recommended_by') }}</label>
                                <input type="text" name="recommended_by" value="{{ old('recommended_by') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.recommended_by_ph') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document / Image</label>
                                <input type="file" name="document" id="document_upload" accept="image/*,.pdf,.doc,.docx" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" onchange="previewDocument(this)">
                                @error('document') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                
                                <div id="document_preview_container" class="mt-3 hidden">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Selected File Preview:</p>
                                    <img id="document_image_preview" src="" alt="Preview" class="max-w-xs rounded-lg border border-gray-200 dark:border-gray-700 hidden" style="max-height: 200px;">
                                    <div id="document_file_preview" class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700 hidden">
                                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <span id="document_file_name" class="text-sm text-gray-700 dark:text-gray-300 truncate"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="tab-more" class="tab-content hidden space-y-8">
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

                <div id="tab-tax" class="tab-content hidden space-y-8">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.tax_id') }}</label>
                                <input type="text" name="tax_id" value="{{ old('tax_id') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.tax_id') }}">
                                @error('tax_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.tax_full_name') }}</label>
                                <input type="text" name="tax_full_name" value="{{ old('tax_full_name') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.tax_full_name') }}">
                                @error('tax_full_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.tax_postal_code') }}</label>
                                <input type="text" name="tax_postal_code" value="{{ old('tax_postal_code') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.tax_postal_code') }}">
                                @error('tax_postal_code') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.tax_regime') }}</label>
                                <input type="text" name="tax_regime" value="{{ old('tax_regime') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.tax_regime') }}">
                                @error('tax_regime') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.tax_invoice_usage') }}</label>
                                <select name="tax_invoice_usage" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                    <option value="">{{ __('file.select') }}</option>
                                    @foreach($cfdiOptions as $code => $langKey)
                                        <option value="{{ $code }}" {{ old('tax_invoice_usage') == $code ? 'selected' : '' }}>
                                            {{ __($langKey) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tax_invoice_usage') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.tax_cfdi_upload') }}</label>
                                <input type="file" name="tax_cfdi_upload" accept=".pdf" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                @error('tax_cfdi_upload') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2 justify-end">
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
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('#tab-more .text-red-600') || document.querySelector('#tab-more .text-red-400')) {
        switchTab('more');
    }
    if (document.querySelector('#tab-tax .text-red-600') || document.querySelector('#tab-tax .text-red-400')) {
        switchTab('tax');
    }
});

function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.remove('block');
        el.classList.add('hidden');
    });
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
        el.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    });
    
    document.getElementById('tab-' + tabId).classList.remove('hidden');
    document.getElementById('tab-' + tabId).classList.add('block');
    
    const activeBtn = document.getElementById('tab-btn-' + tabId);
    activeBtn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    activeBtn.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
}

function previewDocument(input) {
    const container = document.getElementById('document_preview_container');
    const imgPreview = document.getElementById('document_image_preview');
    const filePreview = document.getElementById('document_file_preview');
    const fileName = document.getElementById('document_file_name');

    if (input.files && input.files[0]) {
        container.classList.remove('hidden');
        const file = input.files[0];
        
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
                imgPreview.classList.remove('hidden');
                filePreview.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            imgPreview.classList.add('hidden');
            fileName.textContent = file.name;
            filePreview.classList.remove('hidden');
        }
    } else {
        container.classList.add('hidden');
        imgPreview.classList.add('hidden');
        filePreview.classList.add('hidden');
    }
}


</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#preferred_session_time_picker", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K",
    });
});
</script>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection