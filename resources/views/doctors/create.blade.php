@extends('layouts.app')

@section('title', __('file.add_doctor'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" mb-6">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                <a href="{{ route('doctors.index') }}"
                    class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">{{ __('file.doctors') }}</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white">{{ __('file.add_doctor') }}</span>
            </div>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.add_new_doctor') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.create_doctor_record') }}</p>
        </div>

        <form method="POST" action="{{ route('doctors.store') }}" class="space-y-8" enctype="multipart/form-data">
            @csrf

            <div
                class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <!-- Mobile Tab Selector (Visible only on mobile) -->
                    <div class="sm:hidden p-4 bg-white dark:bg-gray-800">
                        <label for="mobile-tab-select" class="sr-only">Select a tab</label>
                        <select id="mobile-tab-select" onchange="switchTab(this.value)"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500">
                            <option value="personal">{{ __('file.personal_information') }}</option>
                            <option value="professional">{{ __('file.professional_details') }}</option>
                        </select>
                    </div>

                    <!-- Desktop/Tablet Tab Navigation (Hidden on mobile) -->
                    <nav class="hidden sm:flex overflow-x-auto no-scrollbar "
                        aria-label="Tabs">
                        <button type="button" onclick="switchTab('personal')" id="tab-personal"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-gray-400 bg-gray-50 dark:bg-gray-700/50 transition-all">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="hidden sm:inline">{{ __('file.personal_information') }}</span>
                                <span class="sm:hidden">{{ __('file.personal') }}</span>
                            </div>
                        </button>

                        <button type="button" onclick="switchTab('professional')" id="tab-professional"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.first_name') }}
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                        placeholder="{{ __('file.first_name') }}">
                                    @error('first_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.middle_name') }}</label>
                                    <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                        placeholder="{{ __('file.middle_name') }}">
                                    @error('middle_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.last_name') }}
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                        placeholder="{{ __('file.last_name') }}">
                                    @error('last_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    {{ __('file.login_account') }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.link_existing_user') }}</label>
                                        <select name="user_id" id="user_id_select"
                                            class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                            <option value="">{{ __('file.select_existing_user') }}</option>
                                            @foreach($availableUsers as $user)
                                                <option value="{{ $user->id }}" data-email="{{ e($user->email) }}"
                                                    data-phone="{{ $user->phone ? e($user->phone) : '' }}" 
                                                    {{ old('user_id') == $user->id ? 'selected' : '' }}
                                                    {{ $user->doctor ? 'disabled' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                    @if($user->doctor)
                                                        — {{ __('file.already_linked_to_therapist') }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('user_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                            {{ $message }}
                                        </p> @enderror
                                    </div>

                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.date_of_birth') }}</label>
                                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow [color-scheme:light] dark:[color-scheme:dark]">
                                    @error('date_of_birth') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.gender') }}
                                        <span class="text-red-500">*</span></label>
                                    <select name="gender" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                        <option value="">{{ __('file.select_gender') }}</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>
                                            {{ __('file.male') }}
                                        </option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>
                                            {{ __('file.female') }}
                                        </option>
                                    </select>
                                    @error('gender') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.address') }}</label>
                                <textarea name="address" rows="3"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                    placeholder="{{ __('file.address_placeholder') }}">{{ old('address') }}</textarea>
                                @error('address') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.city') }}</label>
                                    <input type="text" name="city" value="{{ old('city') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                        placeholder="{{ __('file.city') }}">
                                    @error('city') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.state') }}</label>
                                    <input type="text" name="state" value="{{ old('state') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                        placeholder="{{ __('file.state') }}">
                                    @error('state') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.zip_code') }}</label>
                                    <input type="text" name="zip_code" value="{{ old('zip_code') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                        placeholder="{{ __('file.zip_code') }}">
                                    @error('zip_code') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.phone') }}</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" minlength="7" maxlength="15"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                        placeholder="{{ __('file.phone_placeholder') }}">
                                    @error('phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.email') }}</label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                        placeholder="{{ __('file.email_placeholder') }}">
                                    @error('email') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                                    {{ __('file.emergency_contact') }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.name') }}</label>
                                        <input type="text" name="emergency_contact_name"
                                            value="{{ old('emergency_contact_name') }}"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                            placeholder="{{ __('file.full_name') }}">
                                        @error('emergency_contact_name') <p
                                            class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.phone') }}</label>
                                        <input type="text" name="emergency_contact_phone"
                                            value="{{ old('emergency_contact_phone') }}" minlength="7" maxlength="15"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                            placeholder="{{ __('file.phone_placeholder') }}">
                                        @error('emergency_contact_phone') <p
                                            class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="content-professional" class="tab-content hidden">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        {{ __('file.specializations') }} <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                        @foreach($specializations as $specialization)
                                            <label class="flex items-center cursor-pointer group">
                                                <input type="checkbox" name="specialization_ids[]" value="{{ $specialization->id }}"
                                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                                    {{ in_array($specialization->id, old('specialization_ids', [])) ? 'checked' : '' }}>
                                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {{ $specialization->name }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('specialization_ids')
                                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    @error('specialization_ids.*')
                                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('file.department') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select name="department_id" required
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
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
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('file.position') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select name="position_id" required
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
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

                            <div class="space-y-8">

                                <!-- Age Groups -->
                                <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        {{ __('file.age_groups_treated') }}
                                    </label>
                                    <div
                                        class="grid grid-cols-2 sm:grid-cols-3 gap-4 max-h-64 overflow-y-auto pr-2 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                        @foreach($ageGroups as $group)
                                            <label class="flex items-start cursor-pointer group">
                                                <input type="checkbox" name="age_group_ids[]" value="{{ $group->id }}"
                                                    class="w-4 h-4 mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                                    {{ in_array($group->id, old('age_group_ids', [])) ? 'checked' : '' }}>
                                                <span
                                                    class="ml-3 text-sm text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {{ $group->name }}
                                                    @if($group->min_age !== null || $group->max_age !== null)
                                                        <span class="block text-xs text-gray-500 dark:text-gray-400">
                                                            ({{ $group->min_age ?? '0' }}–{{ $group->max_age ?? '∞' }})
                                                        </span>
                                                    @endif
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('age_group_ids.*')
                                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Languages -->
                                <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        {{ __('file.languages_spoken') }}
                                    </label>
                                    <div
                                        class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                        @foreach($languages as $id => $name)
                                            <label class="flex items-center cursor-pointer group">
                                                <input type="checkbox" name="language_ids[]" value="{{ $id }}"
                                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                                    {{ in_array($id, old('language_ids', [])) ? 'checked' : '' }}>
                                                <span
                                                    class="ml-3 text-sm text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {{ $name }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('language_ids.*')
                                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-8">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    {{ __('file.treatments_offered') }} & Prices
                                </label>
                                <div
                                    class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                                    <div id="treatment-repeater" class="space-y-4">
                                        <div class="flex flex-col sm:flex-row gap-4 items-end treatment-row">
                                            <div class="flex-1">
                                                <label
                                                    class="block text-xs text-gray-500 dark:text-gray-400">Treatment</label>
                                                <select name="treatments[0][id]"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    <option value="">Select Treatment</option>
                                                    @foreach($treatments as $treatment)
                                                        <option value="{{ $treatment->id }}">{{ $treatment->name }}
                                                            ({{ $treatment->code ?? '—' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-500 dark:text-gray-400">Price</label>
                                                <input type="number" name="treatments[0][price]" step="0.01" min="0"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    placeholder="0.00">
                                            </div>
                                            <button type="button"
                                                class="remove-row text-red-600 hover:text-red-800 text-sm mt-6">Remove</button>
                                        </div>
                                    </div>

                                    <button type="button" id="add-treatment-row"
                                        class="mt-4 text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 text-sm font-medium">
                                        + Add another treatment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('file.create_doctor') }}
                </button>
                <a href="{{ route('doctors.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 dark:bg-transparent dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                b.classList.remove('text-gray-900', 'dark:text-white', 'border-b-2', 'border-gray-900', 'dark:border-gray-400', 'bg-gray-50', 'dark:bg-gray-700/50');
                b.classList.add('text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'hover:bg-gray-50', 'dark:hover:bg-gray-700/30');
            });

            // Update mobile select if present
            const mobileSelect = document.getElementById('mobile-tab-select');
            if (mobileSelect) mobileSelect.value = tabName;

            document.getElementById('content-' + tabName).classList.remove('hidden');
            const btn = document.getElementById('tab-' + tabName);
            if (btn) {
                btn.classList.add('text-gray-900', 'dark:text-white', 'border-b-2', 'border-gray-900', 'dark:border-gray-400', 'bg-gray-50', 'dark:bg-gray-700/50');
                btn.classList.remove('text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'hover:bg-gray-50', 'dark:hover:bg-gray-700/30');

                // Scroll the tab into view on mobile without shifting the entire page
                const nav = btn.closest('nav');
                if (nav && nav.classList.contains('flex')) {
                    const navRect = nav.getBoundingClientRect();
                    const btnRect = btn.getBoundingClientRect();
                    const offset = (btnRect.left - navRect.left) - (navRect.width / 2) + (btnRect.width / 2);
                    nav.scrollBy({ left: offset, behavior: 'smooth' });
                }
            }
        }


        let treatmentIndex = 1;

        document.getElementById('add-treatment-row')?.addEventListener('click', () => {
            const container = document.getElementById('treatment-repeater');
            const row = document.createElement('div');
            row.className = 'flex flex-col sm:flex-row gap-4 items-end treatment-row';
            row.innerHTML = `
                                    <div class="flex-1">
                                        <label class="block text-xs text-gray-500 dark:text-gray-400">Treatment</label>
                                        <select name="treatments[${treatmentIndex}][id]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">Select Treatment</option>
                                            @foreach($treatments as $treatment)
                                                <option value="{{ $treatment->id }}">{{ $treatment->name }} ({{ $treatment->code ?? '—' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-xs text-gray-500 dark:text-gray-400">Price</label>
                                        <input type="number" name="treatments[${treatmentIndex}][price]" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0.00">
                                    </div>
                                    <button type="button" class="remove-row text-red-600 hover:text-red-800 text-sm mt-6">Remove</button>
                                `;
            container.appendChild(row);
            treatmentIndex++;
        });

        document.addEventListener('click', e => {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('.treatment-row').remove();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const photoInput = document.getElementById('profile_photo_input');
            const preview = document.getElementById('profile-photo-preview');
            const img = document.getElementById('preview-img');

            if (photoInput) {
                photoInput.addEventListener('change', e => {
                    const file = e.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = ev => {
                            img.src = ev.target.result;
                            preview.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
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