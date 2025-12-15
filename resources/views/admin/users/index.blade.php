{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', __('file.user_management'))

@section('content')
<div class="px-4 sm:px-6 lg:px-4 py-4 sm:py-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                {{ __('file.user_management') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('file.manage_system_users') }}
            </p>
        </div>

        <div class="flex flex-row-reverse sm:flex-row gap-3 w-full sm:w-auto justify-between sm:justify-end">
            <div class="relative">
                <button type="button" id="filter-button"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-600 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    {{ __('file.filter') }}
                    <span id="filter-count" class="hidden ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200"></span>
                </button>

                <div id="filter-popover" class="hidden absolute z-50 mt-2 right-0 w-[95vw] max-w-md origin-top bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.Filters') }}</h3>
                            <button type="button" id="close-popover" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.role') }}</label>
                                <select id="filter-role" class="w-full px-3 py-2.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                                    <option value="">{{ __('file.all_roles') }}</option>
                                    @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.status') }}</label>
                                <select id="filter-status" class="w-full px-3 py-2.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                                    <option value="">{{ __('file.all_statuses') }}</option>
                                    <option value="1">{{ __('file.active') }}</option>
                                    <option value="0">{{ __('file.inactive') }}</option>
                                </select>
                            </div>

                           

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.created_from') }}</label>
                                    <input type="date" id="filter-from" class="w-full px-3 py-2.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.created_to') }}</label>
                                    <input type="date" id="filter-to" class="w-full px-3 py-2.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" id="clear-filters" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                {{ __('file.clear') }}
                            </button>
                            <button type="button" id="apply-filters" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition shadow-sm">
                                {{ __('file.apply') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('file.add_new_user') }}
            </a>
        </div>
    </div>

    <div id="bulk-delete-form" class="hidden mb-6">
        <form method="POST" action="{{ route('admin.users.bulkDelete') }}" class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
            @csrf @method('DELETE')
            <input type="hidden" name="ids" id="bulk-ids">
            <span class="text-sm font-medium text-red-800 dark:text-red-300">
                <span id="selected-count">0</span> {{ __('file.users_selected') }}
            </span>
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                {{ __('file.delete_selected') }}
            </button>
        </form>
    </div>

    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div id="table-loading" class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm z-10 flex items-center justify-center hidden">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
        </div>

        <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left w-12">
                        <input type="checkbox" id="select-all" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.email') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.phone') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.roles') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.created_at') }}</th>
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

    filterButton.addEventListener('click', e => { e.stopPropagation(); filterPopover.classList.toggle('hidden'); });
    closePopover.addEventListener('click', () => filterPopover.classList.add('hidden'));
    document.addEventListener('click', e => {
        if (!filterPopover.contains(e.target) && !filterButton.contains(e.target)) {
            filterPopover.classList.add('hidden');
        }
    });

    function updateFilterCount() {
        const count = [
            $('#filter-role').val(),
            $('#filter-status').val(),
            $('#filter-from').val(),
            $('#filter-to').val()
        ].filter(v => v !== '').length;
        if (count > 0) {
            filterCount.textContent = count;
            filterCount.classList.remove('hidden');
        } else {
            filterCount.classList.add('hidden');
        }
    }

    function showLoading() { $('#table-loading').removeClass('hidden'); }
    function hideLoading() { $('#table-loading').addClass('hidden'); }

    const table = $('#docapp-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '{{ route('admin.users.datatable') }}',
            data: d => {
                d.role   = $('#filter-role').val();
                d.status = $('#filter-status').val();
                d.from   = $('#filter-from').val();
                d.to     = $('#filter-to').val();
            },
            beforeSend: showLoading,
            complete: hideLoading,
            error: hideLoading
        },
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 7] },
            { searchable: false, targets: [0, 7] }
        ],
        columns: [
            { data: 'id', render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`, className: 'text-center' },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone', render: data => data || '-' },
            { data: 'roles', render: data => data.map(r => `<span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 mr-1">${r}</span>`).join('') || '-' },
            { data: 'status_html' },
            { data: 'created_at' },
            { data: null, className: 'text-right whitespace-nowrap', render: (data, type, row) => `
                <div class="flex items-center justify-end gap-1">
                    <a href="${row.edit_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <form method="POST" action="${row.delete_url}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('{{ __('file.soft_delete_confirm') }}')" class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>`
            }
        ],
        layout: {
            topStart: { buttons: [ 'pageLength', { extend: 'collection', text: '{{ __("file.Export") }}', buttons: [ 'copy', 'excel', 'csv', 'pdf', 'print' ] } ] },
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        language: {
            search: "",
            searchPlaceholder: "{{ __('file.search_users') }}",
            lengthMenu: "{{ __('file.show_entries') }}",
            info: "{{ __('file.showing_entries') }}",
            emptyTable: "{{ __('file.no_users_found') }}",
            processing: "{{ __('file.processing') }}"
        }
    });

    $('#apply-filters').on('click', () => { table.draw(); filterPopover.classList.add('hidden'); updateFilterCount(); });
    $('#clear-filters').on('click', () => { $('#filter-role,#filter-status,#filter-from,#filter-to').val(''); table.draw(); filterPopover.classList.add('hidden'); updateFilterCount(); });

    $('#select-all').on('change', function () {
        $('.row-checkbox').prop('checked', this.checked);
        updateBulk();
    });

    $(document).on('change', '.row-checkbox', updateBulk);

    function updateBulk() {
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
                success: () => { table.draw(false); updateBulk(); }
            });
        }
    });

    updateFilterCount();
});
</script>
@endpush
@endsection