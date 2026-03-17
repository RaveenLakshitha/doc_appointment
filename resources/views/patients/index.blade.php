@extends('layouts.app')

@section('title', __('file.patients'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.patients') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_patient_records') }}
                </p>
            </div>

            <div class="flex flex-row-reverse sm:flex-row gap-3 w-full sm:w-auto justify-between sm:justify-end">
                <button type="button" id="filter-button"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-600 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    {{ __('file.Filters') }}
                    <span id="filter-count"
                        class="hidden ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200"></span>
                </button>

                <button type="button" onclick="document.getElementById('import-modal').classList.remove('hidden');"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M16 8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    {{ __('file.import') }}
                </button>

                <a href="{{ route('patients.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('file.add_patient') }}
                </a>
            </div>
        </div>

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('patients.bulkDelete') }}" id="bulk-delete-form-el"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
                <div id="bulk-ids-container"></div>
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.patient_selected') }}
                </span>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                    {{ __('file.delete_selected') }}
                </button>
            </form>
        </div>

        <div
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left w-12">
                            <input type="checkbox" id="select-all"
                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.mrn') }}</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.name') }}
                        </th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.age') }}</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.gender') }}</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.phone') }}</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.last_visit') }}</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.status') }}
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
            </table>
        </div>

        <div id="filter-drawer" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black/50" id="drawer-backdrop"></div>
            <div class="fixed inset-y-0 right-0 w-full max-w-md bg-white dark:bg-gray-800 shadow-xl transform translate-x-full transition-transform duration-300 ease-in-out"
                id="drawer-panel">
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.Filters') }}</h3>
                    <button type="button" id="close-drawer"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto h-full pb-32">
                    <div class="space-y-6">
                        <div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.gender') }}</label>
                                    <select id="filter-gender"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                        <option value="">{{ __('file.all_genders') }}</option>
                                        <option value="male">{{ __('file.male') }}</option>
                                        <option value="female">{{ __('file.female') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                                    <select id="filter-status"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                        <option value="">{{ __('file.all_statuses') }}</option>
                                        <option value="1">{{ __('file.active') }}</option>
                                        <option value="0">{{ __('file.inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wider">
                                {{ __('file.age_range') }}
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.age_from') }}</label>
                                    <input type="number" id="filter-age-from" min="0" max="120" placeholder="0"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.age_to') }}</label>
                                    <input type="number" id="filter-age-to" min="0" max="120" placeholder="120"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wider">
                                {{ __('file.last_visit_date') }}
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.last_visit_from') }}</label>
                                    <input type="date" id="filter-from"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.last_visit_to') }}</label>
                                    <input type="date" id="filter-to"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="fixed bottom-0 left-0 right-0 p-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 max-w-md ml-auto">
                        <div class="flex gap-3">
                            <button type="button" id="clear-filters"
                                class="flex-1 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                {{ __('file.clear') }}
                            </button>
                            <button type="button" id="apply-filters"
                                class="flex-1 px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition shadow-sm">
                                {{ __('file.apply') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Import Modal --}}
        <div id="import-modal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-black/50" onclick="document.getElementById('import-modal').classList.add('hidden');"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-700">
                    <form action="{{ route('patients.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="px-4 pt-5 pb-4 bg-white dark:bg-gray-800 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white" id="modal-title">
                                        {{ __('file.import_patients') }}
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ __('file.accepted_formats') }}: .xlsx, .xls, .csv ({{ __('file.max_size') }} 2MB).
                                            </p>
                                            <div class="mt-2">
                                                <a href="{{ route('patients.import-template') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 underline flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M7 10l5 5m0 0l5-5m-5 5V3" />
                                                    </svg>
                                                    {{ __('file.download_template') }}
                                                </a>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ __('file.select_file') }}
                                            </label>
                                            <input type="file" name="file" required
                                                class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-300">
                                        </div>

                                        @if(session('validation_errors'))
                                            <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg max-h-40 overflow-y-auto">
                                                <ul class="list-disc list-inside text-xs text-red-600 dark:text-red-400 space-y-1">
                                                    @foreach(session('validation_errors') as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/50 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit"
                                class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition">
                                {{ __('file.import') }}
                            </button>
                            <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden');"
                                class="mt-3 inline-flex justify-center w-full px-4 py-2 text-base font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                {{ __('file.cancel') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const filterButton = document.getElementById('filter-button');
                const filterDrawer = document.getElementById('filter-drawer');
                const drawerBackdrop = document.getElementById('drawer-backdrop');
                const drawerPanel = document.getElementById('drawer-panel');
                const closeDrawer = document.getElementById('close-drawer');
                const filterCount = document.getElementById('filter-count');

                filterButton.addEventListener('click', function () {
                    filterDrawer.classList.remove('hidden');
                    setTimeout(() => drawerPanel.classList.remove('translate-x-full'), 10);
                });

                function closeDrawerHandler() {
                    drawerPanel.classList.add('translate-x-full');
                    setTimeout(() => filterDrawer.classList.add('hidden'), 300);
                }

                closeDrawer.addEventListener('click', closeDrawerHandler);
                drawerBackdrop.addEventListener('click', closeDrawerHandler);

                function updateFilterCount() {
                    const filters = [
                        $('#filter-gender').val(),
                        $('#filter-status').val(),
                        $('#filter-age-from').val(),
                        $('#filter-age-to').val(),
                        $('#filter-from').val(),
                        $('#filter-to').val()
                    ];
                    const activeCount = filters.filter(f => f !== '' && f !== null).length;
                    if (activeCount > 0) {
                        filterCount.textContent = activeCount;
                        filterCount.classList.remove('hidden');
                    } else {
                        filterCount.classList.add('hidden');
                    }
                }

                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: {
                        url: '{{ route('patients.datatable') }}',
                        data: function (d) {
                            d.gender = $('#filter-gender').val();
                            d.status = $('#filter-status').val();
                            d.age_from = $('#filter-age-from').val();
                            d.age_to = $('#filter-age-to').val();
                            d.from = $('#filter-from').val();
                            d.to = $('#filter-to').val();
                        }
                    },
                    columnDefs: [
                        { orderable: false, targets: [0, 5, 8] },
                        { searchable: false, targets: [0, 3, 6, 8] }
                    ],
                    columns: [
                        {
                            data: 'id',
                            render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`,
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        { data: 'medical_record_number' },
                        { data: 'full_name', render: data => data || '-' },
                        {
                            data: 'age',
                            className: 'text-center font-medium',
                            render: data => data > 0 ? data : '-'
                        },
                        { data: 'gender' },
                        { data: 'phone' },
                        {
                            data: 'last_visit',
                            render: data => data ? data : '<span class="text-gray-400 text-xs">{{ __("file.never") }}</span>'
                        },
                        { data: 'status_html' },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-right whitespace-nowrap',
                            render: (data, type, row) => `
                                                                            <div class="flex items-center justify-end gap-1">
                                                                                <a href="${row.show_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="{{ __('file.view_profile') }}">
                                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                                    </svg>
                                                                                </a>

                                                                                ${row.edit_url ? `
                                                                                    <a href="${row.edit_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit">
                                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                                        </svg>
                                                                                    </a>
                                                                                ` : `
                                                                                    <span class="p-2 text-gray-400 dark:text-gray-500 cursor-not-allowed opacity-60" title="Cannot edit inactive or deleted patient">
                                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                                        </svg>
                                                                                    </span>
                                                                                `}

                                                                                ${row.delete_url ? `
                                                                                    <button type="button" onclick="confirmDelete('${row.delete_url}')"
                                                                                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="Delete">
                                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                                        </svg>
                                                                                    </button>
                                                                                ` : ''}
                                                                            </div>
                                                                        `
                        }
                    ],
                    layout: {
                        topStart: {
                            buttons: [
                                {
                                    extend: 'pageLength',
                                    className: 'inline-flex items-center gap-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium transition shadow-sm'
                                },
                                {
                                    extend: 'collection',
                                    className: 'bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2',
                                    text: "{{ __('file.Export') }}",
                                    buttons: [
                                        { extend: 'copy', text: "{{ __('file.copy') }}" },
                                        { extend: 'excel', text: 'Excel', filename: 'Patients_{{ date("Y-m-d") }}' },
                                        { extend: 'csv', text: 'CSV', filename: 'Patients_{{ date("Y-m-d") }}' },
                                        { extend: 'pdf', text: 'PDF', filename: 'Patients_{{ date("Y-m-d") }}', title: 'Patient List' },
                                        { extend: 'print', text: "{{ __('file.print') }}" }
                                    ]
                                }
                            ]
                        },
                        topEnd: 'search',
                        bottomStart: 'info',
                        bottomEnd: 'paging'
                    },
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    language: {
                        search: "",
                        searchPlaceholder: "{{ __('file.search_patients') }}",
                        lengthMenu: "{{ __('file.show_entries') }}",
                        info: "{{ __('file.showing_entries_patients') }}",
                        emptyTable: "{{ __('file.no_patients_found') }}",
                        processing: "{{ __('file.processing') }}"
                    }
                });

                $('#apply-filters').on('click', function () {
                    table.draw();
                    closeDrawerHandler();
                    updateFilterCount();
                });

                $('#clear-filters').on('click', function () {
                    $('#filter-gender, #filter-status, #filter-age-from, #filter-age-to, #filter-from, #filter-to').val('');
                    table.draw();
                    updateFilterCount();
                });

                $('#filter-gender, #filter-status, #filter-age-from, #filter-age-to, #filter-from, #filter-to').on('change', updateFilterCount);

                $('#filter-gender, #filter-status, #filter-age-from, #filter-age-to, #filter-from, #filter-to').on('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        $('#apply-filters').trigger('click');
                    }
                });

                updateFilterCount();

                $('#select-all').on('change', function () {
                    $('.row-checkbox').prop('checked', this.checked);
                    updateBulkDelete();
                });

                $(document).on('change', '.row-checkbox', updateBulkDelete);

                function updateBulkDelete() {
                    const checked = $('.row-checkbox:checked');
                    const count = checked.length;
                    $('#bulk-delete-form').toggleClass('hidden', count === 0);
                    $('#selected-count').text(count);
                    
                    const container = document.getElementById('bulk-ids-container');
                    container.innerHTML = '';
                    checked.each(function () {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = this.value;
                        container.appendChild(input);
                    });
                }

                $('#bulk-delete-form-el').on('submit', function (e) {
                    e.preventDefault();
                    if (!confirm('{{ __("file.confirm_delete_selected_items") }}')) return;
                    
                    $.ajax({
                        url: this.action,
                        method: 'POST',
                        data: $(this).serialize(),
                        success: (response) => {
                            table.draw(false);
                            $('.row-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            updateBulkDelete();
                            if (typeof showNotification === 'function') showNotification('Success', response.message || 'Items deleted successfully.', 'success');
                        },
                        error: (xhr) => {
                            const msg = xhr.responseJSON?.message || 'Delete failed.';
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                        }
                    });
                });

                window.confirmDelete = function (url) {
                    if (!confirm('{{ __("file.confirm_delete_patient") }}')) return;
                    
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            table.draw(false);
                            if (response.success) {
                                if (typeof showNotification === 'function') showNotification('Success', response.message, 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', response.message, 'error');
                            }
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON?.message || 'Delete failed.';
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                        }
                    });
                };
            });
        </script>
    @endpush
@endsection