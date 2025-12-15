{{-- resources/views/services/show.blade.php --}}
@extends('layouts.app')

@section('title', $service->name)

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
            <a href="{{ route('services.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                {{ __('Services') }}
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white font-medium">{{ Str::limit($service->name, 40) }}</span>
        </div>

        <!-- Header -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-6">
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/30 dark:to-indigo-800/30 border-4 border-dashed border-indigo-300 dark:border-indigo-700 flex-shrink-0 flex items-center justify-center">
                        <svg class="w-12 h-12 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ $service->name }}</h1>
                        <div class="flex flex-wrap items-center gap-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ $service->department?->name ?? '—' }}
                            </span>

                            @php
                                $typeColor = match($service->type) {
                                    'Diagnostic'   => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
                                    'Therapeutic'  => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                                    'Consultation' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300',
                                    default        => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                                };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $typeColor }}">
                                {{ $service->type }}
                            </span>

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $service->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' }}">
                                {{ $service->is_active ? 'Active' : 'Inactive' }}
                            </span>

                            @if($service->requires_insurance)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300">
                                    Requires Insurance
                                </span>
                            @endif

                            @if($service->requires_referral)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300">
                                    Requires Referral
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('services.edit', $service) }}"
                   class="inline-flex items-center px-5 py-2.5 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    {{ __('Edit Service') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Service Details
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Duration</label>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $service->duration_minutes }} minutes</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Price</label>
                            <p class="mt-1 text-2xl font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($service->price, 2) }}</p>
                        </div>
                    </div>

                    @if($service->description)
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Description</label>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $service->description }}</p>
                        </div>
                    @endif

                    @if($service->patient_preparation)
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Patient Preparation Instructions</label>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $service->patient_preparation }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assigned Equipment -->
            @if($service->equipment->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17.25v-2.5a2.5 2.5 0 015 0v2.5m-5-10h5m-5 5h5m-7.5-5h15m-15 10h15"/>
                            </svg>
                            Assigned Equipment ({{ $service->equipment->count() }})
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($service->equipment as $eq)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $eq->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Status: <span class="{{ $eq->status === 'Operational' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ $eq->status }}</span>
                                        </div>
                                        @if($eq->last_maintenance)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Last Maintenance: {{ $eq->last_maintenance->format('d M Y') }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Assigned Providers (Doctors) -->
            @if($service->doctors->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Assigned Providers ({{ $service->doctors->count() }})
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($service->doctors as $doctor)
                                <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <div class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $doctor->getFullNameAttribute() }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $doctor->primarySpecialization?->name ?? 'General' }} • {{ $doctor->department?->name }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Availability Slots -->
            @if($service->availabilitySlots->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                            Availability Slots ({{ $service->availabilitySlots->count() }})
                        </h2>
                    </div>
                    <div class="p-4">
                        <div class="space-y-2">
                            @foreach($service->availabilitySlots->sortBy('day_of_week') as $slot)
                                <div class="flex items-center justify-between text-sm py-2 px-3 rounded bg-indigo-50 dark:bg-gray-700">
                                    <span class="font-medium text-gray-900 dark:text-gray-50">{{ $slot->day_of_week }}</span>
                                    <span class="text-gray-600 dark:text-gray-50">
                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">

            <!-- Service Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Service Summary
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Status</span>
                        <span class="text-sm font-medium {{ $service->is_active ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $service->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Requires Insurance</span>
                        <span class="text-sm font-medium">{{ $service->requires_insurance ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Requires Referral</span>
                        <span class="text-sm font-medium">{{ $service->requires_referral ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Equipment</span>
                        <span class="text-sm font-medium">{{ $service->equipment->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Providers</span>
                        <span class="text-sm font-medium">{{ $service->doctors->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Availability Slots</span>
                        <span class="text-sm font-medium">{{ $service->availabilitySlots->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/30 dark:to-indigo-800/30 rounded-xl shadow-sm border border-indigo-200 dark:border-indigo-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-indigo-200 dark:border-indigo-600">
                    <h2 class="text-lg font-semibold text-indigo-900 dark:text-indigo-300 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Quick Actions
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('services.edit', $service) }}" class="w-full flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Service Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection