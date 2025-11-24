{{-- resources/views/doctors/show.blade.php --}}
@extends('layouts.app')
@section('title', $doctor->full_name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8 max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 border-2 border-dashed border-gray-300 dark:border-gray-600">
                    @if($doctor->profile_photo)
                        <img src="{{ Storage::url($doctor->profile_photo) }}" alt="{{ $doctor->full_name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $doctor->full_name }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $doctor->primary_specialty }} 
                        @if($doctor->secondary_specialty) • {{ $doctor->secondary_specialty }} @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Contact Info -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">{{ __('file.contact') }}</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('file.email') }}</dt>
                        <dd class="text-gray-900 dark:text-white font-medium">{{ $doctor->email ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('file.phone') }}</dt>
                        <dd class="text-gray-900 dark:text-white font-medium">{{ $doctor->phone ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('file.address') }}</dt>
                        <dd class="text-gray-900 dark:text-white font-medium text-right">
                            {{ $doctor->address ? "{$doctor->address}, {$doctor->city}, {$doctor->state} {$doctor->zip_code}" : '—' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Professional Info -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">{{ __('file.professional') }}</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('file.license') }}</dt>
                        <dd class="text-gray-900 dark:text-white font-mono">{{ $doctor->license_number ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('file.expiry') }}</dt>
                        <dd class="text-gray-900 dark:text-white font-medium">
                            {{ $doctor->license_expiry_date ? \Carbon\Carbon::parse($doctor->license_expiry_date)->format('M d, Y') : '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('file.department') }}</dt>
                        <dd class="text-gray-900 dark:text-white font-medium">{{ $doctor->department ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('file.position') }}</dt>
                        <dd class="text-gray-900 dark:text-white font-medium">{{ $doctor->position ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('file.hourly_rate') }}</dt>
                        <dd class="text-gray-900 dark:text-white font-medium">${{ number_format($doctor->hourly_rate, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('file.appointments') }}</dt>
                        <dd class="text-gray-900 dark:text-white font-medium">{{ $doctor->appointments_count }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Personal Info -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">{{ __('file.personal') }}</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('file.dob') }}</dt>
                        <dd class="text-gray-900 dark:text-white font-medium">
                            {{ $doctor->date_of_birth ? \Carbon\Carbon::parse($doctor->date_of_birth)->format('M d, Y') : '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('file.gender') }}</dt>
                        <dd class="text-gray-900 dark:text-white font-medium">
                            {{ $doctor->gender ? ucfirst($doctor->gender) : '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('file.status') }}</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium
                                {{ $doctor->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                {{ $doctor->is_active ? __('file.active') : __('file.inactive') }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Qualifications & Certifications -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">{{ __('file.qualifications') }}</h3>
                <div class="text-sm text-gray-900 dark:text-white">
                    {{ $doctor->qualifications ?? '—' }}
                </div>
                @if($doctor->certifications)
                    <div class="mt-3">
                        <h4 class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('file.certifications') }}</h4>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $doctor->certifications }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <a href="{{ route('doctors.edit', $doctor) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                {{ __('file.edit') }}
            </a>
            <a href="{{ route('doctors.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                {{ __('file.back_to_list') }}
            </a>
        </div>
    </div>
</div>
@endsection