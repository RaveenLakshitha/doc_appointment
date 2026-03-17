@extends('layouts.app')
@section('title', __('file.edit_appointment') . ' #' . $appointment->id)

@section('content')
@php
    $isPrivileged = auth()->user()->hasAnyRole(['admin', 'doctor', 'primary_care_provider']);
@endphp
<div class="px-4 sm:px-6 lg:px-4 pb-6 sm:py-12 pt-20">

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('appointments.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                {{ __('file.appointments') }}
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('appointments.show', $appointment) }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                {{ __('file.appointment_number') }}{{ $appointment->id }}
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">{{ __('file.edit') }}</span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.edit_appointment') }} #{{ $appointment->id }}
                </h1>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full
                        @switch($appointment->status)
                            @case('pending')    bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 @break
                            @case('approved')   bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 @break
                            @case('confirmed')  bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 @break
                            @case('completed')  bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 @break
                            @case('cancelled')  bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 @break
                            @case('rejected')   bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @break
                            @default            bg-gray-100 text-gray-700
                        @endswitch">
                        {{ ucfirst(__("file.{$appointment->status}")) }}
                    </span>
                    @if($appointment->appointment_type)
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-300">
                            {{ ucwords(str_replace('_', ' ', $appointment->appointment_type)) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>


    @php
        $isPrivileged = auth()->user()->hasAnyRole(['admin', 'doctor', 'primary_care_provider']);
    @endphp

    {{-- Prescription quick links (approved/completed) --}}
    @if($appointment->status === 'approved' || $appointment->status === 'completed')
    @php
        $hasPrescription = $appointment->prescriptions()->exists();
        $prescription    = $hasPrescription ? $appointment->prescriptions()->latest()->first() : null;
        $canManage = auth()->user()->doctor && auth()->user()->doctor->id === $appointment->doctor_id;
    @endphp
    @if($canManage)
    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/50 rounded-xl p-5">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-green-900 dark:text-green-300">{{ __('file.prescription') }}</h3>
                <p class="text-xs text-green-700 dark:text-green-400 mt-0.5">
                    {{ $hasPrescription ? __('file.prescription') . ' #' . $prescription->id : __('file.no_medications_prescribed') }}
                </p>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                @if($hasPrescription)
                    <a href="{{ route('prescriptions.show', $prescription) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        {{ __('file.view_prescription') }}
                    </a>
                    <a href="{{ route('prescriptions.edit', $prescription) }}"
                       class="inline-flex items-center px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
                        {{ __('file.edit_prescription') }}
                    </a>
                @else
                    <a href="{{ route('prescriptions.create') }}?appointment_id={{ $appointment->id }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('file.create_prescription') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endif
    @endif

    {{-- ========== MAIN FORM ========== --}}
    <form id="appointmentEditForm" method="POST" action="{{ route('appointments.update', $appointment) }}">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            {{-- Row 1 — Patient / Doctor / Schedule --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Patient (read-only) --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('file.patient') }}</h2>
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
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.date_of_birth') }}</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $appointment->patient->date_of_birth->format('d M Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Doctor & Specialization --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('file.doctor') }}</h2>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.appointment_type') }}
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="appointment-type-cards">
                                @php
                                    $currentType = old('appointment_type', $appointment->appointment_type);
                                @endphp
                                <label class="relative flex flex-col p-3 border rounded-xl cursor-pointer transition-all duration-200 group
                                            {{ $currentType == \App\Models\Appointment::TYPE_SPECIFIC ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-900/10 dark:border-indigo-500' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                                    <input type="radio" name="appointment_type" value="{{ \App\Models\Appointment::TYPE_SPECIFIC }}"
                                           class="absolute opacity-0 appointment-type-radio" {{ $currentType == \App\Models\Appointment::TYPE_SPECIFIC ? 'checked' : '' }}
                                           {{ (!$isPrivileged || $appointment->status !== 'pending') ? 'disabled' : '' }}>
                                    <div class="flex items-center justify-between">
                                        <span class="block text-xs font-semibold {{ $currentType == \App\Models\Appointment::TYPE_SPECIFIC ? 'text-indigo-900 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ __('file.specific_doctor') }}
                                        </span>
                                        <div class="w-4 h-4 rounded-full border flex items-center justify-center {{ $currentType == \App\Models\Appointment::TYPE_SPECIFIC ? 'border-indigo-600 dark:border-indigo-400' : 'border-gray-300 dark:border-gray-600' }}">
                                            @if($currentType == \App\Models\Appointment::TYPE_SPECIFIC)
                                                <div class="w-2 h-2 rounded-full bg-indigo-600 dark:bg-indigo-400"></div>
                                            @endif
                                        </div>
                                    </div>
                                </label>

                                <label class="relative flex flex-col p-3 border rounded-xl cursor-pointer transition-all duration-200 group
                                            {{ $currentType == \App\Models\Appointment::TYPE_ANY ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-900/10 dark:border-indigo-500' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                                    <input type="radio" name="appointment_type" value="{{ \App\Models\Appointment::TYPE_ANY }}"
                                           class="absolute opacity-0 appointment-type-radio" {{ $currentType == \App\Models\Appointment::TYPE_ANY ? 'checked' : '' }}
                                           {{ (!$isPrivileged || $appointment->status !== 'pending') ? 'disabled' : '' }}>
                                    <div class="flex items-center justify-between">
                                        <span class="block text-xs font-semibold {{ $currentType == \App\Models\Appointment::TYPE_ANY ? 'text-indigo-900 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ __('file.any_doctor') }}
                                        </span>
                                        <div class="w-4 h-4 rounded-full border flex items-center justify-center {{ $currentType == \App\Models\Appointment::TYPE_ANY ? 'border-indigo-600 dark:border-indigo-400' : 'border-gray-300 dark:border-gray-600' }}">
                                            @if($currentType == \App\Models\Appointment::TYPE_ANY)
                                                <div class="w-2 h-2 rounded-full bg-indigo-600 dark:bg-indigo-400"></div>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div id="specialization-group">
                            <label for="specialization_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.specialization') }} <span id="spec-required" class="text-red-500">*</span>
                            </label>
                            <select name="specialization_id" id="specialization_id" {{ !$isPrivileged ? 'disabled' : '' }}
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all {{ !$isPrivileged ? 'bg-gray-50 dark:bg-gray-800' : '' }}">
                                <option value="">{{ __('file.select_specialization') }}</option>
                                @foreach($specializations as $spec)
                                    <option value="{{ $spec->id }}"
                                            {{ old('specialization_id', $appointment->specialization_id ?? $appointment->doctor?->primarySpecialization?->id) == $spec->id ? 'selected' : '' }}>
                                        {{ $spec->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('specialization_id') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div id="doctor-group">
                            <label for="doctor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.doctor') }} <span id="doctor-required" class="text-red-500">*</span>
                            </label>
                            <select name="doctor_id" id="doctor_id" {{ !$isPrivileged ? 'disabled' : '' }}
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all {{ !$isPrivileged ? 'bg-gray-50 dark:bg-gray-800' : '' }}">
                                <option value="">{{ __('file.select_specialization_first') }}</option>
                            </select>
                            @error('doctor_id') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="age_group_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('file.age_group') }}
                                </label>
                                <select name="age_group_id" id="age_group_id" {{ !$isPrivileged ? 'disabled' : '' }}
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all {{ !$isPrivileged ? 'bg-gray-50 dark:bg-gray-800' : '' }}">
                                    <option value="">{{ __('file.select_age_group') }}</option>
                                    @foreach($ageGroups as $ag)
                                        <option value="{{ $ag->id }}" {{ old('age_group_id', $appointment->age_group_id) == $ag->id ? 'selected' : '' }}>
                                            {{ $ag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="preferred_language_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('file.preferred_language') }}
                                </label>
                                <select name="preferred_language_id" id="preferred_language_id" {{ !$isPrivileged ? 'disabled' : '' }}
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all {{ !$isPrivileged ? 'bg-gray-50 dark:bg-gray-800' : '' }}">
                                    <option value="">{{ __('file.select_language') }}</option>
                                    @foreach($languages as $id => $name)
                                        <option value="{{ $id }}" {{ old('preferred_language_id', $appointment->preferred_language_id) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="preferred_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('file.preferred_time') }}
                                </label>
                                <select name="preferred_time" id="preferred_time" {{ !$isPrivileged ? 'disabled' : '' }}
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all {{ !$isPrivileged ? 'bg-gray-50 dark:bg-gray-800' : '' }}">
                                    <option value="">{{ __('file.select_time') ?? 'Select Time' }}</option>
                                    @foreach($preferredTimeOptions as $value => $label)
                                        <option value="{{ $value }}" {{ old('preferred_time', $appointment->preferred_time) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Schedule --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('file.schedule') }}</h2>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.date') }}
                            </label>
                            <input type="date" name="date" id="date_input" {{ !$isPrivileged ? 'disabled' : '' }}
                                   min="{{ now()->format('Y-m-d') }}"
                                   value="{{ old('date', $appointment->scheduled_start?->format('Y-m-d') ?? '') }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow [color-scheme:light] dark:[color-scheme:dark] {{ !$isPrivileged ? 'bg-gray-50 dark:bg-gray-800' : '' }}">
                            @error('date') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="slot" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.time_slot') }}
                            </label>
                            <select name="slot" id="slot_select" {{ !$isPrivileged ? 'disabled' : '' }}
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all {{ !$isPrivileged ? 'bg-gray-50 dark:bg-gray-800' : '' }}">
                                <option value="">{{ __('file.select_date_first') }}</option>
                            </select>
                            @error('slot') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>


                    </div>
                </div>
            </div>

            {{-- Row 2 — Status / Notes / Treatments --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Left: Status + Notes --}}
                <div class="space-y-6">

                    {{-- Status card --}}
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('file.status') }}</h2>
                        </div>
                        <div class="p-5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('file.active_status') }}
                            </label>
                            <select name="status" {{ !$isPrivileged ? 'disabled' : '' }}
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all {{ !$isPrivileged ? 'bg-gray-50 dark:bg-gray-800' : '' }}">
                                @if($appointment->status === 'pending')
                                    <option value="pending"   {{ old('status', $appointment->status) === 'pending'   ? 'selected' : '' }}>{{ __('file.status_pending') }}</option>
                                    <option value="approved"  {{ old('status', $appointment->status) === 'approved'  ? 'selected' : '' }}>{{ __('file.status_approved') }}</option>
                                    <option value="rejected"  {{ old('status', $appointment->status) === 'rejected'  ? 'selected' : '' }}>{{ __('file.status_rejected') }}</option>
                                    <option value="cancelled" {{ old('status', $appointment->status) === 'cancelled' ? 'selected' : '' }}>{{ __('file.status_cancelled') }}</option>
                                @elseif($appointment->status === 'approved')
                                    <option value="approved"  {{ old('status', $appointment->status) === 'approved'  ? 'selected' : '' }}>{{ __('file.status_approved') }}</option>
                                    <option value="completed" {{ old('status', $appointment->status) === 'completed' ? 'selected' : '' }}>{{ ucfirst(__('file.completed')) }}</option>
                                    <option value="cancelled" {{ old('status', $appointment->status) === 'cancelled' ? 'selected' : '' }}>{{ __('file.status_cancelled') }}</option>
                                @elseif($appointment->status === 'completed')
                                    <option value="completed" selected>{{ ucfirst(__('file.completed')) }}</option>
                                @else
                                    <option value="{{ $appointment->status }}" selected>{{ ucfirst($appointment->status) }}</option>
                                @endif
                            </select>
                            @error('status') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Notes card --}}
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('file.details') }}</h2>
                        </div>
                        <div class="p-5 space-y-4">
                            <div>
                                <label for="reason_for_visit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('file.reason_for_visit') }}
                                </label>
                                <textarea name="reason_for_visit" id="reason_for_visit" rows="3" {{ !$isPrivileged ? 'disabled' : '' }}
                                          class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none {{ !$isPrivileged ? 'bg-gray-50 dark:bg-gray-800' : '' }}"
                                          placeholder="{{ __('file.reason_for_visit_placeholder') }}">{{ old('reason_for_visit', $appointment->reason_for_visit ?? '') }}</textarea>
                                @error('reason_for_visit') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="patient_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('file.patient_notes') }}
                                </label>
                                <textarea name="patient_notes" id="patient_notes" rows="3" {{ !$isPrivileged ? 'disabled' : '' }}
                                          class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none {{ !$isPrivileged ? 'bg-gray-50 dark:bg-gray-800' : '' }}"
                                          placeholder="{{ __('file.notes_placeholder') }}">{{ old('patient_notes', $appointment->patient_notes ?? '') }}</textarea>
                            </div>

                            <div>
                                <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('file.admin_notes') }}
                                </label>
                                <textarea name="admin_notes" id="admin_notes" rows="3" {{ !$isPrivileged ? 'disabled' : '' }}
                                          class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none {{ !$isPrivileged ? 'bg-gray-50 dark:bg-gray-800' : '' }}"
                                          placeholder="{{ __('file.optional_notes') }}">{{ old('admin_notes', $appointment->admin_notes ?? '') }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Right: Treatments --}}
                @php
                    $isAssignedDoctor = auth()->user()->doctor && auth()->user()->doctor->id === $appointment->doctor_id;
                @endphp

                @if($isAssignedDoctor)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <div class="p-5">
                        {{-- Current attached treatments --}}
                        @if($appointment->treatments->isNotEmpty())
                            <div class="overflow-x-auto mb-5">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-900">
                                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.treatment') }}</th>
                                            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.unit_price') }}</th>
                                            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.qty') }}</th>
                                            <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.line_total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($appointment->treatments as $treatment)
                                            <tr>
                                                <td class="px-4 py-2.5 text-gray-900 dark:text-gray-200">
                                                    {{ $treatment->name }}
                                                    @if($treatment->code)
                                                        <span class="ml-1.5 text-xs text-gray-500 dark:text-gray-400">({{ $treatment->code }})</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2.5 text-right text-gray-700 dark:text-gray-300">
                                                    {{ $currency_code ?? 'LKR' }} {{ number_format($treatment->pivot->price_at_time ?? 0, 2) }}
                                                </td>
                                                <td class="px-4 py-2.5 text-right text-gray-700 dark:text-gray-300">
                                                    {{ $treatment->pivot->quantity ?? 1 }}
                                                </td>
                                                <td class="px-4 py-2.5 text-right font-medium text-gray-900 dark:text-white">
                                                    {{ $currency_code ?? 'LKR' }} {{ number_format(($treatment->pivot->quantity ?? 1) * ($treatment->pivot->price_at_time ?? 0), 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50 dark:bg-gray-900 font-medium">
                                        <tr>
                                            <td colspan="3" class="px-4 py-2.5 text-right text-gray-700 dark:text-gray-300 text-xs uppercase">
                                                {{ __('file.total_treatments_cost') }}
                                            </td>
                                            <td class="px-4 py-2.5 text-right text-indigo-600 dark:text-indigo-400">
                                                {{ $currency_code ?? 'LKR' }} {{ number_format($appointment->total_treatment_price ?? 0, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic mb-5">
                                {{ __('file.no_treatments_added_yet') }}
                            </p>
                        @endif

                        {{-- Available treatments checkbox list --}}
                        <div id="available-treatments">
                            <h4 class="text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wide mb-2">
                                {{ __('file.select_treatments') }}
                            </h4>
                            <div id="treatments-checkbox-list"
                                 class="max-h-64 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-900/50 dark:text-gray-100">
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">
                                    {{ __('file.select_doctor_for_treatments') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

        {{-- Bottom save bar --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('appointments.show', $appointment) }}"
               class="inline-flex items-center justify-center px-6 py-2.5 bg-white dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ __('file.cancel') }}
            </a>
            @if($isPrivileged)
            <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                {{ __('file.save_changes') }}
            </button>
            @endif
        </div>

    </div>
</form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const specSelect     = document.getElementById('specialization_id');
    const typeRadios    = document.querySelectorAll('.appointment-type-radio');
    const specGroup     = document.getElementById('specialization-group');
    const doctorGroup   = document.getElementById('doctor-group');
    const specReq       = document.getElementById('spec-required');
    const doctorReq     = document.getElementById('doctor-required');
    const doctorSelect  = document.getElementById('doctor_id');
    const dateInput     = document.getElementById('date_input');
    const slotSelect    = document.getElementById('slot_select');
    const treatmentsList = document.getElementById('treatments-checkbox-list');

    const ageGroupSelect = document.getElementById('age_group_id');
    const languageSelect = document.getElementById('preferred_language_id');

    const preDoctorId          = '{{ $appointment->doctor_id ?? "" }}';
    const currentTreatmentIds  = {{ json_encode($appointment->treatments->pluck('id')->toArray()) }};

    const ALL_AGE_GROUPS = @json($ageGroups);
    const ALL_LANGUAGES = @json($languages);

    function getCurrentType() {
        let val = '{{ \App\Models\Appointment::TYPE_SPECIFIC }}';
        typeRadios.forEach(r => { if(r.checked) val = r.value; });
        return val;
    }

    function getStatus() {
        const statusSelect = document.querySelector('select[name="status"]');
        return statusSelect ? statusSelect.value : '{{ $appointment->status }}';
    }

    let isAutoPopulating = false;

    function updateVisibility() {
        const type = getCurrentType();
        const status = getStatus();
        const isSpecific = type === '{{ \App\Models\Appointment::TYPE_SPECIFIC }}';
        const isAny      = type === '{{ \App\Models\Appointment::TYPE_ANY }}';

        if (specGroup) specGroup.classList.toggle('hidden', !(isAny || status === 'approved'));
        if (doctorGroup) doctorGroup.classList.toggle('hidden', !(isSpecific || status === 'approved'));
        if (specReq) specReq.classList.toggle('hidden', !(isAny || status === 'approved'));
        if (doctorReq) doctorReq.classList.toggle('hidden', !(isSpecific || status === 'approved'));

        if (specSelect) specSelect.required = isAny || status === 'approved';
        if (doctorSelect) doctorSelect.required = isSpecific || status === 'approved';

        // Update card styles
        typeRadios.forEach(radio => {
            const card = radio.closest('label');
            const isChecked = radio.checked;
            const span = card.querySelector('span');
            const dotContainer = card.querySelector('.w-4.h-4');

            if (isChecked) {
                card.classList.remove('border-gray-200', 'dark:border-gray-700', 'hover:border-gray-300', 'dark:hover:border-gray-600');
                card.classList.add('border-indigo-600', 'bg-indigo-50/50', 'dark:bg-indigo-900/10', 'dark:border-indigo-500');
                if (span) span.classList.replace('text-gray-700', 'text-indigo-900');
                if (span) span.classList.replace('dark:text-gray-300', 'dark:text-indigo-200');
                if (dotContainer) {
                    dotContainer.classList.replace('border-gray-300', 'border-indigo-600');
                    dotContainer.classList.replace('dark:border-gray-600', 'dark:border-indigo-400');
                    if (!dotContainer.querySelector('.w-2.h-2')) {
                        const dot = document.createElement('div');
                        dot.className = 'w-2 h-2 rounded-full bg-indigo-600 dark:bg-indigo-400';
                        dotContainer.appendChild(dot);
                    }
                }
            } else {
                card.classList.add('border-gray-200', 'dark:border-gray-700', 'hover:border-gray-300', 'dark:hover:border-gray-600');
                card.classList.remove('border-indigo-600', 'bg-indigo-50/50', 'dark:bg-indigo-900/10', 'dark:border-indigo-500');
                if (span) span.classList.replace('text-indigo-900', 'text-gray-700');
                if (span) span.classList.replace('dark:text-indigo-200', 'dark:text-gray-300');
                if (dotContainer) {
                    dotContainer.classList.replace('border-indigo-600', 'border-gray-300');
                    dotContainer.classList.replace('dark:border-indigo-400', 'dark:border-gray-600');
                    const dot = dotContainer.querySelector('.w-2.h-2');
                    if (dot) dot.remove();
                }
            }
        });

        if (isSpecific || status === 'approved') {
            loadFilteredDoctors();
        } else if (isAny) {
            doctorSelect.innerHTML = '<option value="">{{ __("file.select_doctor") }}</option>';
            doctorSelect.value = '';
            resetAttributes();
        }
    }

    function resetSlots() {
        slotSelect.innerHTML = '<option value="">{{ __("file.select_date_first") }}</option>';
        slotSelect.disabled  = true;
    }

    function loadSlots() {
        const doctorId = doctorSelect?.value || preDoctorId;
        const date     = dateInput.value;
        resetSlots();
        if (!doctorId || !date) return;

        slotSelect.disabled = false;
        slotSelect.innerHTML = '<option value="">{{ __("file.loading") }}</option>';

        const url = '{{ route("doctors.available-slots", ":doctor") }}'
            .replace(':doctor', doctorId) + '?date=' + encodeURIComponent(date);

        fetch(url)
            .then(r => r.json())
            .then(data => {
                slotSelect.innerHTML = '<option value="">{{ __("file.select_time_slot") }}</option>';
                if (data.slots?.length) {
                    data.slots.forEach(s => {
                        const opt    = document.createElement('option');
                        opt.value    = s.start + '|' + s.end;
                        opt.textContent = s.label;
                        if ('{{ $appointment->scheduled_start?->format("H:i") ?? "" }}' === s.start &&
                            '{{ $appointment->scheduled_end?->format("H:i")   ?? "" }}' === s.end) {
                            opt.selected = true;
                        }
                        slotSelect.appendChild(opt);
                    });
                } else {
                    slotSelect.innerHTML = '<option value="">No available slots</option>';
                }
            })
            .catch(() => {
                slotSelect.innerHTML = '<option value="">Error loading slots</option>';
            });
    }

    function resetAttributes() {
        const currentAgeGroup = ageGroupSelect.value;
        const currentLang = languageSelect.value;

        ageGroupSelect.innerHTML = '<option value="">{{ __("file.select_age_group") }}</option>';
        ALL_AGE_GROUPS.forEach(ag => {
            const opt = new Option(ag.name, ag.id);
            if (currentAgeGroup == ag.id) opt.selected = true;
            ageGroupSelect.add(opt);
        });

        languageSelect.innerHTML = '<option value="">{{ __("file.select_language") }}</option>';
        Object.entries(ALL_LANGUAGES).forEach(([id, name]) => {
            const opt = new Option(name, id);
            if (currentLang == id) opt.selected = true;
            languageSelect.add(opt);
        });
    }

    function loadFilteredDoctors() {
        if (!doctorSelect) return;

        const specId = specSelect.value;
        const ageGroupId = ageGroupSelect.value;
        const langId = languageSelect.value;
        const status = getStatus();
        const type = getCurrentType();

        let url = '{{ route("appointments.doctors.filtered") }}?';
        // Only apply filters if it's 'Any Doctor' mode
        if (type === '{{ \App\Models\Appointment::TYPE_ANY }}') {
            if (specId) url += `specialization_id=${specId}&`;
            if (ageGroupId) url += `age_group_id=${ageGroupId}&`;
            if (langId) url += `preferred_language_id=${langId}&`;
        }

        fetch(url)
            .then(r => r.json())
            .then(doctors => {
                const currentVal = doctorSelect.value || preDoctorId;
                doctorSelect.innerHTML = '<option value="">{{ __("file.select_doctor") }}</option>';
                
                doctors.forEach(doc => {
                    const opt = new Option(doc.text, doc.value);
                    if (currentVal == doc.value) opt.selected = true;
                    doctorSelect.add(opt);
                });
            })
            .catch(() => {
                doctorSelect.innerHTML = '<option value="">Error loading doctors</option>';
            });
    }

    function loadDoctorAttributes(doctorId) {
        if (!doctorId) {
            resetAttributes();
            return;
        }

        isAutoPopulating = true;
        fetch(`{{ url('doctors') }}/${doctorId}/attributes`)
            .then(response => response.json())
            .then(data => {
                if (data.specialization_id) {
                    specSelect.value = data.specialization_id;
                }

                // Filter age groups
                const currentAgeGroup = ageGroupSelect.value;
                ageGroupSelect.innerHTML = '<option value="">{{ __("file.select_age_group") }}</option>';
                const supportedAgeGroups = data.age_groups || [];
                
                const ageGroupsToPopulate = ALL_AGE_GROUPS.filter(ag => supportedAgeGroups.includes(ag.id));
                ageGroupsToPopulate.forEach(ag => {
                    const opt = new Option(ag.name, ag.id);
                    if (currentAgeGroup == ag.id) opt.selected = true;
                    ageGroupSelect.add(opt);
                });

                // Filter languages
                const currentLang = languageSelect.value;
                languageSelect.innerHTML = '<option value="">{{ __("file.select_language") }}</option>';
                const supportedLanguages = data.languages || [];

                const langsToPopulate = Object.entries(ALL_LANGUAGES).filter(([id, name]) => 
                    supportedLanguages.includes(parseInt(id))
                );
                langsToPopulate.forEach(([id, name]) => {
                    const opt = new Option(name, id);
                    if (currentLang == id) opt.selected = true;
                    languageSelect.add(opt);
                });
            })
            .finally(() => {
                isAutoPopulating = false;
            });
    }

    function loadDoctorTreatments() {
        if (!treatmentsList) return;

        const doctorId = doctorSelect?.value || preDoctorId;
        if (!doctorId) {
            treatmentsList.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">{{ __("file.select_doctor_for_treatments") }}</p>';
            return;
        }

        treatmentsList.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">{{ __("file.loading_treatments") }}</p>';

        const url = '{{ route("appointments.treatments", ":doctor") }}'.replace(':doctor', doctorId);

        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (!data.treatments || data.treatments.length === 0) {
                    treatmentsList.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">{{ __("file.no_treatments_for_doctor") }}</p>';
                    return;
                }
                let html = '';
                data.treatments.forEach(t => {
                    const isChecked = currentTreatmentIds.includes(t.id);
                    html += `
                        <label class="flex items-center py-2 px-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg cursor-pointer gap-3">
                            <input type="checkbox"
                                   name="treatment_ids[]"
                                   value="${t.id}"
                                   ${isChecked ? 'checked' : ''}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-500 rounded">
                            <span class="flex-1 text-sm text-gray-900 dark:text-gray-100">
                                ${t.name}
                                ${t.code ? `<span class="ml-1 text-xs text-gray-500 dark:text-gray-400">(${t.code})</span>` : ''}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">
                                {{ $currency_code ?? 'LKR' }} ${Number(t.price || 0).toFixed(2)}
                            </span>
                        </label>
                    `;
                });
                treatmentsList.innerHTML = html;
            })
            .catch(() => {
                treatmentsList.innerHTML = '<p class="text-sm text-red-600 dark:text-red-400 text-center py-6">{{ __("file.error_loading_treatments") }}</p>';
            });
    }

    // Event listeners
    typeRadios.forEach(r => {
        r.addEventListener('change', updateVisibility);
    });

    if (specSelect && doctorSelect) {
        specSelect.addEventListener('change', loadFilteredDoctors);
        ageGroupSelect.addEventListener('change', loadFilteredDoctors);
        languageSelect.addEventListener('change', loadFilteredDoctors);

        doctorSelect.addEventListener('change', () => {
            loadDoctorAttributes(doctorSelect.value);
            resetSlots();
            if (dateInput.value) loadSlots();
            loadDoctorTreatments();
        });

        const statusSelect = document.querySelector('select[name="status"]');
        if (statusSelect) {
            statusSelect.addEventListener('change', updateVisibility);
        }
    }

    dateInput.addEventListener('change', () => {
        if ((doctorSelect?.value || preDoctorId) && dateInput.value) {
            loadSlots();
        }
    });

    // Initial loads on page load
    updateVisibility();

    if (preDoctorId) {
        loadDoctorAttributes(preDoctorId);
        loadSlots();
        loadDoctorTreatments();
    }
});
</script>

<style>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection