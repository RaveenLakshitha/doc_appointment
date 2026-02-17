@extends('layouts.app')

@section('title', __('file.appointment_ticket'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-12 max-w-3xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('appointments.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                {{ __('file.appointments') }}
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">{{ __('file.appointment_ticket') }}</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.your_appointment_ticket') }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
        <div class="mb-10">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                {{ config('app.name') }} Clinic
            </h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm">
                {{ now()->format('d M Y • h:i A') }}
            </p>
        </div>

        <div class="py-10 border-t border-b border-gray-200 dark:border-gray-700">
            <p class="text-lg text-gray-600 dark:text-gray-300 mb-4">
                {{ $appointment->session_key ? ucfirst($appointment->session_key) . ' Session' : 'Session' }}
            </p>

            <div class="text-7xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                #{{ $appointment->queue_number ?? '—' }}
            </div>

            <p class="mt-4 text-xl font-medium text-gray-900 dark:text-white">
                {{ $appointment->doctor?->getFullNameAttribute() ?? 'Any Available Doctor' }}
            </p>
        </div>

        <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 gap-6 text-left max-w-lg mx-auto">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Patient</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    {{ $appointment->patient?->getFullNameAttribute() }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Date & Time</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    {{ $appointment->scheduled_start->format('l, d M Y') }}<br>
                    {{ $appointment->scheduled_start->format('h:i A') }} (approx.)
                </p>
            </div>
        </div>

        <div class="mt-12 text-sm text-gray-500 dark:text-gray-400">
            <p>Estimated waiting time: 8–15 minutes per patient</p>
            <p class="mt-2">Please arrive 10 minutes early • Ticket ID: {{ $appointment->id }}</p>
        </div>
    </div>

    <div class="mt-10 text-center">
        <button onclick="window.print()" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Ticket
        </button>
    </div>
</div>
@endsection