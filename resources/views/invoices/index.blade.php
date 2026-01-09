@extends('layouts.app')

@section('title', __('file.invoices'))

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                {{ __('file.invoices') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('file.manage_invoices') }}
            </p>
        </div>

        <div class="flex flex-row-reverse sm:flex-row gap-3 w-full sm:w-auto justify-between sm:justify-end">
            <button type="button" id="filter-button"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-600 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                {{ __('file.Filters') }}
                <span id="filter-count" class="hidden ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200"></span>
            </button>

            <a href="{{ route('invoices.pos') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('file.new_pos_sale') }}
            </a>
        </div>
    </div>

    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.invoice_number') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.patient') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.invoice_date') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.total') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.balance_due') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
        </table>
    </div>

    <div id="filter-drawer" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50" id="drawer-backdrop"></div>
        <div class="fixed inset-y-0 right-0 w-full max-w-md bg-white dark:bg-gray-800 shadow-xl transform translate-x-full transition-transform duration-300 ease-in-out" id="drawer-panel">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.Filters') }}</h3>
                <button type="button" id="close-drawer" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto h-full pb-32">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.status') }}</label>
                        <select id="filter-status" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                            <option value="">{{ __('file.all_statuses') }}</option>
                            <option value="paid">{{ __('file.paid') }}</option>
                            <option value="partially_paid">{{ __('file.partially_paid') }}</option>
                            <option value="sent">{{ __('file.sent') }}</option>
                            <option value="overdue">{{ __('file.overdue') }}</option>
                        </select>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wider">{{ __('file.invoice_date_range') }}</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.from') }}</label>
                                <input type="date" id="filter-from" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.to') }}</label>
                                <input type="date" id="filter-to" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fixed bottom-0 left-0 right-0 p-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 max-w-md ml-auto">
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

    filterButton.addEventListener('click', function() {
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
            $('#filter-status').val(),
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
        responsive: true,
        ajax: {
            url: '{{ route('invoices.datatable') }}',
            data: function (d) {
                d.status = $('#filter-status').val();
                d.from   = $('#filter-from').val();
                d.to     = $('#filter-to').val();
            }
        },
        columnDefs: [
            { orderable: false, targets: [6] },
            { searchable: false, targets: [3, 4, 5, 6] }
        ],
        columns: [
            { data: 'invoice_number' },
            { data: 'patient_name', render: data => data || '-' },
            { data: 'invoice_date' },
            { data: 'total', className: 'text-right font-medium' },
            { data: 'balance_due', className: 'text-right font-medium' },
            { data: 'status_html' },
            { data: null, orderable: false, searchable: false, className: 'text-right whitespace-nowrap', render: (data, type, row) => `
                <div class="flex items-center justify-end gap-1">
                    <a href="${row.show_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <a href="${row.print_url}" target="_blank" class="p-2 text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </a>
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
                            { extend: 'excel', text: 'Excel', filename: 'Invoices_{{ date("Y-m-d") }}' },
                            { extend: 'csv', text: 'CSV', filename: 'Invoices_{{ date("Y-m-d") }}' },
                            { extend: 'pdf', text: 'PDF', filename: 'Invoices_{{ date("Y-m-d") }}', title: 'Invoice List' },
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
            searchPlaceholder: "{{ __('file.search_invoices') }}",
            lengthMenu: "{{ __('file.show_entries') }}",
            info: "{{ __('file.showing_entries_invoices') }}",
            emptyTable: "{{ __('file.no_invoices_found') }}",
            processing: "{{ __('file.processing') }}"
        }
    });

    $('#apply-filters').on('click', function() {
        table.draw();
        closeDrawerHandler();
        updateFilterCount();
    });

    $('#clear-filters').on('click', function () {
        $('#filter-status, #filter-from, #filter-to').val('');
        table.draw();
        updateFilterCount();
    });

    $('#filter-status, #filter-from, #filter-to').on('change', updateFilterCount);

    $('#filter-status, #filter-from, #filter-to').on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $('#apply-filters').trigger('click');
        }
    });

    updateFilterCount();
});
</script>
@endpush
@endsection