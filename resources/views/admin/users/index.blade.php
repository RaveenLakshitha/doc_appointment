@extends('layouts.app')

@section('title', __('file.user_management'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.user_management') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_system_users') }}
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

                <a href="{{ route('users.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('file.add_new_user') }}
                </a>
            </div>
        </div>

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('users.bulkDelete') }}"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.users_selected') }}
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
                            {{ __('file.name') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.email') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.phone') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.roles') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.status') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.created_at') }}
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
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.role') }}</label>
                            <select id="filter-role"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="">{{ __('file.all_roles') }}</option>
                                @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.status') }}</label>
                            <select id="filter-status"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="">{{ __('file.all_statuses') }}</option>
                                <option value="1">{{ __('file.active') }}</option>
                                <option value="0">{{ __('file.inactive') }}</option>
                            </select>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wider">
                                {{ __('file.created_date') }}
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.from') }}</label>
                                    <input type="date" id="filter-from"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.to') }}</label>
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
                        $('#filter-role').val(),
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
                    responsive: false,
                    ajax: {
                        url: '{{ route('users.datatable') }}',
                        data: function (d) {
                            d.role = $('#filter-role').val();
                            d.status = $('#filter-status').val();
                            d.from = $('#filter-from').val();
                            d.to = $('#filter-to').val();
                        }
                    },
                    order: [[1, 'asc']],
                    columnDefs: [
                        { orderable: false, targets: [0, 7] },
                        { searchable: false, targets: [0, 7] }
                    ],
                    columns: [
                        {
                            data: 'id',
                            render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`,
                            className: 'text-center'
                        },
                        { data: 'name' },
                        { data: 'email' },
                        { data: 'phone', render: data => data || '-' },
                        {
                            data: 'roles',
                            render: data => data.map(r => `<span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 mr-1">${r}</span>`).join('') || '-'
                        },
                        { data: 'status_html' },
                        { data: 'created_at' },
                        {
                            data: null,
                            className: 'text-right whitespace-nowrap',
                            render: (data, type, row) => `
                                                    <div class="flex items-center justify-end gap-1">
                                                        ${row.edit_url ? `<a href="${row.edit_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </a>` : ''}
                                                        ${row.delete_url ? `
                                                            <button type="button" onclick="deleteUser(${row.id}, '${row.name.replace(/'/g, "\\'")}')"
                                                                    class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
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
                                        { extend: 'copy', text: "{{ __('file.copy') }}", exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
                                        { extend: 'excel', text: 'Excel', filename: 'Users_{{ date("Y-m-d") }}', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
                                        { extend: 'csv', text: 'CSV', filename: 'Users_{{ date("Y-m-d") }}', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
                                        { extend: 'pdf', text: 'PDF', filename: 'Users_{{ date("Y-m-d") }}', title: 'Users List', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
                                        { extend: 'print', text: "{{ __('file.print') }}", exportOptions: { columns: [0, 1, 2, 3, 4, 5] } }
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
                        searchPlaceholder: "{{ __('file.search_users') }}",
                        lengthMenu: "{{ __('file.show_entries') }}",
                        info: "{{ __('file.showing_entries') }}",
                        emptyTable: "{{ __('file.no_users_found') }}",
                        processing: "{{ __('file.processing') }}"
                    }
                });

                $('#apply-filters').on('click', function () {
                    table.draw();
                    closeDrawerHandler();
                    updateFilterCount();
                });

                $('#clear-filters').on('click', function () {
                    $('#filter-role, #filter-status, #filter-from, #filter-to').val('');
                    table.draw();
                    updateFilterCount();
                });

                $('#filter-role, #filter-status, #filter-from, #filter-to').on('change', updateFilterCount);

                $('#filter-role, #filter-status, #filter-from, #filter-to').on('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        $('#apply-filters').trigger('click');
                    }
                });

                updateFilterCount();

                function updateBulkDelete() {
                    const checked = $('.row-checkbox:checked');
                    const count = checked.length;

                    $('#bulk-delete-form').toggleClass('hidden', count === 0);
                    $('#selected-count').text(count);

                    $('#bulk-delete-form input[name="ids[]"]').remove();

                    const form = $('#bulk-delete-form form');
                    checked.each(function () {
                        form.append(`<input type="hidden" name="ids[]" value="${this.value}">`);
                    });
                }

                $('#select-all').on('change', function () {
                    $('.row-checkbox').prop('checked', this.checked);
                    updateBulkDelete();
                });

                $(document).on('change', '.row-checkbox', updateBulkDelete);

                $('#bulk-delete-form form').on('submit', function (e) {
                    e.preventDefault();

                    if (!confirm('{{ __('file.confirm_delete_selected') }}')) {
                        return;
                    }

                    const form = $(this);

                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.success) {
                                $('.row-checkbox').prop('checked', false);
                                $('#select-all').prop('checked', false);
                                if (typeof updateBulkDelete === 'function') updateBulkDelete();
                                
                                table.draw(false);
                                if (typeof showNotification === 'function') showNotification('Success', response.message || 'Selected records deleted', 'success');
                                else alert(response.message || 'Selected records deleted');
                            } else {
                                if (typeof showNotification === 'function') showNotification(response.message || 'Operation failed', 'error');
                                else alert(response.message || 'Operation failed');
                            }
                        },
                        error: function (xhr) {
                            let msg = 'Operation failed';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            if (typeof showNotification === 'function') showNotification(msg, 'error');
                            else alert(msg);
                        }
                    });
                });
                window.deleteUser = function(id, name) {
                    if (!confirm(`Are you sure you want to delete ${name}?`)) return;

                    fetch(`{{ route('users.index') }}/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new URLSearchParams({ '_method': 'DELETE' })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Success', data.message, 'success');
                            table.draw(false);
                        } else {
                            showNotification('Error', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        showNotification('Error', 'An error occurred', 'error');
                    });
                };
            });
        </script>
    @endpush
@endsection