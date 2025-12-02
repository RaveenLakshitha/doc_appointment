@extends('layouts.app')
@section('title', __('file.doctors'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.doctors') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.manage_doctor_records') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <div class="flex-1 sm:flex-initial">
                <input type="text" id="live-search" value="{{ request('search') }}"
                       placeholder="{{ __('file.search_doctors') }}"
                       class="w-full sm:w-80 px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition">
            </div>

            <!-- Updated Button: Icon only + always active style since panel is open -->
            <button type="button" id="toggle-filters"
                    class="flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium 
                           bg-blue-600 text-white hover:bg-blue-700 
                           dark:bg-blue-500 dark:hover:bg-blue-600 
                           rounded-lg transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
            </button>

            <a href="{{ route('doctors.create') }}"
               class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">{{ __('file.add_doctor') }}</span>
            </a>
        </div>
    </div>

    <!-- Filters Panel – NOW OPEN BY DEFAULT -->
    <div id="filters-panel"
         class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 mb-6 
                transition-all duration-300 ease-in-out overflow-hidden origin-top">
        <form id="filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Specialty</label>
                <select name="specialty" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-900 dark:text-white">
                    <option value="">All Specialties</option>
                    @foreach($specialties as $id => $name)
                        <option value="{{ $id }}" {{ request('specialty') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-900 dark:text-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Gender</label>
                <select name="gender" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-900 dark:text-white">
                    <option value="">All Gender</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                <select name="department" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-900 dark:text-white">
                    <option value="">All Departments</option>
                    @foreach($departments as $id => $name)
                        <option value="{{ $id }}" {{ request('department') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <a href="{{ route('doctors.index') }}"
                   class="w-full px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Clear All
                </a>
            </div>
        </form>
    </div>

    <!-- Rest of your content (bulk delete, mobile sort, table, etc.) remains unchanged -->
    <form method="POST" action="{{ route('doctors.bulkDelete') }}" id="bulk-delete-form" class="hidden mb-6">
        @csrf @method('DELETE')
        <input type="hidden" name="ids" id="bulk-ids">
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-800 rounded-lg p-4 flex items-center justify-between">
            <span class="text-red-800 dark:text-red-300 font-medium">
                <span id="selected-count">0</span> doctors selected
            </span>
            <button type="submit" onclick="return confirm('Permanently delete selected doctors?')"
                    class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition">
                Delete Selected
            </button>
        </div>
    </form>

    <div class="sm:hidden mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border p-4">
        <h3 class="text-sm font-semibold mb-3">Sort by</h3>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <a href="#" data-sort="name" class="text-blue-600 hover:underline">Name</a>
            <a href="#" data-sort="specialty" class="text-blue-600 hover:underline">Specialty</a>
            <a href="#" data-sort="is_active" class="text-blue-600 hover:underline">Status</a>
            <a href="#" data-sort="appointments_count" class="text-blue-600 hover:underline">Appointments</a>
        </div>
    </div>

    <div id="doctors-container">
        @include('doctors.partials.table')
    </div>
</div>

<script>
    const filtersPanel    = document.getElementById('filters-panel');
    const toggleBtnFilter = document.getElementById('toggle-filters');
    const liveSearch      = document.getElementById('live-search');
    const filterForm      = document.getElementById('filter-form');
    let debounceTimer;
    let isFilterOpen = true;

    function hasActiveFilters() {
        const params = new URLSearchParams(location.search);
        return ['search','specialty','status','gender','department'].some(k => params.has(k) && params.get(k));
    }

    function setFilterHeight() {
        if (isFilterOpen) {
            filtersPanel.style.maxHeight = (filterForm.scrollHeight + 60) + 'px';
            filtersPanel.style.opacity = '1';
            filtersPanel.style.marginBottom = '1.5rem';
        } else {
            filtersPanel.style.maxHeight = '0';
            filtersPanel.style.opacity = '0';
            filtersPanel.style.marginBottom = '0';
        }
    }

    function updateButtonState() {
        if (isFilterOpen) {
            toggleBtnFilter.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            toggleBtnFilter.classList.add('bg-blue-600', 'text-white', 'dark:bg-blue-500', 'hover:bg-blue-700');
        } else {
            toggleBtnFilter.classList.remove('bg-blue-600', 'text-white', 'dark:bg-blue-500', 'hover:bg-blue-700');
            toggleBtnFilter.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
        }
    }

    function buildUrl(params) {
        const base = '{{ route("doctors.index") }}';
        const sep = base.includes('?') ? '&' : '?';
        return base + sep + params.toString();
    }

    function applyFilters() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();

        for (const [key, value] of formData) {
            if (value) params.set(key, value);
        }

        if (liveSearch.value.trim()) {
            params.set('search', liveSearch.value.trim());
        } else {
            params.delete('search');
        }

        const current = new URLSearchParams(location.search);
        if (!params.has('sort')) params.set('sort', current.get('sort') || 'name');
        if (!params.has('direction')) params.set('direction', current.get('direction') || 'asc');
        params.delete('page');

        const newUrl = buildUrl(params);
        history.replaceState(null, '', newUrl);

        fetch(newUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => {
            if (!r.ok) throw new Error('Network error');
            return r.text();
        })
        .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const container = doc.querySelector('#doctors-container');
            if (container) {
                document.getElementById('doctors-container').innerHTML = container.innerHTML;
                bindEvents();
                syncUI();
            }
        })
        .catch(err => console.error(err));
    }

    function syncUI() {
        if (hasActiveFilters() && !isFilterOpen) {
            isFilterOpen = true;
            setFilterHeight();
            updateButtonState();
        }
    }

    toggleBtnFilter.addEventListener('click', () => {
        isFilterOpen = !isFilterOpen;
        setFilterHeight();
        updateButtonState();
    });

    liveSearch.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(applyFilters, 450);
    });

    filterForm.addEventListener('change', applyFilters);

    document.addEventListener('click', e => {
        const sortLink = e.target.closest('[data-sort]');
        if (sortLink) {
            e.preventDefault();
            const sort = sortLink.dataset.sort;
            const params = new URLSearchParams(location.search);
            const curDir = params.get('direction') || 'asc';
            const newDir = (params.get('sort') === sort && curDir === 'asc') ? 'desc' : 'asc';
            params.set('sort', sort);
            params.set('direction', newDir);
            params.delete('page');
            const url = buildUrl(params);
            history.replaceState(null, '', url);
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const container = doc.querySelector('#doctors-container');
                    if (container) {
                        document.getElementById('doctors-container').innerHTML = container.innerHTML;
                        bindEvents();
                        syncUI();
                    }
                });
        }

        const pageLink = e.target.closest('.pagination a');
        if (pageLink) {
            e.preventDefault();
            const url = pageLink.href;
            history.replaceState(null, '', url);
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const container = doc.querySelector('#doctors-container');
                    if (container) {
                        document.getElementById('doctors-container').innerHTML = container.innerHTML;
                        bindEvents();
                        syncUI();
                    }
                });
        }
    });

    function bindEvents() {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.onchange = updateBulk);
        const selectAll = document.getElementById('select-all');
        if (selectAll) selectAll.onchange = () => document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = selectAll.checked);
        updateBulk();
    }

    function updateBulk() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const form = document.getElementById('bulk-delete-form');
        const count = document.getElementById('selected-count');
        const ids = document.getElementById('bulk-ids');
        if (checked.length > 0) {
            form.classList.remove('hidden');
            count.textContent = checked.length;
            ids.value = Array.from(checked).map(c => c.value).join(',');
        } else {
            form.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        setFilterHeight();
        updateButtonState();
        bindEvents();
        syncUI();
    });

    window.addEventListener('resize', () => {
        if (isFilterOpen) setFilterHeight();
    });
</script>
@endsection