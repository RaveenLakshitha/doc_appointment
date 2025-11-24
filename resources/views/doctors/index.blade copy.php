{{-- resources/views/doctors/index.blade.php --}}
@extends('layouts.app')
@section('title', __('file.doctors'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.doctors') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.manage_doctor_records') }}</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <form method="GET" id="search-form" class="flex gap-1 flex-1 sm:flex-initial">
                <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="{{ __('file.search_placeholder') }}"
                   class="w-full px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded 
                          focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 
                          focus:border-transparent dark:bg-gray-800 dark:text-white 
                          pr-10">

                <button type="submit"
                        class="px-2.5 py-1.5 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                <a href="{{ route('doctors.index') }}"
                   class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            </form>

            <a href="{{ route('doctors.create') }}"
               class="px-4 py-1.5 bg-gray-900 dark:bg-gray-700 dark:bg-transparent border border-gray-300 dark:border-gray-200 text-white text-sm font-medium rounded hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors flex items-center justify-center gap-1 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">{{ __('file.add_doctor') }}</span>
                <span class="sm:hidden">{{ __('file.add') }}</span>
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('doctors.bulkDelete') }}" id="bulk-delete-form" class="hidden mb-4">
        @csrf @method('DELETE')
        <input type="hidden" name="ids" id="bulk-ids">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded p-3">
            <div class="flex items-center justify-between gap-3">
                <span class="text-sm text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.doctor_selected') }}
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

    <div class="sm:hidden mb-3 text-xs text-gray-600 dark:text-gray-400">
        {{ __('file.showing_results', ['from' => $doctors->firstItem(), 'to' => $doctors->lastItem(), 'total' => $doctors->total()]) }}
    </div>

    <div class="sm:hidden mb-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-3">
        <h3 class="text-xs font-semibold text-gray-900 dark:text-white mb-2">{{ __('file.sort_by') }}</h3>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <x-sort-link field="name" :sort="$sort" :direction="$direction">{{ __('file.name') }}</x-sort-link>
            <x-sort-link field="primary_specialty" :sort="$sort" :direction="$direction">{{ __('file.specialty') }}</x-sort-link>
            <x-sort-link field="is_active" :sort="$sort" :direction="$direction">{{ __('file.status') }}</x-sort-link>
            <x-sort-link field="appointments_count" :sort="$sort" :direction="$direction">{{ __('file.appointments') }}</x-sort-link>
        </div>
    </div>

    <div class="space-y-3 sm:hidden">
        @forelse($doctors as $doctor)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-3">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="ids[]" value="{{ $doctor->id }}" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex-shrink-0">
                                @if($doctor->profile_photo)
                                    <img src="{{ Storage::url($doctor->profile_photo) }}" alt="{{ $doctor->full_name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="font-medium text-sm text-gray-900 dark:text-white">{{ $doctor->full_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $doctor->license_number }}</div>
                            </div>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $doctor->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                        {{ $doctor->is_active ? __('file.active') : __('file.inactive') }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs mb-3">
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 mb-0.5">{{ __('file.specialty') }}</div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $doctor->primary_specialty ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 mb-0.5">{{ __('file.appointments') }}</div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $doctor->appointments_count }}</div>
                    </div>
                    <div class="col-span-2">
                        <div class="text-gray-500 dark:text-gray-400 mb-0.5">{{ __('file.contact') }}</div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $doctor->phone ?? '—' }}</div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700 pt-2 -mx-3 px-3 -mb-3 bg-gray-50 dark:bg-gray-900/30">
                    <a href="{{ route('doctors.show', $doctor) }}" class="p-1.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" title="{{ __('file.view_profile') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <a href="{{ route('doctors.edit', $doctor) }}" class="p-1.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" title="{{ __('file.edit') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <form method="POST" action="{{ route('doctors.destroy', $doctor) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('{{ __('file.confirm_delete_doctor') }}')"
                                class="p-1.5 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="{{ __('file.delete') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('file.no_doctors_found') }}</p>
            </div>
        @endforelse
    </div>

    <div class="hidden sm:block bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left"><input type="checkbox" id="select-all" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500"></th>
                        <th class="px-4 py-3"></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300"><x-sort-link field="name" :sort="$sort" :direction="$direction">{{ __('file.name') }}</x-sort-link></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300"><x-sort-link field="primary_specialty" :sort="$sort" :direction="$direction">{{ __('file.specialty') }}</x-sort-link></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300"><x-sort-link field="is_active" :sort="$sort" :direction="$direction">{{ __('file.status') }}</x-sort-link></th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300"><x-sort-link field="appointments_count" :sort="$sort" :direction="$direction">{{ __('file.appointments') }}</x-sort-link></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300"><x-sort-link field="phone" :sort="$sort" :direction="$direction">{{ __('file.contact') }}</x-sort-link></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('file.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($doctors as $doctor)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="px-4 py-3"><input type="checkbox" name="ids[]" value="{{ $doctor->id }}" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500"></td>
                            <td class="px-4 py-3">
                                <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700">
                                    @if($doctor->profile_photo)
                                        <img src="{{ Storage::url($doctor->profile_photo) }}" alt="{{ $doctor->full_name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 font-medium text-sm text-gray-900 dark:text-white">{{ $doctor->full_name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300">
                                    {{ Str::limit($doctor->primary_specialty, 20) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $doctor->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                    {{ $doctor->is_active ? __('file.active') : __('file.inactive') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-white">{{ $doctor->appointments_count }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $doctor->phone }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('doctors.show', $doctor) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" title="{{ __('file.view_profile') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('doctors.edit', $doctor) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" title="{{ __('file.edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('doctors.destroy', $doctor) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('{{ __('file.confirm_delete_doctor') }}')"
                                                class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="{{ __('file.delete') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_doctors_found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 sm:hidden">
        {{ $doctors->appends(request()->query())->links() }}
    </div>

    <div class="hidden sm:block mt-6">
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