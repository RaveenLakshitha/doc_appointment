{{-- resources/views/appointment-requests/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Request #'.$appointmentRequest->id.' - '.$appointmentRequest->patient->full_name)

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <!-- Breadcrumb -->
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
            <a href="{{ route('appointment_requests.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                Appointment Requests
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white font-medium">
                #{{ $appointmentRequest->id }} - {{ Str::limit($appointmentRequest->patient->full_name, 25) }}
            </span>
        </div>

        <!-- Header -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                    {{ Str::upper(substr($appointmentRequest->patient->full_name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Appointment Request from {{ $appointmentRequest->patient->full_name }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            @switch($appointmentRequest->status)
                                @case('pending')   bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 @break
                                @case('approved')  bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 @break
                                @case('rejected')  bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 @break
                                @case('cancelled') bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 @break
                                @default bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400
                            @endswitch">
                            {{ ucfirst($appointmentRequest->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                @if($appointmentRequest->status === 'pending')
                    <form method="POST" action="{{ route('appointment_requests.approve', $appointmentRequest) }}" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-all shadow-sm hover:shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Approve
                        </button>
                    </form>

                    <form method="POST" action="{{ route('appointment_requests.reject', $appointmentRequest) }}" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit"
                                onclick="return confirm('Are you sure you want to reject this request?')"
                                class="inline-flex items-center px-5 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-all shadow-sm hover:shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Reject
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('appointment_requests.cancel', $appointmentRequest) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit"
                            onclick="return confirm('Are you sure you want to cancel this request?')"
                            class="inline-flex items-center px-5 py-2.5 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-all shadow-sm hover:shadow-md">
                        Cancel Request
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Request Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Request Details
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Requested Date</label>
                            <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">
                                {{ $appointmentRequest->requested_date->format('l, F j, Y') }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Preferred Start Time</label>
                            <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">
                                {{ $appointmentRequest->requested_start_time ? \Carbon\Carbon::parse($appointmentRequest->requested_start_time)->format('g:i A') : '—' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Duration</label>
                            <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">
                                {{ $appointmentRequest->duration_minutes }} minutes
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Doctor Selection</label>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                {{ ucfirst(str_replace('_', ' ', $appointmentRequest->doctor_selection_mode)) }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Specialization</label>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $appointmentRequest->specialization->name ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Reason for Visit</label>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $appointmentRequest->reason_for_visit ?? '—' }}
                            </p>
                        </div>
                    </div>

                    @if($appointmentRequest->preferred_time_range_start || $appointmentRequest->preferred_time_range_end)
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Preferred Time Range</label>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $appointmentRequest->preferred_time_range_start ? \Carbon\Carbon::parse($appointmentRequest->preferred_time_range_start)->format('g:i A') : '' }}
                                {{ $appointmentRequest->preferred_time_range_start && $appointmentRequest->preferred_time_range_end ? ' – ' : '' }}
                                {{ $appointmentRequest->preferred_time_range_end ? \Carbon\Carbon::parse($appointmentRequest->preferred_time_range_end)->format('g:i A') : '' }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Patient & Doctor (if specific) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Patient Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                        <h3 class="text-lg font-semibold flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Patient
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xl font-bold">
                                {{ Str::upper(substr($appointmentRequest->patient->full_name, 0, 2)) }}
                            </div>
                            <div>
                                <a href="{{ route('patients.show', $appointmentRequest->patient) }}"
                                   class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition">
                                    {{ $appointmentRequest->patient->full_name }}
                                </a>
                                <p class="text-sm text-gray-500 dark:text-gray-400">MRN: {{ $appointmentRequest->patient->medical_record_number }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Specific Doctor Card (only if mode is specific) -->
                @if($appointmentRequest->doctor_selection_mode === 'specific' && $appointmentRequest->doctor)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white">
                        <h3 class="text-lg font-semibold flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h-4m-6 0H5"/>
                            </svg>
                            Requested Doctor
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xl font-bold">
                                Dr
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Dr. {{ $appointmentRequest->doctor->full_name }}
                                </p>
                                @if($appointmentRequest->doctor->primarySpecialization)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $appointmentRequest->doctor->primarySpecialization->name }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Additional Notes -->
            @if($appointmentRequest->notes)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Additional Notes
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $appointmentRequest->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('appointment_requests.create') }}"
                       class="w-full flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Request
                    </a>

                    <a href="{{ route('patients.show', $appointmentRequest->patient) }}"
                       class="w-full flex items-center justify-center px-4 py-3 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 text-sm font-medium rounded-lg border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        View Patient Profile
                    </a>

                    <a href="{{ route('appointment_requests.index') }}"
                       class="w-full flex items-center justify-center px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Requests
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
