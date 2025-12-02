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
            <form id="search-form" class="flex gap-1 flex-1 sm:flex-initial">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('file.search_placeholder') }}"
                       class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-white transition">

                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                <button type="button" onclick="clearSearch()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </form>

            <a href="{{ route('doctors.create') }}" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg flex items-center gap-2 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="7" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">{{ __('file.add_doctor') }}</span>
            </a>
        </div>
    </div>

    <!-- Bulk Delete Bar -->
    <form method="POST" action="{{ route('doctors.bulkDelete') }}" id="bulk-delete-form" class="hidden mb-6">
        @csrf @method('DELETE')
        <input type="hidden" name="ids" id="bulk-ids">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 flex items-center justify-between">
            <span class="text-red-800 dark:text-red-300 font-medium">
                <span id="selected-count">0</span> {{ __('file.doctor_selected') }}
            </span>
            <button type="submit" onclick="return confirm('{{ __('file.confirm_delete_selected') }}')" 
                    class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                {{ __('file.delete_selected') }}
            </button>
        </div>
    </form>

    <!-- Mobile: Results Count -->
    <div class="sm:hidden mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('file.showing_results', ['from' => $doctors->firstItem(), 'to' => $doctors->lastItem(), 'total' => $doctors->total()]) }}
    </div>

    <!-- Mobile Sort Links -->
    <div class="sm:hidden mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('file.sort_by') }}</h3>
        <div class="grid grid-cols-2 gap-3">
            <a href="#" data-sort="name" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Name</a>
            <a href="#" data-sort="primary_specialty" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Specialty</a>
            <a href="#" data-sort="is_active" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Status</a>
            <a href="#" data-sort="appointments_count" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Appointments</a>
        </div>
    </div>

    <!-- AJAX Container -->
    <div id="doctors-container">
        @include('doctors.partials.table')
    </div>
</div>

<script>
    document.getElementById('search-form').addEventListener('submit', e => e.preventDefault());

    let searchTimeout;
    document.querySelector('input[name="search"]').addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadDoctors, 400);
    });

    function clearSearch() {
        document.querySelector('input[name="search"]').value = '';
        loadDoctors();
    }

    function loadDoctors(params = null) {
        if (!params) {
            const formData = new FormData(document.getElementById('search-form'));
            params = new URLSearchParams(formData);
        }

        const current = new URLSearchParams(window.location.search);
        if (!params.has('sort')) params.set('sort', current.get('sort') || 'name');
        if (!params.has('direction')) params.set('direction', current.get('direction') || 'asc');
        if (params.get('search')?.trim() === '') params.delete('search');

        const url = '{{ route('doctors.index') }}?' + params.toString();

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newContainer = doc.querySelector('#doctors-container');
            if (newContainer) {
                document.getElementById('doctors-container').innerHTML = newContainer.innerHTML;
                history.replaceState(null, '', url);
                bindEvents();
            }
        });
    }

    function bindEvents() {
        // Sort Links
        document.querySelectorAll('[data-sort]').forEach(link => {
            link.onclick = function(e) {
                e.preventDefault();
                const field = this.dataset.sort;
                const params = new URLSearchParams(window.location.search);
                if (params.get('sort') === field && params.get('direction') === 'asc') {
                    params.set('direction', 'desc');
                } else {
                    params.set('direction', 'asc');
                }
                params.set('sort', field);
                loadDoctors(params);
            };
        });

        // Pagination
        document.querySelectorAll('.pagination a').forEach(link => {
            link.onclick = e => {
                e.preventDefault();
                loadDoctors(new URL(link.href).searchParams);
            };
        });

        // Bulk Delete
        document.getElementById('select-all')?.addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
            updateBulkDelete();
        });

        document.querySelectorAll('.row-checkbox').forEach(cb => cb.addEventListener('change', updateBulkDelete));
        updateBulkDelete();
    }

    function updateBulkDelete() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const form = document.getElementById('bulk-delete-form');
        const idsInput = document.getElementById('bulk-ids');
        const countSpan = document.getElementById('selected-count');

        if (checked.length > 0) {
            form.classList.remove('hidden');
            idsInput.value = Array.from(checked).map(c => c.value).join(',');
            countSpan.textContent = checked.length;
        } else {
            form.classList.add('hidden');
        }
    }

    bindEvents();
</script>
@endsection