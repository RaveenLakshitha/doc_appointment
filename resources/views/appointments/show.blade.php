@extends('layouts.app')

@section('title', __('file.appointment_number') . $appointment->id . ' - ' . ($appointment->patient?->full_name ?? __('file.unknown')))

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-6 sm:py-12 pt-20">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center mb-3">
            <button onclick="window.history.back()" class="inline-flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('file.back') }}
            </button>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">
            {{ __('file.appointment_number') }}{{ $appointment->id }}
        </h1>
        <div class="flex flex-wrap items-center gap-2 mt-2">
            <span class="px-2.5 py-1 text-xs font-medium rounded-full
                @switch($appointment->status)
                    @case('pending')    bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 @break
                    @case('approved')   bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 @break
                    @case('confirmed')  bg-blue-100 text-blue-300 @break
                    @case('completed')  bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 @break
                    @case('cancelled')  bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 @break
                    @case('rejected')   bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @break
                    @case('paid')       bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300 @break
                    @default            bg-gray-100 text-gray-700
                @endswitch">
                {{ ucfirst(__("file.{$appointment->status}")) }}
            </span>
            @if($appointment->appointment_type)
                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-300">
                    {{ __('file.' . ($appointment->appointment_type ?? 'any')) }}
                </span>
            @endif
        </div>
    </div>

    <!-- Action Buttons Group -->
    <div class="flex flex-wrap gap-3">

        {{-- 1. Primary action – most important / most used --}}
        @if($appointment->status === 'completed' && auth()->user()->can('invoices.create'))
            <a href="{{ route('invoices.pos') }}?appointment_id={{ $appointment->id }}"
               class="inline-flex items-center justify-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 
                      text-white text-sm font-semibold rounded-lg transition-colors shadow-md min-w-[160px] order-1 sm:order-2">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                {{ __('file.proceed_to_pos') }}
            </a>
        @endif

        {{-- 2. Assign & Approve (only for pending) --}}
        @if($appointment->status === 'pending' && auth()->user()->hasAnyRole(['admin', 'primary_care_provider']))
            <button type="button" onclick="openAssignModal()"
                    class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 
                          text-white text-sm font-semibold rounded-lg transition-colors shadow-md min-w-[160px] order-2 sm:order-1">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                {{ __('file.assign_and_approve') }}
            </button>
        @endif

        {{-- 3. Edit & Delete – admin/doctor actions (less prominent) --}}
        @if(auth()->user()->hasAnyRole(['admin', 'primary_care_provider']))
            <a href="{{ route('appointments.edit', $appointment) }}"
               class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 hover:bg-gray-700 
                      dark:bg-gray-700 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg 
                      transition-colors shadow-sm min-w-[110px] order-3">
                <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                {{ __('file.edit') }}
            </a>

            <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" class="inline order-4">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        onclick="return confirm('{{ __('file.delete_confirm') }}')"
                        class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 
                              text-white text-sm font-medium rounded-lg transition-colors shadow-sm min-w-[110px]">
                    <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ __('file.delete') }}
                </button>
            </form>
        @endif

    </div>
