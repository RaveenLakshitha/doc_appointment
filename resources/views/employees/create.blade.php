@extends('layouts.app')

@section('title', __('file.add_employee'))

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('employees.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">{{ __('file.employees') }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">{{ __('file.add_employee') }}</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.add_new_employee') }}</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.create_employee_record') }}</p>
    </div>

    <form method="POST" action="{{ route('employees.store') }}" class="space-y-8" enctype="multipart/form-data">
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

                    <button type="button" onclick="switchTab('employment')" id="tab-employment"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="hidden sm:inline">{{ __('file.employment_details') }}</span>
                            <span class="sm:hidden">{{ __('file.employment') }}</span>
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

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.login_account') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.link_existing_user') }}</label>
                                    <select name="user_id" id="user_id_select" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                        <option value="">{{ __('file.select_existing_user') }}</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}"
                                                    data-phone="{{ $user->phone ? e($user->phone) : '' }}"
                                                    {{ old('user_id') == $user->id ? 'selected' : '' }}
                                                    {{ $user->employee ? 'disabled' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                                @if($user->employee)
                                                    — {{ __('file.already_linked') }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>

                                    @if($users->whereNotNull('employee')->count() > 0)
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('file.note_already_linked_users_disabled') }}
                                    </p>
                                @endif

                                    @error('user_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="create_new_user" value="1" id="create_new_user" class="form-checkbox h-4 w-4 text-blue-600 rounded focus:ring-blue-500" onclick="toggleNewUserFields(this)" {{ old('create_new_user') ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('file.or_create_new_account') }}</span>
                                    </label>

                                    <div id="new-user-fields" class="{{ old('create_new_user') ? '' : 'hidden' }} space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.new_user_email') }} <span class="text-red-500">*</span></label>
                                            <input type="email" name="new_user_email" value="{{ old('new_user_email') }}" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800" placeholder="employee@company.com">
                                            @error('new_user_email') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.password') }} <span class="text-red-500">*</span></label>
                                                <input type="password" name="new_user_password" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800">
                                                @error('new_user_password') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.confirm_password') }} <span class="text-red-500">*</span></label>
                                                <input type="password" name="new_user_password_confirmation" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.date_of_birth') }}</label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                @error('date_of_birth') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.gender') }}</label>
                                <select name="gender" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                    <option value="">{{ __('file.select_gender') }}</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('file.male') }}</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('file.female') }}</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>{{ __('file.other') }}</option>
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
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.postal_code') }}</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.postal_code') }}">
                                @error('postal_code') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.country') }}</label>
                            <input type="text" name="country" value="{{ old('country') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.country') }}">
                            @error('country') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.phone') }}</label>
                            <input type="text" name="phone" id="phone_input" value="{{ old('phone') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.phone_placeholder') }}">
                            @error('phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.photo') }}</label>
                            <input type="file" name="photo" accept="image/*" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-gray-900 file:text-white hover:file:bg-gray-800 dark:file:bg-gray-700 dark:hover:file:bg-gray-600">
                            @error('photo') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div id="content-professional" class="tab-content hidden">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.profession') }}</label>
                            <input type="text" name="profession" value="{{ old('profession') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.profession') }}">
                            @error('profession') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.specialization') }}</label>
                            <input type="text" name="specialization" value="{{ old('specialization') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.specialization') }}">
                            @error('specialization') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.professional_bio') }}</label>
                            <textarea name="professional_bio" rows="4" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none">{{ old('professional_bio') }}</textarea>
                            @error('professional_bio') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.education_qualification') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.degree') }}</label>
                                    <input type="text" name="degree" value="{{ old('degree') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.degree') }}">
                                    @error('degree') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.institution') }}</label>
                                    <input type="text" name="institution" value="{{ old('institution') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.institution') }}">
                                    @error('institution') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.year_completed') }}</label>
                                    <input type="number" name="year_completed" value="{{ old('year_completed') }}" min="1900" max="{{ date('Y') + 10 }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="2020">
                                    @error('year_completed') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.license_information') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.license_type') }}</label>
                                    <input type="text" name="license_type" value="{{ old('license_type') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.license_type') }}">
                                    @error('license_type') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.license_number') }}</label>
                                    <input type="text" name="license_number" value="{{ old('license_number') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.license_number') }}">
                                    @error('license_number') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.license_issue_date') }}</label>
                                    <input type="date" name="license_issue_date" value="{{ old('license_issue_date') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                    @error('license_issue_date') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.license_expiry_date') }}</label>
                                    <input type="date" name="license_expiry_date" value="{{ old('license_expiry_date') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                    @error('license_expiry_date') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.license_issuing_authority') }}</label>
                                    <input type="text" name="license_issuing_authority" value="{{ old('license_issuing_authority') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.license_issuing_authority') }}">
                                    @error('license_issuing_authority') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="content-employment" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.department') }} <span class="text-red-500">*</span></label>
                                <select name="department_id" required class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="">{{ __('file.select_department') }}</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.position') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="position" value="{{ old('position') }}" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.position') }}">
                                @error('position') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.employee_code') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="employee_code" value="{{ old('employee_code') }}" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.employee_code') }}">
                                @error('employee_code') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.reporting_to') }}</label>
                                <select name="reporting_to" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="">{{ __('file.none') }}</option>
                                    @foreach($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}" {{ old('reporting_to') == $supervisor->id ? 'selected' : '' }}>
                                            {{ $supervisor->first_name }} {{ $supervisor->middle_name ? $supervisor->middle_name . ' ' : '' }}{{ $supervisor->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('reporting_to') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.hire_date') }} <span class="text-red-500">*</span></label>
                                <input type="date" name="hire_date" value="{{ old('hire_date') }}" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                @error('hire_date') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.employment_type') }}</label>
                                <input type="text" name="employment_type" value="{{ old('employment_type') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.employment_type') }}">
                                @error('employment_type') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.salary') }}</label>
                                <input type="number" step="0.01" name="salary" value="{{ old('salary') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                @error('salary') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.payment_frequency') }}</label>
                                <input type="text" name="payment_frequency" value="{{ old('payment_frequency') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" placeholder="{{ __('file.payment_frequency') }}">
                                @error('payment_frequency') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
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
                {{ __('file.create_employee') }}
            </button>
            <a href="{{ route('employees.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
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
    const phoneInput = document.getElementById('phone_input');

    if (checkbox.checked) {
        fields.classList.remove('hidden');
        fields.querySelectorAll('input').forEach(input => input.setAttribute('required', 'required'));
        userSelect.disabled = true;
        userSelect.value = '';
        phoneInput.removeAttribute('readonly');
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
    const phoneInput = document.getElementById('phone_input');

    if (!select || !phoneInput) return;

    if (select.value) {
        const option = select.options[select.selectedIndex];
        const phone = option.getAttribute('data-phone') || '';
        phoneInput.value = phone;
        phoneInput.setAttribute('readonly', 'readonly');
    } else {
        phoneInput.value = '';
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