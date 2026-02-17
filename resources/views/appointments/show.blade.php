@extends('layouts.app')

@section('title', __('file.appointment_number') . $appointment->id . ' - ' . ($appointment->patient?->full_name ?? __('file.unknown')))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 pb-8 sm:py-12 pt-20">

    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-6">
        <a href="{{ route('appointments.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
            {{ __('file.appointments') }}
        </a>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="font-medium text-gray-900 dark:text-white">
            #{{ $appointment->id }} • {{ Str::limit($appointment->patient?->full_name ?? '—', 28) }}
        </span>
    </div>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('file.appointment_number') }}{{ $appointment->id }}
            </h1>
            <div class="flex flex-wrap gap-2 mt-2">
                <span class="px-2.5 py-1 text-xs font-medium rounded-full
                    @switch($appointment->status)
                        @case('pending')    bg-amber-100 text-amber-800 @break
                        @case('approved')   bg-green-100 text-green-800 @break
                        @case('confirmed')  bg-blue-100 text-blue-800 @break
                        @case('completed')  bg-emerald-100 text-emerald-800 @break
                        @case('cancelled')  bg-red-100 text-red-800 @break
                        @case('rejected')   bg-gray-100 text-gray-800 @break
                        @default            bg-gray-100 text-gray-700
                    @endswitch">
                    {{ ucfirst(__("file.{$appointment->status}")) }}
                </span>

                @if($appointment->appointment_type)
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-violet-100 text-violet-800">
                        {{ ucwords(str_replace('_', ' ', $appointment->appointment_type)) }}
                    </span>
                @endif
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('appointments.edit', $appointment) }}"
               class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900">
                {{ __('file.edit') }}
            </a>
            <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" class="inline">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('{{ __('file.delete_confirm') }}')"
                        class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                    {{ __('file.delete') }}
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3">{{ __('file.patient') }}</h3>
            <a href="{{ route('patients.show', $appointment->patient) }}"
               class="block text-base font-medium text-gray-900 dark:text-white hover:text-indigo-600">
                {{ $appointment->patient?->full_name ?? '—' }}
            </a>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                MRN: {{ $appointment->patient?->medical_record_number ?? '—' }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3">{{ __('file.doctor') }}</h3>
            <p class="text-base font-medium text-gray-900 dark:text-white">
                {{ $appointment->doctor ? 'Dr. ' . $appointment->doctor->getFullNameAttribute() : __('file.not_assigned') }}
            </p>
            @if($appointment->doctor?->primarySpecialization?->name)
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ ucwords(str_replace('_', ' ', $appointment->doctor->primarySpecialization->name)) }}
                </p>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3">{{ __('file.schedule') }}</h3>
            @if($appointment->scheduled_start)
                <p class="text-base font-medium text-gray-900 dark:text-white">
                    {{ $appointment->scheduled_start->format('M j, Y') }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $appointment->scheduled_start->format('g:i A') }} – {{ $appointment->scheduled_end->format('g:i A') }}
                </p>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 italic">{{ __('file.not_scheduled_yet') }}</p>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm mb-8">
        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-4">{{ __('file.appointment_details') }}</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.type') }}</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                    {{ $appointment->appointment_type ? ucwords(str_replace('_', ' ', $appointment->appointment_type)) : '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.duration') }}</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                    {{ $appointment->duration_minutes ? $appointment->duration_minutes . ' min' : '—' }}
                </p>
            </div>
            @if($appointment->appointment_type === \App\Models\Appointment::TYPE_ANY && $appointment->specialization)
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.specialization') }}</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                    {{ $appointment->specialization->name ?? '—' }}
                </p>
            </div>
            @endif
            <div class="col-span-2 sm:col-span-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.reason') }}</p>
                <p class="text-sm text-gray-900 dark:text-white mt-1 whitespace-pre-line">
                    {{ $appointment->reason_for_visit ?? '—' }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm mb-8">
        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-4 uppercase tracking-wide">
            {{ __('file.treatments') }}
        </h3>

        @if($appointment->treatments->isEmpty())
            <div class="text-center py-10 text-gray-500 dark:text-gray-400 italic">
                {{ __('file.no_treatments_added_yet') }}
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.treatment') }}
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.unit_price') }}
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.qty') }}
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.line_total') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($appointment->treatments as $treatment)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200">
                                    {{ $treatment->name }}
                                    @if($treatment->code)
                                        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $treatment->code }})</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700 dark:text-gray-300">
                                    {{ $currency_code ?? 'LKR' }} {{ number_format($treatment->pivot->price_at_time ?? 0, 2) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700 dark:text-gray-300">
                                    {{ $treatment->pivot->quantity ?? 1 }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white">
                                    {{ $currency_code ?? 'LKR' }} {{ number_format(($treatment->pivot->quantity ?? 1) * ($treatment->pivot->price_at_time ?? 0), 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-800/50 font-medium">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">
                                {{ __('file.total_treatments_cost') }}
                            </td>
                            <td class="px-4 py-3 text-right text-indigo-600 dark:text-indigo-400">
                                {{ $currency_code ?? 'LKR' }} {{ number_format($appointment->total_treatment_price ?? 0, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    @if($appointment->patient_notes || $appointment->doctor_notes || $appointment->admin_notes)
    <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm">
        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-4">{{ __('file.notes') }}</h3>
        <div class="space-y-5">
            @if($appointment->patient_notes)
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('file.patient_notes') }}</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 whitespace-pre-line">{{ $appointment->patient_notes }}</p>
            </div>
            @endif

            @if($appointment->doctor_notes)
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('file.doctor_notes') }}</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 whitespace-pre-line">{{ $appointment->doctor_notes }}</p>
            </div>
            @endif

            @if($appointment->admin_notes)
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('file.admin_notes') }}</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 whitespace-pre-line">{{ $appointment->admin_notes }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>

@if($appointment->status === 'pending')
<div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-4 py-4 z-50">
    <div class="max-w-6xl mx-auto flex justify-end gap-4">
        <form action="{{ route('appointments.reject', $appointment) }}" method="POST" class="inline">
            @csrf @method('PATCH')
            <button type="submit" class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700">
                {{ __('file.reject') }}
            </button>
        </form>
        <form action="{{ route('appointments.approve', $appointment) }}" method="POST" class="inline">
            @csrf @method('PATCH')
            <button type="submit" onclick="return confirm('{{ __('file.approve_confirm') }}')"
                    class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700">
                {{ __('file.approve') }}
            </button>
        </form>
    </div>
</div>
@endif

@if($appointment->status === 'approved')
<div class="mt-10 px-4 sm:px-6 lg:px-8 flex justify-end gap-4">
    <a href="{{ route('appointments.prescription.create', $appointment) }}"
       class="inline-flex items-center px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        {{ __('file.create_prescription') }}
    </a>

    @if($appointment->prescriptions()->exists())
        <a href="{{ route('prescriptions.show', $appointment->prescriptions->first()) }}"
           class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition">
            {{ __('file.view_prescription') }}
        </a>
    @endif
</div>
@endif

@endsection