</div>
    </div>

    {{-- Main content grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN — Patient + Schedule + Details --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Appointment Details Card --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-medium text-gray-900 dark:text-white">{{ __('file.appointment_details') }}</h2>
                </div>
                <div class="p-5 space-y-5">

                    {{-- Quick stats row --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="text-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.date') }}</div>
                            @if($appointment->scheduled_start)
                                <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $appointment->scheduled_start->format('d M') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $appointment->scheduled_start->format('Y') }}</div>
                            @else
                                <div class="text-sm text-gray-400 dark:text-gray-500 italic">{{ __('file.not_scheduled_yet') }}</div>
                            @endif
                        </div>
                        <div class="text-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg col-span-2 sm:col-span-3">
                            <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6">
                                <div>
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.time_slot') }}</div>
                                    @if($doctorSlot)
                                        <div class="text-base font-semibold text-gray-900 dark:text-white whitespace-nowrap">{{ $doctorSlot['start']->format('g:i A') }} – {{ $doctorSlot['end']->format('g:i A') }}</div>
                                    @elseif($appointment->scheduled_start && $appointment->scheduled_end)
                                        <div class="text-base font-semibold text-gray-900 dark:text-white whitespace-nowrap">{{ $appointment->scheduled_start->format('g:i A') }} – {{ $appointment->scheduled_end->format('g:i A') }}</div>
                                    @else
                                        <div class="text-sm text-gray-400 dark:text-gray-500">—</div>
                                    @endif
                                </div>
                                <div class="w-px h-8 bg-gray-200 dark:bg-gray-700 mx-2"></div>
                                <div>
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.appointment_time') }}</div>
                                    @if($appointment->scheduled_start)
                                        @php
                                            $apptStart = $appointment->scheduled_start;
                                            $apptEnd   = $appointment->duration_minutes
                                                ? $apptStart->copy()->addMinutes($appointment->duration_minutes)
                                                : $appointment->scheduled_end;
                                        @endphp
                                        <div class="text-base font-semibold text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                            {{ $apptStart->format('g:i A') }}{{ $apptEnd ? ' – ' . $apptEnd->format('g:i A') : '' }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400 dark:text-gray-500 italic">{{ __('file.not_specified') }}</div>
                                    @endif
                                </div>
                                <div class="w-px h-8 bg-gray-200 dark:bg-gray-700 mx-2"></div>
                                <div>
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.duration') ?? 'Duration' }}</div>
                                    <div class="text-base font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $appointment->duration_minutes ?? 15 }} {{ __('file.min') ?? 'min' }}
                                    </div>
                                </div>
                                <div class="w-px h-8 bg-gray-200 dark:bg-gray-700 mx-2"></div>
                                <div>
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.room') }}</div>
                                    @if($appointment->room)
                                        <div class="text-base font-semibold text-gray-900 dark:text-white whitespace-nowrap">{{ $appointment->room->room_number }}</div>
                                    @else
                                        <div class="text-sm text-gray-400 dark:text-gray-500 italic">{{ __('file.not_assigned') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reason --}}
                    @if($appointment->reason_for_visit)
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1.5">{{ __('file.reason_for_visit') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $appointment->reason_for_visit }}</p>
                    </div>
                    @endif

                    {{-- Specialization --}}
                    @if($appointment->specialization?->name)
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1.5">{{ __('file.specialization') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $appointment->specialization->name }}</p>
                    </div>
                    @endif

                    {{-- Age Group --}}
                    @if($appointment->ageGroup)
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1.5">{{ __('file.age_group') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $appointment->ageGroup->name }}</p>
                    </div>
                    @endif

                    {{-- Preferred Language --}}
                    @if($appointment->preferredLanguage)
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1.5">{{ __('file.preferred_language') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $appointment->preferredLanguage->name }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Treatments Card --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-base font-medium text-gray-900 dark:text-white">{{ __('file.treatments') }}</h2>
                    @if(auth()->user()->doctor && auth()->user()->doctor->id === $appointment->doctor_id)
                        <button type="button" 
                                onclick="openTreatmentModal()"
                                class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            {{ __('file.add_treatment') }}
                        </button>
                    @endif
                </div>

                @if($appointment->treatments->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 text-center px-5">
                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">{{ __('file.no_treatments_added_yet') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900">
                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.treatment') }}</th>
                                    <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.unit_price') }}</th>
                                    <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.qty') }}</th>
                                    <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.line_total') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($appointment->treatments as $treatment)
                                    <tr>
                                        <td class="px-5 py-3 text-sm text-gray-900 dark:text-white">
                                            {{ $treatment->name }}
                                            @if($treatment->code)
                                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $treatment->code }})</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-sm text-right text-gray-700 dark:text-gray-300">
                                            {{ $currency_code ?? 'LKR' }} {{ number_format($treatment->pivot->price_at_time ?? 0, 2) }}
                                        </td>
                                        <td class="px-5 py-3 text-sm text-right text-gray-700 dark:text-gray-300">
                                            {{ $treatment->pivot->quantity ?? 1 }}
                                        </td>
                                        <td class="px-5 py-3 text-sm text-right font-medium text-gray-900 dark:text-white">
                                            {{ $currency_code ?? 'LKR' }} {{ number_format(($treatment->pivot->quantity ?? 1) * ($treatment->pivot->price_at_time ?? 0), 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <td colspan="3" class="px-5 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        {{ __('file.total_treatments_cost') }}
                                    </td>
                                    <td class="px-5 py-3 text-right font-semibold text-indigo-600 dark:text-indigo-400">
                                        {{ $currency_code ?? 'LKR' }} {{ number_format($appointment->total_treatment_price ?? 0, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Notes Card (only if notes exist) --}}
            @if($appointment->patient_notes || $appointment->doctor_notes || $appointment->admin_notes)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-medium text-gray-900 dark:text-white">{{ __('file.notes') }}</h2>
                </div>
                <div class="p-5 space-y-4">
                    @if($appointment->patient_notes)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.patient_notes') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $appointment->patient_notes }}</p>
                    </div>
                    @endif
                    @if($appointment->doctor_notes)
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.doctor_notes') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $appointment->doctor_notes }}</p>
                    </div>
                    @endif
                    @if($appointment->admin_notes)
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.admin_notes') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $appointment->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="space-y-6">

            {{-- Patient Card --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-medium text-gray-900 dark:text-white">{{ __('file.patient') }}</h2>
                </div>
                <div class="p-5 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.full_name') }}</label>
                        <a href="{{ route('patients.show', $appointment->patient) }}"
                           class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                            {{ $appointment->patient?->full_name ?? '—' }}
                        </a>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.mrn') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $appointment->patient?->medical_record_number ?? '—' }}</p>
                    </div>
                    @if($appointment->patient?->date_of_birth)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.date_of_birth') }} / {{ __('file.age') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white flex items-center gap-2">
                            {{ $appointment->patient->date_of_birth->format('d M Y') }}
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                {{ $appointment->patient->date_of_birth->age }} {{ strtolower(__('file.years') ?? 'years') }}
                            </span>
                        </p>
                    </div>
                    @endif
                    @if($appointment->patient?->phone)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.phone') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $appointment->patient->phone }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Doctor Card --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-medium text-gray-900 dark:text-white">{{ __('file.doctor') }}</h2>
                </div>
                <div class="p-5 space-y-3">
                    @if($appointment->doctor)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.name') }}</label>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            Dr. {{ $appointment->doctor->getFullNameAttribute() }}
                        </p>
                    </div>
                    @if($appointment->doctor->primarySpecialization?->name)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.specialization') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $appointment->doctor->primarySpecialization->name }}</p>
                    </div>
                    @endif
                    @if($appointment->doctor->email)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.email') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $appointment->doctor->email }}</p>
                    </div>
                    @endif
                    @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">{{ __('file.not_assigned') }}</p>
                    @endif
                </div>
            </div>

            {{-- Prescription Card --}}
@php
    $prescription = $appointment->prescriptions()->latest()->first();
    $hasPrescription = !is_null($prescription);
    $canManagePrescription = auth()->user()->doctor && auth()->user()->doctor->id === $appointment->doctor_id;
@endphp

@if($appointment->status === 'approved' || $appointment->status === 'completed' || $appointment->doctor_id)
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-base font-medium text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('file.prescription') }}
            </h2>
            
            @if($hasPrescription && $prescription->prescription_date)
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $prescription->prescription_date->format('d M Y') }}
                </span>
            @endif
        </div>

        <div class="p-5">
            @if($hasPrescription)
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('file.prescription') }} #{{ $prescription->id }}
                            </p>
                            @if($prescription->type)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $prescription->type }}
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('prescriptions.show', $prescription) }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                {{ __('file.view_prescription') }}
                            </a>

                            @if($canManagePrescription)
                                <a href="{{ route('prescriptions.edit', $prescription) }}"
                                   class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    {{ __('file.edit') }}
                                </a>
                            @endif
                        </div>
                    </div>

                    @if($prescription->diagnosis)
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">
                                {{ __('file.diagnosis') }}
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                {{ Str::limit($prescription->diagnosis, 120) }}
                            </p>
                        </div>
                    @endif
                </div>

            @elseif($canManagePrescription)
                <div class="text-center py-6">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5V20a1 1 0 01-1 1H4a1 1 0 01-1-1v-3.414l5-5A2 2 0 008 10.172V5L7 4z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('file.no_medications_prescribed_yet') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('file.create_prescription_for_this_appointment') }}
                    </p>

                    <div class="mt-6">
                        <a href="{{ route('prescriptions.create') }}?appointment_id={{ $appointment->id }}"
                           class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ __('file.create_prescription') }}
                        </a>
                    </div>
                </div>

            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400 italic">
                    {{ __('file.no_medications_prescribed') }}
                </div>
            @endif
        </div>
    </div>
