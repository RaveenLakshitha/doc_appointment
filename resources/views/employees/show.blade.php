@extends('layouts.app')

@section('title', $employee->full_name)

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" mb-6">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                <a href="{{ route('employees.index') }}"
                    class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    {{ __('file.employees') }}
                </a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white font-medium">{{ Str::limit($employee->full_name, 30) }}</span>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Sidebar – Profile Card -->
            <div class="lg:w-80 xl:w-96 flex-shrink-0">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 sticky top-6">
                    <div class="p-6 text-center border-b border-gray-200 dark:border-gray-700">
                        <div
                            class="w-32 h-32 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 border-4 border-gray-200 dark:border-gray-600 mx-auto mb-4">
                            @if($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $employee->full_name }}</h2>

                        <div class="flex flex-wrap items-center justify-center gap-2 mb-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $employee->status ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' }}">
                                {{ $employee->status ? __('file.active') : __('file.inactive') }}
                            </span>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                {{ $employee->employee_code ?? '—' }}
                            </span>
                        </div>

                        <a href="{{ route('employees.edit', $employee) }}"
                            class="inline-flex items-center px-5 py-2.5 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors w-full justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            {{ __('file.edit') }}
                        </a>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">
                                {{ __('file.hire_date') }}
                            </div>
                            <div class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $employee->hire_date?->format('d M Y') ?? '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200 dark:border-gray-700 space-y-3">
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.department') }}</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $employee->department?->name ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.position') }}</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $employee->position ?? '—' }}</p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.reporting_to') }}</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $employee->supervisor?->full_name ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.employee_code') }}</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">
                                {{ $employee->employee_code ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content – Tabs -->
            <div class="w-full">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700"
                    x-data="{ activeTab: 'profile' }">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <!-- Mobile Tab Selector (Visible only on mobile) -->
                        <div class="sm:hidden p-4 bg-white dark:bg-gray-800">
                            <label for="mobile-tab-select" class="sr-only">Select a tab</label>
                            <select id="mobile-tab-select" x-model="activeTab"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500">
                                <option value="profile">{{ __('file.profile') }}</option>
                            </select>
                        </div>

                        <!-- Desktop/Tablet Tab Navigation (Hidden on mobile) -->
                        <nav class="hidden sm:flex space-x-4 px-4 overflow-x-auto no-scrollbar "
                            aria-label="Tabs">
                            <button @click="activeTab = 'profile'"
                                :class="activeTab === 'profile' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                                {{ __('file.profile') }}
                            </button>
                        </nav>
                    </div>

                    <div class="p-6">
                        <!-- PROFILE TAB -->
                        <div x-show="activeTab === 'profile'" x-cloak>
                            <div class="space-y-8">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                        {{ __('file.personal_information') }}
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.first_name') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $employee->first_name ?? '—' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.middle_name') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $employee->middle_name ?? '—' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.last_name') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $employee->last_name ?? '—' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.date_of_birth') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $employee->date_of_birth?->format('d M Y') ?? '—' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.gender') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white capitalize">
                                                {{ $employee->gender_display ?? $employee->gender ?? '—' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.phone') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $employee->user?->phone ?? '—' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.email') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $employee->user?->email ?? '—' }}
                                            </p>
                                        </div>
                                    </div>

                                    @if($employee->address || $employee->city || $employee->state || $employee->postal_code || $employee->country)
                                                                    <div class="mt-6">
                                                                        <label
                                                                            class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.address') }}</label>
                                                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                                            {{ $employee->address ?? '' }}<br>
                                                                            {{ trim(implode(', ', array_filter([
                                            $employee->city,
                                            $employee->state,
                                            $employee->postal_code,
                                            $employee->country
                                        ]))) ?: '—' }}
                                                                        </p>
                                                                    </div>
                                    @endif

                                    @if($employee->emergency_contact_name || $employee->emergency_contact_phone)
                                        <div class="mt-6">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                                {{ __('file.emergency_contact') }}
                                            </h4>
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                {{ $employee->emergency_contact_name ?? '—' }}<br>
                                                {{ $employee->emergency_contact_phone ?? '—' }}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Professional & Employment -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                        {{ __('file.professional_and_employment') }}
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.department') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $employee->department?->name ?? '—' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.position') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $employee->position ?? '—' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.profession') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $employee->profession ?? '—' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.specialization') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $employee->specialization ?? '—' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.hire_date') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $employee->hire_date?->format('d M Y') ?? '—' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.employment_type') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $employee->employment_type ?? '—' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.salary') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $employee->salary ? number_format($employee->salary, 2) : '—' }}
                                            </p>
                                        </div>
                                    </div>

                                    @if($employee->professional_bio)
                                        <div class="mt-6">
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.professional_bio') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-line">
                                                {{ $employee->professional_bio }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection