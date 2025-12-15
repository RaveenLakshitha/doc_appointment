@extends('layouts.app')

@section('title', __('file.patients'))

@section('content')
<div class="px-4 sm:px-6 lg:px-4 py-4 sm:py-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                {{ __('file.patients') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('file.manage_patient_records') }}
            </p>
        </div>

        <div class="flex flex-row-reverse sm:flex-row gap-3 w-full sm:w-auto justify-between sm:justify-end">
            <div class="relative">
                <button type="button" id="filter-button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-600 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span id="filter-count" class="hidden ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200"></span>
                </button>

            <div id="filter-popover" class="hidden absolute z-50 mt-2 left-1 -translate-x-1/2 
                w-[98vw] max-w-sm sm:max-w-md md:max-w-lg 
                max-h-[88vh] overflow-y-auto
                bg-white dark:bg-gray-800 
                rounded-xl shadow-2xl 
                border border-gray-200 dark:border-gray-700
                origin-top">
                <div class="p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.Filters') }}</h3>
                            <button type="button" id="close-popover" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Section 1: Basic Info -->
                        <div class="mb-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.gender') }}</label>
                                    <select id="filter-gender" class="w-full px-1 py-2.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                                        <option value="">{{ __('file.all_genders') }}</option>
                                        <option value="male">{{ __('file.male') }}</option>
                                        <option value="female">{{ __('file.female') }}</option>
                                        <option value="other">{{ __('file.other') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.status') }}</label>
                                    <select id="filter-status" class="w-full px-1 py-2.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                                        <option value="">{{ __('file.all_statuses') }}</option>
                                        <option value="1">{{ __('file.active') }}</option>
                                        <option value="0">{{ __('file.inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Age Range -->
                        <div class="mb-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 uppercase tracking-wider">{{ __('file.age_range') }}</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.age_from') }}</label>
                                    <input type="number" id="filter-age-from" min="0" max="120" placeholder="0" class="w-full px-4 py-2.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.age_to') }}</label>
                                    <input type="number" id="filter-age-to" min="0" max="120" placeholder="120" class="w-full px-4 py-2.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Last Visit Date -->
                        <div class="mb-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 uppercase tracking-wider">{{ __('file.last_visit_date') }}</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.last_visit_from') }}</label>
                                    <input type="date" id="filter-from" class="w-full px-1 py-2.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.last_visit_to') }}</label>
                                    <input type="date" id="filter-to" class="w-full px-1 py-2.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" id="clear-filters"
                                    class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                {{ __('file.clear') }}
                            </button>
                            <button type="button" id="apply-filters"
                                    class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition shadow-sm">
                                {{ __('file.apply') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <a href="{{ route('patients.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('file.add_patient') }}
            </a>
        </div>
    </div>

    <div id="bulk-delete-form" class="hidden mb-6">
        <form method="POST" action="{{ route('patients.bulkDelete') }}" class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
            @csrf @method('DELETE')
            <input type="hidden" name="ids" id="bulk-ids">
            <span class="text-sm font-medium text-red-800 dark:text-red-300">
                <span id="selected-count">0</span> {{ __('file.patient_selected') }}
            </span>
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                {{ __('file.delete_selected') }}
            </button>
        </form>
    </div>

    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div id="table-loading" class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm z-10 flex items-center justify-center hidden">
            <div class="flex flex-col items-center gap-3">
                <div class="flex space-x-2"></div>
            </div>
        </div>
        
        <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left w-12">
                        <input type="checkbox" id="select-all" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">MRN</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.name') }}</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Age</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gender</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Visit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterButton = document.getElementById('filter-button');
    const filterPopover = document.getElementById('filter-popover');
    const closePopover = document.getElementById('close-popover');
    const filterCount = document.getElementById('filter-count');

    filterButton.addEventListener('click', function(e) {
        e.stopPropagation();
        filterPopover.classList.toggle('hidden');
    });

    closePopover.addEventListener('click', function() {
        filterPopover.classList.add('hidden');
    });

    document.addEventListener('click', function(e) {
        if (!filterPopover.contains(e.target) && !filterButton.contains(e.target)) {
            filterPopover.classList.add('hidden');
        }
    });

    function updateFilterCount() {
        const filters = [
            $('#filter-gender').val(),
            $('#filter-status').val(),
            $('#filter-age-from').val(),
            $('#filter-age-to').val(),
            $('#filter-from').val(),
            $('#filter-to').val()
        ];
        const activeCount = filters.filter(f => f !== '').length;
        if (activeCount > 0) {
            filterCount.textContent = activeCount;
            filterCount.classList.remove('hidden');
        } else {
            filterCount.classList.add('hidden');
        }
    }

    function showLoading() { document.getElementById('table-loading').classList.remove('hidden'); }
    function hideLoading() { document.getElementById('table-loading').classList.add('hidden'); }

    const table = $('#docapp-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '{{ route('patients.datatable') }}',
            data: function (d) {
                d.gender     = $('#filter-gender').val();
                d.status     = $('#filter-status').val();
                d.age_from   = $('#filter-age-from').val();
                d.age_to     = $('#filter-age-to').val();
                d.from       = $('#filter-from').val();
                d.to         = $('#filter-to').val();
            },
            beforeSend: showLoading,
            complete: hideLoading,
            error: hideLoading
        },
        order: [[2, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 7] },
            { searchable: false, targets: [0, 3, 5, 7] }
        ],
        columns: [
            { data: 'id', render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`, orderable: false, searchable: false, className: 'text-center' },
            { data: 'medical_record_number' },
            { data: 'full_name', render: data => data || '-' },
            { data: 'age', className: 'text-center font-medium', render: data => data > 0 ? data : '-' },
            { data: 'gender' },
            { data: 'last_visit', render: data => data ? data : '<span class="text-gray-400 text-xs">Never</span>' },
            { data: 'status_html' },
            { data: null, orderable: false, searchable: false, className: 'text-right whitespace-nowrap', render: (data, type, row) => `
                <div class="flex items-center justify-end gap-1">
                    <a href="${row.show_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <a href="${row.edit_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <form method="POST" action="${row.delete_url}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('{{ __("file.confirm_delete_patient") }}')"
                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>`
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
                            { extend: 'copy', text: "{{ __('file.copy') }}"},
                            { extend: 'excel', text: 'Excel', filename: 'Patients_{{ date("Y-m-d") }}' },
                            { extend: 'csv', text: 'CSV', filename: 'Patients_{{ date("Y-m-d") }}' },
                            { extend: 'pdf', text: 'PDF', filename: 'Patients_{{ date("Y-m-d") }}', title: 'Patient List' },
                            { extend: 'print', text: "{{ __('file.print') }}" }
                        ]
                    }
                ],
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
            info: "{{ __('file.showing_entries') }}",
            emptyTable: "{{ __('file.no_patients_found') }}",
            processing: "{{ __('file.processing') }}"
        }
    });

    $('#apply-filters').on('click', function() {
        table.draw();
        filterPopover.classList.add('hidden');
        updateFilterCount();
    });

    $('#clear-filters').on('click', function () {
        $('#filter-gender, #filter-status, #filter-age-from, #filter-age-to, #filter-from, #filter-to').val('');
        table.draw();
        filterPopover.classList.add('hidden');
        updateFilterCount();
    });

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
        const count = $('.row-checkbox:checked').length;
        $('#bulk-delete-form').toggleClass('hidden', count === 0);
        $('#selected-count').text(count);
        $('#bulk-ids').val($('.row-checkbox:checked').map(function() { return this.value; }).get().join(','));
    }

    $('#bulk-delete-form form').on('submit', function (e) {
        e.preventDefault();
        if (confirm('{{ __("file.confirm_delete_selected") }}')) {
            $.ajax({
                url: this.action,
                method: 'POST',
                data: $(this).serialize(),
                success: () => {
                    table.draw(false);
                    updateBulkDelete();
                }
            });
        }
    });
});
</script>
@endpush
@endsection