@endif

            {{-- Created/Updated Timestamps --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-medium text-gray-900 dark:text-white">{{ __('file.properties') }}</h2>
                </div>
                <div class="p-5 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.created_at') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $appointment->created_at->format('d M Y, g:i A') }}</p>
                    </div>
                    @if($appointment->updated_at && $appointment->updated_at->ne($appointment->created_at))
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.updated_at') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $appointment->updated_at->diffForHumans() }}</p>
                    </div>
                    @endif
                    @if($appointment->approved_by)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.approved_by') }}</label>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ optional($appointment->approvedBy)->name ?? __('file.unknown') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Treatment application modal --}}
@if(auth()->user()->doctor && auth()->user()->doctor->id === $appointment->doctor_id)
<div id="treatment_modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Backdrop --}}
        <div id="modal_overlay" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeTreatmentModal()"></div>

        {{-- Centering trick --}}
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal panel --}}
        <div id="modal_content" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full opacity-0 scale-95 duration-300">
            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-gray-700">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left">
                        <h3 class="text-xl font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">
                            {{ __('file.apply_treatments') }}
                        </h3>
                    </div>
                </div>
            </div>
            
            <form id="treatment-form" method="POST" action="{{ route('appointments.treatments.update', $appointment) }}">
                @csrf
                @method('PATCH')
                
                <div class="px-4 py-5 sm:p-6 max-h-[60vh] overflow-y-auto">
                    <div class="space-y-3">
                        @if($treatments->isEmpty())
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic text-center py-4">{{ __('file.no_treatments_for_doctor') }}</p>
                        @else
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($treatments as $treatment)
                                    <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="treatment_ids[]" value="{{ $treatment->id }}" 
                                                {{ $appointment->treatments->contains($treatment->id) ? 'checked' : '' }}
                                                class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600">
                                        </div>
                                        <div class="ml-3 flex-1 flex flex-col">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $treatment->name }}</span>
                                            @if($treatment->code)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $treatment->code }}</span>
                                            @endif
                                        </div>
                                        <div class="text-indigo-600 dark:text-indigo-400 font-bold text-sm">
                                            {{ $currency_code ?? 'LKR' }} {{ number_format($treatment->pivot->price ?? $treatment->price ?? 0, 2) }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        {{ __('file.save_changes') }}
                    </button>
                    <button type="button" onclick="closeTreatmentModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-5 py-2.5 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        {{ __('file.cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Assign and Approve Modal --}}
<div id="assign_modal" class="fixed inset-0 z-[60] overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-80" aria-hidden="true" onclick="closeAssignModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div id="assign_modal_content" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full opacity-0 scale-95 duration-300">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.assign_and_approve') }}</h3>
                <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form id="assign_form" action="{{ route('appointments.assign-and-approve', $appointment) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="px-6 py-6 space-y-5">
                    <div id="modal_age_group_container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.age_group') }}</label>
                        <div id="modal_age_group_text" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 mb-2"></div>
                        <input type="hidden" name="age_group_id" id="modal_age_group_id" value="{{ $appointment->age_group_id }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.specialization') }}</label>
                        <div id="modal_specialization_text" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 mb-2 hidden"></div>
                        <select name="specialization_id" id="modal_specialization_id" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">{{ __('file.select_specialization') }}</option>
                            @foreach(\App\Models\Specialization::all() as $spec)
                                <option value="{{ $spec->id }}" {{ $appointment->specialization_id == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.doctor') }}</label>
                        <div id="modal_doctor_text" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 mb-2 hidden"></div>
                        <select name="doctor_id" id="modal_doctor_id" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">{{ __('file.select_doctor') }}</option>
                            @if($appointment->appointment_type === \App\Models\Appointment::TYPE_SPECIFIC && $appointment->doctor)
                                <option value="{{ $appointment->doctor->id }}" selected>Dr. {{ $appointment->doctor->getFullNameAttribute() }}</option>
                            @endif
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.date') }}</label>
                        @if($appointment->scheduled_start)
                            <div class="w-full px-3 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-medium">
                                {{ \Carbon\Carbon::parse($appointment->scheduled_start)->format('l, d M Y') }}
                            </div>
                            <input type="hidden" name="date" value="{{ \Carbon\Carbon::parse($appointment->scheduled_start)->format('Y-m-d') }}">
                        @else
                            <div id="modal_available_days_container" class="space-y-3 hidden mb-3">
                                <div id="modal_available_days" class="flex flex-wrap gap-2">
                                    <!-- Quick select days will go here -->
                                </div>
                                <button type="button" id="toggle_modal_date" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ __('file.pick_another_date') ?? 'Pick another date' }}
                                </button>
                            </div>
                            <div id="modal_date_input_group">
                                <input type="date" name="date" id="modal_date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>

                            <div id="modal_date_warning" class="mt-2 hidden">
                                <div class="flex items-center p-2 text-xs text-amber-800 border border-amber-200 rounded-lg bg-amber-50 dark:bg-gray-800 dark:text-amber-400 dark:border-amber-900">
                                    <svg class="flex-shrink-0 inline w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                    </svg>
                                    <span class="sr-only">Warning</span>
                                    <div id="modal_date_warning_text">
                                        <!-- Warning message will go here -->
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div id="modal_slot_group">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.time_slot') }} <span class="text-red-500">*</span></label>
                        <select name="slot" id="modal_slot"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">{{ __('file.select_slot') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.appointment_time') }} <span class="text-red-500">*</span></label>
                        <input type="time" name="appointment_time" id="modal_appointment_time" required
                               value="{{ $appointment->scheduled_start ? $appointment->scheduled_start->format('H:i') : '' }}"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>

                    @if($appointment->appointment_type === \App\Models\Appointment::TYPE_SPECIFIC)
                        <div id="modal_specific_slot_info">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.time_slot') }}</label>
                            @if($appointment->scheduled_start && $appointment->scheduled_end)
                                <div class="w-full px-3 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-medium">
                                    {{ $appointment->scheduled_start->format('h:i A') }} - {{ $appointment->scheduled_end->format('h:i A') }}
                                </div>
                                <input type="hidden" name="slot" value="{{ $appointment->scheduled_start->format('H:i') }}|{{ $appointment->scheduled_end->format('H:i') }}">
                            @endif
                        </div>
                    @endif

                    {{-- Unavailable Times --}}
                    <div id="modal_unavailable_container" class="hidden mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <label class="block text-sm font-medium text-red-600 dark:text-red-400 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('file.unavailable_times') ?? 'Unavailable Times' }}
                        </label>
                        <div id="modal_unavailable_list" class="space-y-2">
                            {{-- Booked slots will be injected here --}}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.duration_minutes') ?? 'Duration' }} <span class="text-red-500">*</span></label>
                        <select name="duration_minutes" id="modal_duration_minutes" required class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="15" {{ ($appointment->duration_minutes ?? 15) == 15 ? 'selected' : '' }}>15 {{ __('file.min') ?? 'min' }}</option>
                            <option value="30" {{ ($appointment->duration_minutes ?? 15) == 30 ? 'selected' : '' }}>30 {{ __('file.min') ?? 'min' }}</option>
                            <option value="45" {{ ($appointment->duration_minutes ?? 15) == 45 ? 'selected' : '' }}>45 {{ __('file.min') ?? 'min' }}</option>
                            <option value="60" {{ ($appointment->duration_minutes ?? 15) == 60 ? 'selected' : '' }}>1 {{ __('file.hour') ?? 'hr' }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.room') }}</label>
                        <select required name="room_id" id="modal_room_id" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="" disabled selected>{{ __('file.select_room') ?? 'Select Room' }}</option>
                            @foreach(\App\Models\Room::active()->get() as $room)
                                <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->room_number }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex flex-row-reverse gap-3 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit" class="inline-flex justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm min-w-[120px]">
                        {{ __('file.assign_and_approve') }}
                    </button>
                    <button type="button" onclick="closeAssignModal()" class="inline-flex justify-center px-5 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium transition-colors">
                        {{ __('file.cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script>
    function openTreatmentModal() {
        const modal = document.getElementById('treatment_modal');
        const content = document.getElementById('modal_content');
        
        modal.classList.remove('hidden');
        // Prevent body scroll
        document.body.classList.add('overflow-hidden');
        
        // Trigger animation
        setTimeout(() => {
            content.classList.remove('opacity-0', 'scale-95');
            content.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeTreatmentModal() {
        const modal = document.getElementById('treatment_modal');
        const content = document.getElementById('modal_content');
        
        content.classList.remove('opacity-100', 'scale-100');
        content.classList.add('opacity-0', 'scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 300);
    }

    const modalSpecSelect = document.getElementById('modal_specialization_id');
    const modalDoctorSelect = document.getElementById('modal_doctor_id');
    const modalDateSelect = document.getElementById('modal_date');
    const modalSlotSelect = document.getElementById('modal_slot');
    const modalAgeGroupSelect = document.getElementById('modal_age_group_id');
    const modalAgeGroupContainer = document.getElementById('modal_age_group_container');
    const modalSpecText = document.getElementById('modal_specialization_text');
    const modalDoctorText = document.getElementById('modal_doctor_text');
    const modalAvailableDays = document.getElementById('modal_available_days');
    const modalSlotGroup = document.getElementById('modal_slot_group');
    const modalSpecificSlotInfo = document.getElementById('modal_specific_slot_info');

    function openAssignModal() {
        const modal = document.getElementById('assign_modal');
        const content = document.getElementById('assign_modal_content');
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        setTimeout(() => {
            content.classList.remove('opacity-0', 'scale-95');
            content.classList.add('opacity-100', 'scale-100');
        }, 10);

        const appointmentType = '{{ $appointment->appointment_type }}';
        
        // Reset visual states
        modalSpecSelect.classList.remove('bg-gray-100', 'pointer-events-none');
        modalDoctorSelect.classList.remove('bg-gray-100', 'pointer-events-none');
        modalSpecSelect.removeAttribute('tabindex');
        modalDoctorSelect.removeAttribute('tabindex');
        modalAgeGroupContainer.classList.add('hidden');
        modalSpecText.classList.add('hidden');
        modalDoctorText.classList.add('hidden');
        modalSpecSelect.classList.remove('hidden');
        modalDoctorSelect.classList.remove('hidden');

        if (appointmentType === '{{ \App\Models\Appointment::TYPE_SPECIFIC }}') {
            modalSpecSelect.classList.add('hidden');
            modalDoctorSelect.classList.add('hidden');
            modalSpecText.classList.remove('hidden');
            modalDoctorText.classList.remove('hidden');
            
            const specId = '{{ $appointment->doctor?->primary_specialization_id ?? $appointment->specialization_id ?? "" }}';
            modalSpecSelect.value = specId;
            modalSpecText.textContent = '{{ $appointment->doctor?->primarySpecialization?->name ?? $appointment->specialization?->name ?? "" }}' || (modalSpecSelect.options[modalSpecSelect.selectedIndex]?.text || "");
            modalDoctorText.textContent = '{{ $appointment->doctor ? "Dr. " . $appointment->doctor->getFullNameAttribute() : "" }}';

            if (modalSlotGroup) modalSlotGroup.classList.add('hidden');
            if (modalSpecificSlotInfo) modalSpecificSlotInfo.classList.remove('hidden');
        } else if (appointmentType === '{{ \App\Models\Appointment::TYPE_ANY }}') {
            const specId = '{{ $appointment->specialization_id }}';
            const specName = '{{ $appointment->specialization?->name ?? "" }}';
            
            modalSpecSelect.value = specId;
            modalSpecSelect.classList.add('hidden');
            modalSpecText.textContent = specName;
            modalSpecText.classList.remove('hidden');
            
            modalDoctorSelect.classList.remove('hidden');
            modalDoctorText.classList.add('hidden');
            
            if (modalSlotGroup) modalSlotGroup.classList.remove('hidden');
            if (modalSpecificSlotInfo) modalSpecificSlotInfo.classList.add('hidden');

            modalAgeGroupContainer.classList.remove('hidden');
            const ageGroupDisplay = document.getElementById('modal_age_group_text');
            if (ageGroupDisplay) ageGroupDisplay.textContent = '{{ $appointment->ageGroup?->name ?? "" }}';
        }

        loadModalDoctors();
    }

    function closeAssignModal() {
        const modal = document.getElementById('assign_modal');
        const content = document.getElementById('assign_modal_content');
        content.classList.remove('opacity-100', 'scale-100');
        content.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 300);
    }

    function loadModalDoctors() {
        if ('{{ $appointment->appointment_type }}' === '{{ \App\Models\Appointment::TYPE_SPECIFIC }}') {
            // Already has doctor selected, skip reloading doctors. Load slots directly.
            if (modalDoctorSelect.value) {
                loadModalDays();
                loadModalSlots();
            }
            return;
        }

        const specId = modalSpecSelect.value;
        const ageGroupId = modalAgeGroupSelect ? (modalAgeGroupSelect.value || '{{ $appointment->age_group_id }}') : '{{ $appointment->age_group_id }}';
        // modalDateSelect is null when scheduled_start is already set (date rendered as static text)
        // Fall back to the hidden date input in that case
        const dateInput = modalDateSelect || document.querySelector('#assign_form input[name="date"]');
        const date = dateInput ? dateInput.value : '';

        if (!specId) {
            modalDoctorSelect.innerHTML = '<option value="">{{ __("file.select_doctor") }}</option>';
            return;
        }

        let url = `{{ route('appointments.doctors.filtered') }}?specialization_id=${specId}`;
        if (ageGroupId) {
            url += `&age_group_id=${ageGroupId}`;
        }
        if (date) {
            url += `&date=${date}`;
        }

        fetch(url)
            .then(r => r.json())
            .then(data => {
                const currentVal = modalDoctorSelect.value;
                modalDoctorSelect.innerHTML = '<option value="">{{ __("file.select_doctor") }}</option>';
                data.forEach(doc => {
                    const opt = new Option(doc.text, doc.value);
                    if (doc.value == '{{ $appointment->doctor_id }}') {
                        opt.selected = true;
                    } else if (currentVal == doc.value) {
                        opt.selected = true;
                    }
                    modalDoctorSelect.add(opt);
                });

                // Update doctor text label if in specific mode
                if ('{{ $appointment->appointment_type }}' === '{{ \App\Models\Appointment::TYPE_SPECIFIC }}') {
                    modalDoctorText.textContent = modalDoctorSelect.options[modalDoctorSelect.selectedIndex]?.text || '';
                }

                if (modalDoctorSelect.value) {
                    loadModalDays();
                    loadModalSlots();
                }
            });
    }

    function loadModalDays() {
        const doctorId = modalDoctorSelect.value;
        const container = document.getElementById('modal_available_days');
        const containerWrapper = document.getElementById('modal_available_days_container');
        const inputGroup = document.getElementById('modal_date_input_group');
        const toggleBtn = document.getElementById('toggle_modal_date');

        if (!doctorId || !container) {
            if (containerWrapper) containerWrapper.classList.add('hidden');
            if (inputGroup) inputGroup.classList.remove('hidden');
            return;
        }

        fetch(`{{ url('doctors') }}/${doctorId}/available-days`)
            .then(r => r.json())
            .then(data => {
                const days = data.days || [];
                container.innerHTML = '';
                if (days.length === 0) {
                    containerWrapper.classList.add('hidden');
                    inputGroup.classList.remove('hidden');
                    return;
                }

                containerWrapper.classList.remove('hidden');
                inputGroup.classList.add('hidden');
                
                days.forEach(day => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'px-3 py-1.5 text-xs font-medium rounded-md border border-indigo-200 dark:border-indigo-800 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors';
                    btn.textContent = day.label;
                    btn.onclick = () => {
                        selectDate(day.date);
                        // Highlight logic
                        container.querySelectorAll('button').forEach(b => {
                            b.classList.remove('bg-indigo-50', 'dark:bg-indigo-900/30', 'text-indigo-700', 'dark:text-indigo-300', 'border-indigo-600');
                            b.classList.add('bg-white', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300', 'border-indigo-200');
                        });
                        btn.classList.add('bg-indigo-50', 'dark:bg-indigo-900/30', 'text-indigo-700', 'dark:text-indigo-300', 'border-indigo-600');
                        btn.classList.remove('bg-white', 'dark:bg-gray-800', 'border-indigo-200');
                    };
                    container.appendChild(btn);
                });
            })
            .catch(() => {
                if (containerWrapper) containerWrapper.classList.add('hidden');
                if (inputGroup) inputGroup.classList.remove('hidden');
            });

        if (toggleBtn) {
            toggleBtn.onclick = () => {
                inputGroup.classList.toggle('hidden');
                if (!inputGroup.classList.contains('hidden')) {
                    toggleBtn.textContent = '{{ __("file.use_available_days") ?? "Use available days" }}';
                } else {
                    toggleBtn.textContent = '{{ __("file.pick_another_date") ?? "Pick another date" }}';
                }
            };
        }
    }

    function selectDate(dateStr) {
        const dateField = modalDateSelect || document.querySelector('#assign_form input[name="date"]');
        if (dateField) dateField.value = dateStr;
        loadModalSlots();
    }

    function loadModalSlots() {
        const doctorId = modalDoctorSelect.value;
        const dateInput2 = modalDateSelect || document.querySelector('#assign_form input[name="date"]');
        const date = dateInput2 ? dateInput2.value : '';
        if (!doctorId || !date) {
            modalSlotSelect.innerHTML = '<option value="">{{ __("file.select_slot") }}</option>';
            return;
        }
        fetch(`{{ url('doctors') }}/${doctorId}/available-slots?date=${date}`)
            .then(r => r.json())
            .then(data => {
                const warningDiv = document.getElementById('modal_date_warning');
                const warningText = document.getElementById('modal_date_warning_text');
                
                if (warningDiv && warningText) {
                    if (data.message && (!data.slots || data.slots.length === 0)) {
                        warningText.textContent = data.message;
                        warningDiv.classList.remove('hidden');
                    } else {
                        warningDiv.classList.add('hidden');
                    }
                }

                modalSlotSelect.innerHTML = '<option value="">{{ __("file.select_slot") }}</option>';
                const slots = data.slots || [];
                const appointments = data.appointments || [];
                
                if (slots.length === 0 && data.message) {
                    const opt = new Option(data.message, "");
                    opt.disabled = true;
                    modalSlotSelect.add(opt);
                }

                slots.forEach(slot => {
                    const label = slot.label || `${slot.start} – ${slot.end}`;
                    const val = `${slot.start}|${slot.end}`;
                    modalSlotSelect.add(new Option(label, val));
                });

                if (slots.length === 1) {
                    modalSlotSelect.value = `${slots[0].start}|${slots[0].end}`;
                    modalSlotSelect.dispatchEvent(new Event('change'));
                }

                renderUnavailableTimes(appointments);
            });
    }

    function renderUnavailableTimes(appointments) {
        const container = document.getElementById('modal_unavailable_container');
        const list = document.getElementById('modal_unavailable_list');
        
        if (!container || !list) return;
        
        if (!appointments || appointments.length === 0) {
            container.classList.add('hidden');
            return;
        }

        container.classList.remove('hidden');
        list.innerHTML = appointments.map(a => {
            // Convert 24h to 12h for display
            const formatTime = (t) => {
                const [h, m] = t.split(':');
                const hour = parseInt(h);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const h12 = hour % 12 || 12;
                return `${h12}:${m} ${ampm}`;
            };

            return `
                <div class="flex items-center justify-between px-3 py-2 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-lg text-xs">
                    <span class="font-medium text-red-700 dark:text-red-300">
                        ${formatTime(a.start)} - ${formatTime(a.end)}
                    </span>
                    <span class="text-[10px] uppercase tracking-wider text-red-500 dark:text-red-400 font-bold">
                        ${a.status}
                    </span>
                </div>
            `;
        }).join('');
    }

    const modalRoomSelect = document.getElementById('modal_room_id');
    const modalTimeInput = document.getElementById('modal_appointment_time');
    const modalDurationSelect = document.getElementById('modal_duration_minutes');

    function checkModalRoomAvailability() {
        const dateInputRoom = modalDateSelect || document.querySelector('#assign_form input[name="date"]');
        if (!dateInputRoom || !modalTimeInput || !modalDurationSelect || !modalRoomSelect) return;
        
        const date = dateInputRoom.value;
        const time = modalTimeInput.value;
        const duration = modalDurationSelect.value;
        const excludeId = '{{ $appointment->id }}';

        if (!date || !time) return;

        fetch(`{{ route('appointments.check_room_availability') }}?date=${date}&time=${time}&duration=${duration}&exclude_id=${excludeId}`)
            .then(r => r.json())
            .then(data => {
                const busyRooms = data.busy_rooms || [];

                // Restore previously hidden options
                if (!modalRoomSelect._hiddenBank) modalRoomSelect._hiddenBank = [];
                modalRoomSelect._hiddenBank.forEach(opt => modalRoomSelect.add(opt));
                modalRoomSelect._hiddenBank = [];

                // Remove busy options
                Array.from(modalRoomSelect.options).forEach(opt => {
                    if (!opt.value) return;
                    const roomId = parseInt(opt.value);
                    if (busyRooms.includes(roomId)) {
                        if (opt.selected) modalRoomSelect.value = '';
                        modalRoomSelect._hiddenBank.push(opt);
                        modalRoomSelect.remove(opt.index);
                    }
                });
            });
    }

    modalSlotSelect.addEventListener('change', () => {
        if (modalSlotSelect.value) {
            const startTime = modalSlotSelect.value.split('|')[0];
            const timeInput = document.getElementById('modal_appointment_time');
            if (timeInput && (!timeInput.value || timeInput.value === "00:00")) {
                timeInput.value = startTime;
            }
            checkModalRoomAvailability();
            filterModalDurationBySlot();
        }
    });

    modalSpecSelect.addEventListener('change', loadModalDoctors);
    modalDoctorSelect.addEventListener('change', () => {
        loadModalDays();
        loadModalSlots();
    });
    if (modalDateSelect) {
        modalDateSelect.addEventListener('change', () => {
            loadModalDoctors();
            checkModalRoomAvailability();
        });
    }
    if (modalTimeInput) modalTimeInput.addEventListener('change', function() {
        checkModalRoomAvailability();
        filterModalDurationBySlot();
    });
    if (modalDurationSelect) modalDurationSelect.addEventListener('change', checkModalRoomAvailability);

    // Remove modal duration options that would push past the slot end
    function filterModalDurationBySlot() {
        const dSel = document.getElementById('modal_duration_minutes');
        const timeSel = document.getElementById('modal_appointment_time');
        // Get slot end from the hidden input or the slot select (for any-doctor modal)
        const slotHidden = document.querySelector('#assign_form input[name="slot"]');
        const slotVal = slotHidden ? slotHidden.value : (modalSlotSelect ? modalSlotSelect.value : '');

        if (!dSel || !timeSel || !slotVal || !timeSel.value) {
            if (dSel && dSel._hiddenBank) {
                dSel._hiddenBank.forEach(opt => dSel.add(opt));
                dSel._hiddenBank = [];
            }
            return;
        }
        const [, slotEnd] = slotVal.split('|');
        const [apptH, apptM] = timeSel.value.split(':').map(Number);
        const [endH, endM] = slotEnd.split(':').map(Number);
        const maxMinutes = (endH * 60 + endM) - (apptH * 60 + apptM);

        if (!dSel._hiddenBank) dSel._hiddenBank = [];
        dSel._hiddenBank.forEach(opt => dSel.add(opt));
        dSel._hiddenBank = [];

        Array.from(dSel.options).forEach(opt => {
            if (!opt.value) return;
            if (parseInt(opt.value) > maxMinutes) {
                if (opt.selected) dSel.value = '';
                dSel._hiddenBank.push(opt);
                dSel.remove(opt.index);
            }
        });
    }
</script>
@endsection
