{{-- resources/views/doctors/show.blade.php --}}
@extends('layouts.app')

@section('title', $doctor->full_name)

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('doctors.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                {{ __('file.doctors') }}
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white font-medium">{{ Str::limit($doctor->full_name, 30) }}</span>
        </div>
    </div>

    <!-- Main Layout: Profile Card (Left) + Content (Right) -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- LEFT SIDE: Profile Card -->
        <div class="lg:w-80 xl:w-96 flex-shrink-0">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 sticky top-6">
                <!-- Profile Photo -->
                <div class="p-6 text-center border-b border-gray-200 dark:border-gray-700">
                    <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 border-4 border-gray-200 dark:border-gray-600 mx-auto mb-4">
                        @if($doctor->profile_photo)
                            <img src="{{ Storage::disk('public')->url($doctor->profile_photo) }}" alt="{{ $doctor->full_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $doctor->full_name }}</h2>
                    
                    <div class="flex flex-wrap items-center justify-center gap-2 mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $doctor->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' }}">
                            {{ $doctor->is_active ? __('file.active') : __('file.inactive') }}
                        </span>
                        <!-- Fixed: Now shows position from OptionList -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                            {{ $doctor->positionOption?->name ?? '—' }}
                        </span>
                    </div>

                    <a href="{{ route('doctors.edit', $doctor) }}"
                       class="inline-flex items-center px-5 py-2.5 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors w-full justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{ __('file.edit_doctor') }}
                    </a>
                </div>

                <!-- Quick Stats -->
                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.total_appointments') }}</div>
                        <div class="text-l font-bold text-blue-600 dark:text-blue-400">{{ $doctor->appointments_count }}</div>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.hourly_rate') }}</div>
                        <div class="text-l font-bold text-green-600 dark:text-green-400">${{ number_format($doctor->hourly_rate, 2) }}</div>
                    </div>
                </div>

                <!-- Contact & Basic Info -->
                <div class="p-6 border-t border-gray-200 dark:border-gray-700 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.department') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $doctor->department?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.primary_specialty') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $doctor->primarySpecialization?->name ?? '—' }}</p>
                    </div>
                    <!-- Added Position here for better visibility -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.position') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $doctor->positionOption?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.license_number') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $doctor->license_number ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: Tabbed Content -->
        <div class="w-full lg:col-span-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700" x-data="{ activeTab: 'profile' }">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex space-x-4 px-4 overflow-x-auto">
                        <button @click="activeTab = 'profile'" 
                                :class="activeTab === 'profile' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                            {{ __('file.profile') }}
                        </button>
                        <button @click="activeTab = 'appointments'" 
                                :class="activeTab === 'appointments' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                            {{ __('file.appointments') }}
                        </button>
                        <button @click="activeTab = 'patients'" 
                                :class="activeTab === 'patients' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                            {{ __('file.patients') }}
                        </button>
                        <button @click="activeTab = 'schedule'" 
                                :class="activeTab === 'schedule' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                            {{ __('file.schedule') }}
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Profile Tab -->
                    <div x-show="activeTab === 'profile'" x-cloak>
                        <!-- Professional Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.professional_information') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.position') }}</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white font-medium">{{ $doctor->positionOption?->name ?? '—' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.license_expiry') }}</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $doctor->license_expiry_date?->format('d M Y') ?? '—' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.years_of_experience') }}</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $doctor->years_experience }} {{ __('file.years') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Education & Certifications -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.education_certifications') }}</h3>
                            <div class="space-y-6">
                                @if($doctor->education)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">{{ __('file.education') }}</label>
                                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $doctor->education }}</p>
                                    </div>
                                @endif
                                @if($doctor->certifications)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">{{ __('file.certifications') }}</label>
                                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $doctor->certifications }}</p>
                                    </div>
                                @endif
                                @if($doctor->qualifications)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">{{ __('file.qualifications') }}</label>
                                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $doctor->qualifications }}</p>
                                    </div>
                                @endif
                                @if(!$doctor->education && !$doctor->certifications && !$doctor->qualifications)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_education_info') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Appointments Tab -->
                    <div x-show="activeTab === 'appointments'" x-cloak>
                        @if($doctor->appointments->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($doctor->appointments as $appointment)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-600">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $appointment->patient->full_name ?? __('file.unknown_patient') }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $appointment->appointment_datetime->format('d M Y, h:i A') }}
                                            </p>
                                        </div>
                                        <span class="inline-flex px-2.5 py-0.5 rounded text-xs font-medium {{ $appointment->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_appointments_found') }}</p>
                        @endif
                    </div>

                    <!-- Patients Tab -->
                    <div x-show="activeTab === 'patients'" x-cloak>
                        @php
                            $uniquePatients = $doctor->appointments->map(function ($appointment) {
                                return $appointment->patient;
                            })->unique('id');
                        @endphp

                        @if($uniquePatients->isNotEmpty())
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($uniquePatients as $patient)
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-600">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $patient->full_name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $patient->phone ?? __('file.no_phone') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_patients_found') }}</p>
                        @endif
                    </div>

                    <!-- Schedule Tab -->
                    <div x-show="activeTab === 'schedule'" x-cloak>
                        @if($doctor->schedules->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($doctor->schedules as $schedule)
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-600">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                                            {{ implode(', ', $schedule->days_of_week) }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $schedule->start_time->format('h:i A') }} - {{ $schedule->end_time->format('h:i A') }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ __('file.room') }}: {{ $schedule->room?->name ?? '—' }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_schedule_available') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection