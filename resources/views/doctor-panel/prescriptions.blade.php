@extends('layouts.app')

@section('title', __('file.prescriptions'))

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                My Prescriptions
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                View and manage prescriptions you have issued to patients
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

            <!-- No "Add Prescription" button – doctors create via appointment flow -->
        </div>
    </div>

    <!-- Bulk delete form – using your existing shared route -->
    <div id="bulk-delete-form" class="hidden mb-6">
        <form method="POST" action="{{ route('prescriptions.bulkDelete') }}" class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
            @csrf @method('DELETE')
            <input type="hidden" name="ids" id="bulk-ids">
            <span class="text-sm font-medium text-red-800 dark:text-red-300">
                <span id="selected-count">0</span> {{ __('file.prescription_selected') }}
            </span>
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                {{ __('file.delete_selected') }}
            </button>
        </form>
    </div>

    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left w-12">
                        <input type="checkbox" id="select-all" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.patient') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.diagnosis') }}</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.medications') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
        </table>
    </div>

    <!-- Filter Drawer – identical to admin version -->
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.type') }}</label>
                        <select id="filter-type" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                            <option value="">{{ __('file.all_types') }}</option>
                            <option value="Standard">{{ __('file.standard') }}</option>
                            <option value="Emergency">{{ __('file.emergency') }}</option>
                        </select>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wider">{{ __('file.prescription_date') }}</h4>
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
        responsive: true,
        ajax: {
            url: '{{ route('prescriptions.datatable') }}',
            data: function (d) {
                d.type = $('#filter-type').val();
                d.from = $('#filter-from').val();
                d.to   = $('#filter-to').val();
            }
        },
        order: [[1, 'desc']],
        columnDefs: [
            { orderable: false, targets: [0, 6] },
            { searchable: false, targets: [0, 5, 6] }
        ],
        columns: [
            { data: 'id', render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`, orderable: false, searchable: false, className: 'text-center' },
            { data: 'prescription_date' },
            { data: 'patient_name' },
            { data: 'type' },
            { data: 'diagnosis', render: data => data || '-' },
            { data: 'medications_count', className: 'text-center', render: data => `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300">${data}</span>` },
            { data: null, orderable: false, searchable: false, className: 'text-right whitespace-nowrap', render: (data, type, row) => `
                <div class="flex items-center justify-end gap-1">
                    <a href="${row.show_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <!-- Optional: add edit/delete if you want doctors to modify their own -->
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
                            { extend: 'excel', text: 'Excel', filename: 'My_Prescriptions_{{ date("Y-m-d") }}' },
                            { extend: 'csv', text: 'CSV', filename: 'My_Prescriptions_{{ date("Y-m-d") }}' },
                            { extend: 'pdf', text: 'PDF', filename: 'My_Prescriptions_{{ date("Y-m-d") }}', title: 'My Prescriptions' },
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
            emptyTable: "No prescriptions found",
            processing: "{{ __('file.processing') }}"
        }
    });

    $('#apply-filters').on('click', function() {
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
        if (confirm('{{ __('file.confirm_delete_selected') }}')) {
            $.ajax({
                url: this.action,
                method: 'POST',
                data: $(this).serialize(),
                success: () => {
                    table.draw(false);
                    updateBulkDelete();
                },
                error: (xhr) => {
                    alert('Error deleting prescriptions: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        }
    });
});
</script>
@endpush
@endsection