{{-- resources/views/doctors/index.blade.php --}}
@extends('layouts.app')

@section('title', __('file.doctors'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.doctors') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.manage_doctor_records') }}</p>
        </div>
        <a href="{{ route('doctors.create') }}"
           class="inline-flex items-center px-4 py-2.5 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('file.add_doctor') }}
        </a>
    </div>

    <!-- Bulk Delete -->
    <form method="POST" action="{{ route('doctors.bulkDelete') }}" id="bulk-delete-form" class="hidden mb-6">
        @csrf @method('DELETE')
        <input type="hidden" name="ids" id="bulk-ids">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <span class="text-sm text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.doctor_selected') }}
                </span>
                <button type="submit" onclick="return confirm('{{ __('file.confirm_delete_selected') }}')"
                        class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                    {{ __('file.delete') }}
                </button>
            </div>
        </div>
    </form>

    <!-- Mobile Results Info -->
    <div class="sm:hidden text-sm text-gray-600 dark:text-gray-400 mb-4">
        {{ __('file.showing_results', ['from' => $doctors->firstItem(), 'to' => $doctors->lastItem(), 'total' => $doctors->total()]) }}
    </div>

    <!-- Mobile Sort -->
    <div class="sm:hidden mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">{{ __('file.sort_by') }}</h3>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <x-sort-link field="name" :sort="$sort" :direction="$direction">{{ __('file.name') }}</x-sort-link>
            <x-sort-link field="primary_specialty" :sort="$sort" :direction="$direction">{{ __('file.specialty') }}</x-sort-link>
            <x-sort-link field="is_active" :sort="$sort" :direction="$direction">{{ __('file.status') }}</x-sort-link>
            <x-sort-link field="appointments_count" :sort="$sort" :direction="$direction">{{ __('file.appointments') }}</x-sort-link>
        </div>
    </div>

    <!-- Mobile Cards -->
    <div class="space-y-4 sm:hidden">
        @forelse($doctors as $doctor)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <input type="checkbox" name="ids[]" value="{{ $doctor->id }}" class="row-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-gray-900">

                        <div class="flex-shrink-0">
                            @if($doctor->profile_photo)
                                <img class="h-14 w-14 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700"
                                     src="{{ asset('storage/' . $doctor->profile_photo) }}" alt="{{ $doctor->full_name }}">
                            @else
                                <div class="h-14 w-14 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                                    {{ substr($doctor->first_name, 0, 1) }}{{ substr($doctor->last_name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $doctor->full_name }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $doctor->license_number }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $doctor->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                        {{ $doctor->is_active ? __('file.active') : __('file.inactive') }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm mb-5">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">{{ __('file.specialty') }}</span>
                        <p class="font-medium truncate">{{ $doctor->primary_specialty ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">{{ __('file.appointments') }}</span>
                        <p class="font-medium">{{ $doctor->appointments_count }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">{{ __('file.contact') }}</span>
                        <p class="font-medium">{{ $doctor->phone ?? '—' }}</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <a href="{{ route('doctors.show', $doctor) }}" class="p-2.5 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    <a href="{{ route('doctors.edit', $doctor) }}" class="p-2.5 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('doctors.destroy', $doctor) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('{{ __('file.confirm_delete_doctor') }}')"
                                class="p-2.5 text-gray-600 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-500 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="mt-4 text-gray-500">{{ __('file.no_doctors_found') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table -->
    <div class="hidden sm:block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left"><input type="checkbox" id="select-all" class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900"></th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ __('file.doctor') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="primary_specialty" :sort="$sort" :direction="$direction">{{ __('file.specialty') }}</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="is_active" :sort="$sort" :direction="$direction">{{ __('file.status') }}</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 text-center dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="appointments_count" :sort="$sort" :direction="$direction">{{ __('file.appointments') }}</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="phone" :sort="$sort" :direction="$direction">{{ __('file.contact') }}</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ __('file.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($doctors as $doctor)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition">
                            <td class="px-4 py-4"><input type="checkbox" name="ids[]" value="{{ $doctor->id }}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0">
                                        @if($doctor->profile_photo)
                                            <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700"
                                                 src="{{ asset('storage/' . $doctor->profile_photo) }}" alt="{{ $doctor->full_name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-medium text-sm">
                                                {{ substr($doctor->first_name, 0, 1) }}{{ substr($doctor->last_name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $doctor->full_name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $doctor->license_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                    {{ Str::limit($doctor->primary_specialty, 20) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $doctor->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ $doctor->is_active ? __('file.active') : __('file.inactive') }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center text-sm font-medium text-gray-900 dark:text-white">
                                {{ $doctor->appointments_count }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $doctor->phone ?? '—' }}</td>
                            <td class="px-4 py-4 text-sm">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('doctors.show', $doctor) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition" title="{{ __('file.view_profile') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('doctors.edit', $doctor) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition" title="{{ __('file.edit') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('doctors.destroy', $doctor) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('{{ __('file.confirm_delete_doctor') }}')"
                                                class="text-gray-600 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-500 transition" title="{{ __('file.delete') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                {{ __('file.no_doctors_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $doctors->appends(request()->query())->links() }}
    </div>
</div>

<script>
    document.getElementById('select-all')?.addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkDelete();
    });
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.addEventListener('change', updateBulkDelete));

    function updateBulkDelete() {
        const checked = document.querySelectorAll('.row-checkbox:checked').length;
        const form = document.getElementById('bulk-delete-form');
        const idsInput = document.getElementById('bulk-ids');
        document.getElementById('selected-count').textContent = checked;
        if (checked > 0) {
            form.classList.remove('hidden');
            idsInput.value = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value).join(',');
        } else {
            form.classList.add('hidden');
        }
    }
</script>
@endsection