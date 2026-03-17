{{-- resources/views/services/show.blade.php --}}
@extends('layouts.app')
@section('title', $service->name)

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <!-- Header Section -->
        <div class=" mb-6">
            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-3">
                <a href="{{ route('services.index') }}" class="hover:text-gray-900 dark:hover:text-gray-200">
                    {{ __('file.services') }}
                </a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white">{{ Str::limit($service->name, 30) }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-3xl font-semibold text-gray-900 dark:text-white mb-2">{{ $service->name }}</h1>
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="px-2.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300">
                            {{ $service->department?->name ?? __('file.no_department') }}
                        </span>
                        @php
                            $typeColors = match ($service->type) {
                                'Diagnostic' => 'border-blue-300 dark:border-blue-700 text-blue-700 dark:text-blue-300',
                                'Therapeutic' => 'border-green-300 dark:border-green-700 text-green-700 dark:text-green-300',
                                'Consultation' => 'border-purple-300 dark:border-purple-700 text-purple-700 dark:text-purple-300',
                                default => 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300',
                            };
                        @endphp
                        <span class="px-2.5 py-1 text-xs border rounded {{ $typeColors }}">
                            {{ $service->type }}
                        </span>
                        <span
                            class="px-2.5 py-1 text-xs border rounded {{ $service->is_active ? 'border-green-300 dark:border-green-700 text-green-700 dark:text-green-300' : 'border-red-300 dark:border-red-700 text-red-700 dark:text-red-300' }}">
                            {{ $service->is_active ? __('file.active') : __('file.inactive') }}
                        </span>
                        @if($service->requires_insurance)
                            <span
                                class="px-2.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300">
                                {{ __('file.insurance_required') }}
                            </span>
                        @endif
                        @if($service->requires_referral)
                            <span
                                class="px-2.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300">
                                {{ __('file.referral_required') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('services.edit', $service) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm rounded hover:bg-gray-800 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ __('file.edit') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Details -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Service Information -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-medium text-gray-900 dark:text-white">
                            {{ __('file.service_information') }}
                        </h2>
                    </div>
                    <div class="p-5 space-y-5">
                        <!-- Price / Duration / Type / Status Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="text-center p-3 border border-gray-200 dark:border-gray-700 rounded">
                                <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('file.price') }}</div>
                                <div class="text-xl font-semibold text-gray-900 dark:text-white">
                                    {{ $currency_code }} {{ number_format($service->price, 2) }}
                                </div>
                            </div>

                            <div class="text-center p-3 border border-gray-200 dark:border-gray-700 rounded">
                                <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('file.duration') }}</div>
                                <div class="text-xl font-semibold text-gray-900 dark:text-white">
                                    {{ $service->duration_minutes }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.minutes') }}</div>
                            </div>

                            <div class="text-center p-3 border border-gray-200 dark:border-gray-700 rounded">
                                <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('file.type') }}</div>
                                <div class="text-base font-medium text-gray-900 dark:text-white">{{ $service->type }}</div>
                            </div>

                            <div class="text-center p-3 border border-gray-200 dark:border-gray-700 rounded">
                                <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('file.status') }}</div>
                                <div
                                    class="text-base font-semibold {{ $service->is_active ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $service->is_active ? __('file.active') : __('file.inactive') }}
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($service->description)
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <label
                                    class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">{{ __('file.description') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white leading-relaxed">
                                    {{ $service->description }}
                                </p>
                            </div>
                        @endif

                        <!-- Patient Preparation -->
                        @if($service->patient_preparation)
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <label
                                    class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">{{ __('file.patient_preparation') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white leading-relaxed whitespace-pre-line">
                                    {{ $service->patient_preparation }}
                                </p>
                            </div>
                        @endif

                        <!-- Department -->
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div>
                                <label
                                    class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">{{ __('file.department') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $service->department?->name ?? '—' }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">{{ __('file.type') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $service->type }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assigned Equipment -->
                @if($service->equipment->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-base font-medium text-gray-900 dark:text-white">
                                {{ __('file.assigned_equipment') }} ({{ $service->equipment->count() }})
                            </h2>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($service->equipment as $equipment)
                                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded">
                                        <div class="font-medium text-sm text-gray-900 dark:text-white">{{ $equipment->name }}</div>
                                        <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                            {{ __('file.status') }}:
                                            <span
                                                class="{{ $equipment->status === 'Operational' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $equipment->status }}
                                            </span>
                                        </div>
                                        @if($equipment->last_maintenance)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ __('file.last_maintenance') }}: {{ $equipment->last_maintenance->format('d M Y') }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Assigned Providers / Doctors -->
                @if($service->doctors->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-base font-medium text-gray-900 dark:text-white">
                                {{ __('file.assigned_providers') }} ({{ $service->doctors->count() }})
                            </h2>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($service->doctors as $doctor)
                                    <div class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $doctor->getFullNameAttribute() }}
                                            </div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $doctor->primarySpecialization?->name ?? '—' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ $doctor->department?->name ?? '—' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-6">

                <!-- Properties -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-medium text-gray-900 dark:text-white">{{ __('file.properties') }}</h2>
                    </div>
                    <div class="p-5 space-y-2">
                        <div
                            class="flex items-center justify-between p-2.5 border border-gray-200 dark:border-gray-700 rounded">
                            <span
                                class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.requires_insurance') }}</span>
                            <div class="flex items-center">
                                @if($service->requires_insurance)
                                    <svg class="w-5 h-5 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-600" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between p-2.5 border border-gray-200 dark:border-gray-700 rounded">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.requires_referral') }}</span>
                            <div class="flex items-center">
                                @if($service->requires_referral)
                                    <svg class="w-5 h-5 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-600" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between p-2.5 border border-gray-200 dark:border-gray-700 rounded">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.is_active') }}</span>
                            <div class="flex items-center">
                                @if($service->is_active)
                                    <svg class="w-5 h-5 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-600" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Summary -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-medium text-gray-900 dark:text-white">{{ __('file.service_summary') }}
                        </h2>
                    </div>
                    <div class="p-5 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('file.price') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $currency_code }}
                                {{ number_format($service->price, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('file.duration') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $service->duration_minutes }}
                                {{ __('file.minutes') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('file.type') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $service->type }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('file.department') }}</span>
                            <span
                                class="font-medium text-gray-900 dark:text-white">{{ $service->department?->name ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('file.providers') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $service->doctors->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('file.equipment') }}</span>
                            <span
                                class="font-medium text-gray-900 dark:text-white">{{ $service->equipment->count() }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection