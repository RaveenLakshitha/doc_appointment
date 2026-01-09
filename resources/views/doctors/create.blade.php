@extends('layouts.app')
@section('title', __('file.add_doctor'))

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('doctors.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">{{ __('file.doctors') }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">{{ __('file.add_doctor') }}</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.add_new_doctor') }}</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.create_doctor_record') }}</p>
    </div>

    <form method="POST" action="{{ route('doctors.store') }}" class="space-y-8" enctype="multipart/form-data">
        @csrf

        <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex overflow-x-auto scrollbar-hide" aria-label="Tabs">
                    <button type="button" onclick="switchTab('personal')" id="tab-personal"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-gray-400 bg-gray-50 dark:bg-gray-700/50 transition-all">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="hidden sm:inline">{{ __('file.personal_information') }}</span>
                            <span class="sm:hidden">{{ __('file.personal') }}</span>
                        </div>
                    </button>

                    <button type="button" onclick="switchTab('professional')" id="tab-professional"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="hidden sm:inline">{{ __('file.professional_details') }}</span>
                            <span class="sm:hidden">{{ __('file.professional') }}</span>
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
                                <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.first_name') }}">
                                @error('first_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.middle_name') }}</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.middle_name') }}">
                                @error('middle_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.last_name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.last_name') }}">
                                @error('last_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="col-span-1 md:col-span-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('file.login_account') }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('file.link_existing_user') }}
                                    </label>
                                    <select name="user_id" id="user_id_select" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                        <option value="">{{ __('file.select_existing_user') }}</option>
                                        @foreach($availableUsers as $user)
                                            <option value="{{ $user->id }}"
                                                    data-email="{{ e($user->email) }}"
                                                    data-phone="{{ $user->phone ? e($user->phone) : '' }}"
                                                    {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="create_new_user" value="1" id="create_new_user" class="form-checkbox h-4 w-4 text-blue-600 rounded focus:ring-blue-500"
                                               onclick="toggleNewUserFields(this)" {{ old('create_new_user') ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('file.or_create_new_account') }}
                                        </span>
                                    </label>

                                    <div id="new-user-fields" class="{{ old('create_new_user') ? '' : 'hidden' }} space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ __('file.new_user_email') }} <span class="text-red-500">*</span>
                                            </label>
                                            <input type="email" name="new_user_email" value="{{ old('new_user_email') }}"
                                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800"
                                                   placeholder="doctor.new@hospital.com">
                                            @error('new_user_email')
                                                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    {{ __('file.password') }} <span class="text-red-500">*</span>
                                                </label>
                                                <input type="password" name="new_user_password"
                                                       class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800">
                                                @error('new_user_password')
                                                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    {{ __('file.confirm_password') }} <span class="text-red-500">*</span>
                                                </label>
                                                <input type="password" name="new_user_password_confirmation"
                                                       class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.date_of_birth') }} <span class="text-red-500">*</span></label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white [color-scheme:light] dark:[color-scheme:dark] transition-shadow">
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.address') }}</label>
                            <textarea name="address" rows="3" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none" placeholder="{{ __('file.address_placeholder') }}">{{ old('address') }}</textarea>
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
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.phone') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="phone" id="phone_input" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.phone_placeholder') }}">
                                @error('phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.email') }} <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email_input" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.email_placeholder') }}">
                                @error('email') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.emergency_contact') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.name') }}</label>
                                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.full_name') }}">
                                    @error('emergency_contact_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.phone') }}</label>
                                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.phone_placeholder') }}">
                                    @error('emergency_contact_phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.profile_photo') }}</label>
                            <input type="file" name="profile_photo" accept="image/*" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-gray-900 file:text-white hover:file:bg-gray-800 dark:file:bg-gray-700 dark:hover:file:bg-gray-600">
                            @error('profile_photo') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div id="content-professional" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.primary_specialty') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="primary_specialization_id" required
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 
                                            focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all">
                                    <option value="">{{ __('file.select_specialization') }}</option>
                                    @foreach($specializations as $specialization)
                                        <option value="{{ $specialization->id }}" {{ old('primary_specialization_id') == $specialization->id ? 'selected' : '' }}>
                                            {{ $specialization->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('primary_specialization_id')
                                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.license_number') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="license_number" value="{{ old('license_number') }}" required
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                       placeholder="{{ __('file.license_number') }}">
                                @error('license_number') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.license_expiry_date') }} <span class="text-red-500">*</span></label>
                                <input type="date" name="license_expiry_date" value="{{ old('license_expiry_date') }}" required
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white [color-scheme:light] dark:[color-scheme:dark] transition-shadow">
                                @error('license_expiry_date') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.qualifications') }}</label>
                                <input type="text" name="qualifications" value="{{ old('qualifications') }}"
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                       placeholder="{{ __('file.qualifications_placeholder') }}">
                                @error('qualifications') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.years_experience') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="years_experience" value="{{ old('years_experience') }}" required min="0" max="60"
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                       placeholder="{{ __('file.years_experience') }}">
                                @error('years_experience') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.education') }}</label>
                            <textarea name="education" rows="3"
                                      class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                      placeholder="{{ __('file.education_placeholder') }}">{{ old('education') }}</textarea>
                            @error('education') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.certifications') }}</label>
                            <textarea name="certifications" rows="3"
                                      class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                      placeholder="{{ __('file.certifications_placeholder') }}">{{ old('certifications') }}</textarea>
                            @error('certifications') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.department') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="department_id" required
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                            bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                            focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all">
                                    <option value="">{{ __('file.select_department') }}</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.position') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="position_id" required
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg 
                                            bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                            focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all">
                                    <option value="">{{ __('file.select_position') }}</option>
                                    @foreach($positions as $id => $name)
                                        <option value="{{ $id }}" {{ old('position_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('position_id')
                                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.hourly_rate') }} <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="hourly_rate" value="{{ old('hourly_rate') }}" required
                                   class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                   placeholder="{{ __('file.hourly_rate_placeholder') }}">
                            @error('hourly_rate') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 border border-gray-300 dark:border-gray-600 dark:bg-white dark:text-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('file.create_doctor') }}
            </button>
            <a href="{{ route('doctors.index') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
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
    document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-gray-900', 'dark:text-white', 'border-b-2', 'border-gray-900', 'dark:border-gray-400', 'bg-gray-50', 'dark:bg-gray-700/50');
        btn.classList.add('text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'hover:bg-gray-50', 'dark:hover:bg-gray-700/30');
    });
    document.getElementById('content-' + tabName).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.remove('text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'hover:bg-gray-50', 'dark:hover:bg-gray-700/30');
    activeTab.classList.add('text-gray-900', 'dark:text-white', 'border-b-2', 'border-gray-900', 'dark:border-gray-400', 'bg-gray-50', 'dark:bg-gray-700/50');
}

function toggleNewUserFields(checkbox) {
    const fields = document.getElementById('new-user-fields');
    const userSelect = document.getElementById('user_id_select');
    const emailInput = document.getElementById('email_input');
    const phoneInput = document.getElementById('phone_input');

    if (checkbox.checked) {
        fields.classList.remove('hidden');
        fields.querySelectorAll('input').forEach(input => input.setAttribute('required', 'required'));
        userSelect.disabled = true;
        userSelect.value = '';
        emailInput.removeAttribute('readonly');
        phoneInput.removeAttribute('readonly');
        emailInput.value = '';
        phoneInput.value = '';
    } else {
        fields.classList.add('hidden');
        fields.querySelectorAll('input').forEach(input => input.removeAttribute('required'));
        userSelect.disabled = false;
        fillUserData();
    }
}

function fillUserData() {
    const select = document.getElementById('user_id_select');
    const emailInput = document.getElementById('email_input');
    const phoneInput = document.getElementById('phone_input');
    const checkbox = document.getElementById('create_new_user');

    if (!select || !emailInput || !phoneInput) return;

    if (select.value) {
        const option = select.options[select.selectedIndex];
        const email = option.getAttribute('data-email') || '';
        const phone = option.getAttribute('data-phone') || '';

        emailInput.value = email;
        phoneInput.value = phone;

        emailInput.setAttribute('readonly', 'readonly');
        phoneInput.removeAttribute('readonly');

        checkbox.checked = false;
        document.getElementById('new-user-fields').classList.add('hidden');
        document.getElementById('new-user-fields').querySelectorAll('input').forEach(input => input.removeAttribute('required'));
    } else {
        emailInput.value = '';
        phoneInput.value = '';
        emailInput.removeAttribute('readonly');
        phoneInput.removeAttribute('readonly');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const userSelect = document.getElementById('user_id_select');
    const checkbox = document.getElementById('create_new_user');

    if (userSelect) {
        userSelect.addEventListener('change', fillUserData);
        fillUserData();
    }

    if (checkbox && checkbox.checked) {
        toggleNewUserFields(checkbox);
    }
});
</script>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection