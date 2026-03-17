@extends('layouts.app')

@section('title', __('file.prescriptions'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.prescriptions') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_all_patient_prescriptions') }}
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

                <a href="{{ route('prescriptions.create', ['from' => 'all-prescriptions']) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('file.add_prescription') }}
                </a>
            </div>
        </div>

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('prescriptions.bulkDelete') }}" id="bulk-delete-form-el"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
                <div id="bulk-ids-container"></div>
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.prescription_selected') }}
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
                            {{ __('file.date') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.patient') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.type') }}
                        </th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.medications') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.doctor') }}
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
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.type') }}</label>
                            <select id="filter-type"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="">{{ __('file.all_types') }}</option>
                                <option value="Standard">{{ __('file.standard') }}</option>
                                <option value="Emergency">{{ __('file.emergency') }}</option>
                                <option value="Chronic">{{ __('file.chronic') }}</option>
                                <option value="Follow-up">{{ __('file.follow_up') }}</option>
                            </select>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wider">
                                {{ __('file.prescription_date') }}
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.from') }}</label>
                                    <input type="date" id="filter-from"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.to') }}</label>
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
                        $('#filter-type').val(),
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
                        url: '{{ route('prescriptions.datatable') }}',
                        data: function (d) {
                            d.type = $('#filter-type').val();
                            d.from = $('#filter-from').val();
                            d.to = $('#filter-to').val();
                        }
                    },
                    order: [[1, 'desc']],
                    columnDefs: [
                        { orderable: false, targets: [0, 6] },
                        { searchable: false, targets: [0, 4, 6] }
                    ],
                    columns: [
                        {
                            data: 'id',
                            render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`,
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        { data: 'prescription_date' },
                        { data: 'patient_name' },
                        { data: 'type' },
                        {
                            data: 'medications_count',
                            className: 'text-center',
                            render: data => `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300">${data}</span>`
                        },
                        { data: 'doctor_name', render: data => data || '-' },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-right whitespace-nowrap',
                            render: (data, type, row) => `
                                                    <div class="flex items-center justify-end gap-1">
                                                        <a href="${row.show_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="{{ __('file.view') }}">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        </a>
                                                        <a href="${row.print_url || '#'}" class="p-2 ${row.print_url ? 'text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' : 'text-gray-400 dark:text-gray-600 opacity-50 cursor-not-allowed'} transition-colors" ${!row.print_url ? 'disabled' : ''} title="{{ __('file.print') }}">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                        </a>
                                                        <a href="${row.edit_url || '#'}" class="p-2 ${row.edit_url ? 'text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400' : 'text-gray-400 dark:text-gray-600 opacity-50 cursor-not-allowed'} transition-colors" ${!row.edit_url ? 'disabled' : ''} title="{{ __('file.edit') }}">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        </a>
                                                        ${row.delete_url ? `
                                                            <button type="button" onclick="confirmDelete('${row.delete_url}')" class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="{{ __('file.delete') }}">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            </button>
                                                        ` : ''}
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
                                        { extend: 'copy', text: "{{ __('file.copy') }}" },
                                        { extend: 'excel', text: 'Excel', filename: 'Prescriptions_{{ date("Y-m-d") }}' },
                                        { extend: 'csv', text: 'CSV', filename: 'Prescriptions_{{ date("Y-m-d") }}' },
                                        { extend: 'pdf', text: 'PDF', filename: 'Prescriptions_{{ date("Y-m-d") }}', title: 'Prescription List' },
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
                        searchPlaceholder: "{{ __('file.search_prescriptions') }}",
                        lengthMenu: "{{ __('file.show_entries') }}",
                        info: "{{ __('file.showing_entries') }}",
                        emptyTable: "{{ __('file.no_prescriptions_found') }}",
                        processing: "{{ __('file.processing') }}"
                    }
                });

                $('#apply-filters').on('click', function () {
                    table.draw();
                    closeDrawerHandler();
                    updateFilterCount();
                });

                $('#clear-filters').on('click', function () {
                    $('#filter-type, #filter-from, #filter-to').val('');
                    table.draw();
                    updateFilterCount();
                });

                $('#filter-type, #filter-from, #filter-to').on('change', updateFilterCount);

                $('#filter-type, #filter-from, #filter-to').on('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        $('#apply-filters').trigger('click');
                    }
                });

                updateFilterCount();

                $('#select-all').on('change', function () {
                    $('.row-checkbox').prop('checked', this.checked);
                    updateBulkDeleteUI();
                });

                $(document).on('change', '.row-checkbox', updateBulkDeleteUI);

                function updateBulkDeleteUI() {
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
                    if (!confirm('{{ __('file.confirm_delete_selected_items') }}')) return;

                    $.ajax({
                        url: this.action,
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function (response) {
                            table.draw(false);
                            $('.row-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            updateBulkDeleteUI();
                            if (response.success) {
                                if (typeof showNotification === 'function') showNotification('Success', response.message || 'Items deleted successfully.', 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', response.message || 'Error deleting items.', 'error');
                            }
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON?.message || 'Error deleting selected items.';
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                        }
                    });
                });

                window.confirmDelete = function (url) {
                    if (!confirm('{{ __('file.confirm_delete_item') }}')) return;

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