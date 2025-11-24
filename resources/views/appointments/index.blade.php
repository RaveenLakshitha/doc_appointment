{{-- resources/views/appointments/index.blade.php --}}
@extends('layouts.app')
@section('title', __('file.appointments'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.appointments') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.manage_appointments') }}</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <!-- Search Form -->
            <form method="GET" id="search-form" class="flex gap-1 flex-1 sm:flex-initial">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('file.search_appointments_placeholder') }}"
                       class="w-full px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded 
                              focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 
                              focus:border-transparent dark:bg-transparent dark:text-white pr-10">

                <button type="submit"
                        class="px-2.5 py-1.5 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                <a href="{{ route('appointments.index') }}"
                   class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded hover:bg-gray-200 dark:hover:bg-gray-600 border dark:border-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            </form>

            <!-- Add New Appointment Button -->
            <a href="{{ route('appointments.create') }}"
               class="px-4 py-1.5 bg-gray-900 dark:bg-gray-700 dark:bg-transparent border border-gray-300 dark:border-gray-200 text-white text-sm font-medium rounded hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors flex items-center justify-center gap-1 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">{{ __('file.new_appointment') }}</span>
                <span class="sm:hidden">{{ __('file.add') }}</span>
            </a>
        </div>
    </div>

    <!-- Bulk Delete Bar -->
    <form method="POST" action="{{ route('appointments.bulkDelete') }}" id="bulk-delete-form" class="hidden mb-4">
        @csrf @method('DELETE')
        <input type="hidden" name="ids" id="bulk-ids">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded p-3">
            <div class="flex items-center justify-between gap-3">
                <span class="text-sm text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.appointment_selected') }}
                </span>
                <button type="submit" onclick="return confirm('{{ __('file.confirm_delete_selected') }}')"
                        class="px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ __('file.delete') }}
                </button>
            </div>
        </div>
    </form>

    <!-- Mobile: Showing X of Y results -->
    <div class="sm:hidden mb-3 text-xs text-gray-600 dark:text-gray-400">
        {{ __('file.showing_results', ['from' => $appointments->firstItem(), 'to' => $appointments->lastItem(), 'total' => $appointments->total()]) }}
    </div>

    <!-- Mobile: Sort Links -->
    <div class="sm:hidden mb-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-3">
        <h3 class="text-xs font-semibold text-gray-900 dark:text-white mb-2">{{ __('file.sort_by') }}</h3>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <x-sort-link field="appointment_datetime" :sort="$sort" :direction="$direction">{{ __('file.date_time') }}</x-sort-link>
            <x-sort-link field="patient_name" :sort="$sort" :direction="$direction">{{ __('file.patient') }}</x-sort-link>
            <x-sort-link field="doctor_name" :sort="$sort" :direction="$direction">{{ __('file.doctor') }}</x-sort-link>
            <x-sort-link field="status" :sort="$sort" :direction="$direction">{{ __('file.status') }}</x-sort-link>
        </div>
    </div>

    <!-- Mobile Cards -->
    <div class="space-y-3 sm:hidden">
        @forelse($appointments as $appt)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-3">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="ids[]" value="{{ $appt->id }}" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-1 focus:ring-gray-900">
                        <div>
                            <div class="font-medium text-sm text-gray-900 dark:text-white">{{ $appt->patient->full_name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Dr. {{ $appt->doctor->full_name }}</div>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                        @switch($appt->status)
                            @case('pending')   bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 @break
                            @case('confirmed') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 @break
                            @case('completed') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 @break
                            @case('cancelled') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 @break
                            @default bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400
                        @endswitch">
                        {{ ucfirst($appt->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs mb-3">
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 mb-0.5">{{ __('file.date') }}</div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $appt->appointment_datetime->format('M j, Y') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 mb-0.5">{{ __('file.time') }}</div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $appt->appointment_datetime->format('g:i A') }}</div>
                    </div>
                    <div class="col-span-2">
                        <div class="text-gray-500 dark:text-gray-400 mb-0.5">{{ __('file.duration') }}</div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $appt->duration_minutes }} {{ __('file.minutes') }}</div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700 pt-2 -mx-3 px-3 -mb-3 bg-gray-50 dark:bg-gray-900/30">
                    <a href="{{ route('appointments.show', $appt) }}" class="p-1.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" title="{{ __('file.view') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    <a href="{{ route('appointments.edit', $appt) }}" class="p-1.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" title="{{ __('file.edit') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('appointments.destroy', $appt) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('{{ __('file.confirm_delete_appointment') }}')"
                                class="p-1.5 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="{{ __('file.delete') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('file.no_appointments_found') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table -->
    <div class="hidden sm:block bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left"><input type="checkbox" id="select-all" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-1 focus:ring-gray-900"></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300"><x-sort-link field="appointment_datetime" :sort="$sort" :direction="$direction">{{ __('file.date_time') }}</x-sort-link></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('file.patient') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('file.doctor') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('file.duration') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300"><x-sort-link field="status" :sort="$sort" :direction="$direction">{{ __('file.status') }}</x-sort-link></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('file.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($appointments as $appt)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="px-4 py-3"><input type="checkbox" name="ids[]" value="{{ $appt->id }}" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-1 focus:ring-gray-900"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $appt->appointment_datetime->format('M j, Y') }}<br>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $appt->appointment_datetime->format('g:i A') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-sm text-gray-900 dark:text-white">{{ $appt->patient->full_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">MRN: {{ $appt->patient->medical_record_number }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Dr. {{ $appt->doctor->full_name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $appt->duration_minutes }} {{ __('file.min') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @switch($appt->status)
                                        @case('pending')   bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 @break
                                        @case('confirmed') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 @break
                                        @case('completed') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 @break
                                        @case('cancelled') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 @break
                                        @default bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400
                                    @endswitch">
                                    {{ ucfirst($appt->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('appointments.show', $appt) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" title="{{ __('file.view') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('appointments.edit', $appt) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" title="{{ __('file.edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('appointments.destroy', $appt) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('{{ __('file.confirm_delete_appointment') }}')"
                                                class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="{{ __('file.delete') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm">{{ __('file.no_appointments_found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6 sm:hidden">
        {{ $appointments->appends(request()->query())->links() }}
    </div>
    <div class="hidden sm:block mt-6">
        {{ $appointments->appends(request()->query())->links() }}
    </div>
</div>

<script>
    document.getElementById('select-all')?.addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkDelete();
    });
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.addEventListener('change', updateBulkDelete));

    function updateBulkDelete() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const count = checked.length;
        const form = document.getElementById('bulk-delete-form');
        const idsInput = document.getElementById('bulk-ids');
        const countSpan = document.getElementById('selected-count');

        if (count > 0) {
            form.classList.remove('hidden');
            idsInput.value = Array.from(checked).map(cb => cb.value).join(',');
            countSpan.textContent = count;
        } else {
            form.classList.add('hidden');
        }
    }
</script>
@endsection