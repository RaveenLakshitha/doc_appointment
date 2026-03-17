@extends('layouts.app')

@section('title', __('file.expense_categories'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.expense_categories') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_expense_category_records') }}
                </p>
            </div>
            <button type="button" onclick="openAddDrawer()"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('file.add_category') }}
            </button>
        </div>

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('expense-categories.bulkDelete') }}" id="bulk-delete-form-el"
                  class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
                <div id="bulk-ids-container"></div>
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.categories_selected') }}
                </span>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                    {{ __('file.delete_selected') }}
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-right all pr-6" style="width: 80px; min-width: 80px;">
                                <input type="checkbox" id="select-all"
                                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.code') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.name') }}
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.status') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Details Drawer -->
    <div id="profile-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="drawer-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
             onclick="closeProfileDrawer()"></div>

        <div id="drawer-panel" class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto
                                    w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col
                                    h-[90vh] md:h-full rounded-t-3xl md:rounded-none">

            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="drawer-name"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.category_details') }}</p>
                </div>
                <button onclick="closeProfileDrawer()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 text-sm">
                <div class="space-y-5">
                    <div>
                        <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.information') }}
                        </h4>
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.code') }}</label>
                                <div class="text-gray-900 dark:text-white" id="drawer-code"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.name') }}</label>
                                <div class="text-gray-900 dark:text-white" id="drawer-name-detail"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('file.status') }}
                        </h4>
                        <div id="drawer-status"></div>
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            {{ __('file.description') }}
                        </h4>
                        <div class="text-gray-900 dark:text-white" id="drawer-description"></div>
                    </div>
                </div>
            </div>

            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeProfileDrawer()"
                        class="w-full px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                    {{ __('file.close') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Add Drawer -->
    <div id="add-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="add-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
             onclick="closeAddDrawer()"></div>

        <div id="add-panel" class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto
                                    w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col
                                    h-[90vh] md:h-full rounded-t-3xl md:rounded-none">

            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.add_category') }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.create_new_category') }}</p>
                </div>
                <button onclick="closeAddDrawer()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 text-sm">
                <form id="add-form" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.code') }} *</label>
                        <input type="text" name="code" required
                               class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.name') }} *</label>
                        <input type="text" name="name" required
                               class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                        <select name="is_active"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="1">{{ __('file.active') }}</option>
                            <option value="0">{{ __('file.inactive') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.description') }}</label>
                        <textarea name="description" rows="5"
                                  class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"></textarea>
                    </div>
                </form>
            </div>

            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <button onclick="closeAddDrawer()"
                            class="flex-1 px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                        {{ __('file.cancel') }}
                    </button>
                    <button type="submit" form="add-form"
                            class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        {{ __('file.create') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Drawer -->
    <div id="edit-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="edit-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
             onclick="closeEditDrawer()"></div>

        <div id="edit-panel" class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto
                                    w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col
                                    h-[90vh] md:h-full rounded-t-3xl md:rounded-none">

            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="edit-drawer-name"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.edit_category') }}</p>
                </div>
                <button onclick="closeEditDrawer()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 text-sm">
                <form id="edit-form" class="space-y-5">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="id" id="edit-id">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.code') }}</label>
                        <input type="text" name="code" id="edit-code" required
                               class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.name') }}</label>
                        <input type="text" name="name" id="edit-name" required
                               class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                        <select name="is_active" id="edit-status"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="1">{{ __('file.active') }}</option>
                            <option value="0">{{ __('file.inactive') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.description') }}</label>
                        <textarea name="description" id="edit-description" rows="5"
                                  class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"></textarea>
                    </div>
                </form>
            </div>

            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <button onclick="closeEditDrawer()"
                            class="flex-1 px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                        {{ __('file.cancel') }}
                    </button>
                    <button type="submit" form="edit-form"
                            class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        {{ __('file.save_changes') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: '{{ route('expense-categories.datatable') }}',
                    order: [[2, 'asc']],
                    columnDefs: [
                        { orderable: false, targets: [0, 4] },
                        { searchable: false, targets: [0, 3, 4] }
                    ],
                    columns: [
                        {
                            data: 'id',
                            render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`,
                            className: 'text-center'
                        },
                        { data: 'code', render: data => data || '-' },
                        { data: 'name', render: data => data || '-' },
                        {
                            data: 'is_active',
                            className: 'text-center',
                            render: data => data
                                ? `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">{{ __('file.active') }}</span>`
                                : `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">{{ __('file.inactive') }}</span>`
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-right whitespace-nowrap',
                            render: (data, type, row) => `
                                <div class="flex items-center justify-end gap-1">
                                    <button onclick='openProfileDrawer(${JSON.stringify(row).replace(/'/g, "\\'")})'
                                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button onclick='openEditDrawer(${JSON.stringify(row).replace(/'/g, "\\'")})'
                                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <form method="POST" action="${row.delete_url}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('{{ __('file.confirm_delete_category') }}')"
                                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>`
                        }
                    ],
                    layout: {
                        topStart: {
                            buttons: [
                                { extend: 'pageLength', className: 'inline-flex items-center gap-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium transition shadow-sm' },
                                {
                                    extend: 'collection',
                                    text: "{{ __('file.Export') }}",
                                    className: 'bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium',
                                    buttons: [
                                        { extend: 'copy', text: "{{ __('file.copy') }}", exportOptions: { columns: [1, 2, 3] } },
                                        { extend: 'excel', text: 'Excel', filename: 'ExpenseCategories_{{ date("Y-m-d") }}', exportOptions: { columns: [1, 2, 3] } },
                                        { extend: 'csv', text: 'CSV', filename: 'ExpenseCategories_{{ date("Y-m-d") }}', exportOptions: { columns: [1, 2, 3] } },
                                        { extend: 'pdf', text: 'PDF', filename: 'ExpenseCategories_{{ date("Y-m-d") }}', title: 'Expense Categories List', exportOptions: { columns: [1, 2, 3] } },
                                        { extend: 'print', text: "{{ __('file.print') }}", exportOptions: { columns: [1, 2, 3] } },
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
                        searchPlaceholder: "{{ __('file.search_categories') }}",
                        lengthMenu: "{{ __('file.show_entries') }}",
                        info: "{{ __('file.showing_entries') }}",
                        emptyTable: "{{ __('file.no_categories_found') }}",
                        processing: "{{ __('file.processing') }}"
                    }
                });

                // Checkbox logic ───────────────────────────────────────
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

                // Bulk delete AJAX
                $('#bulk-delete-form-el').on('submit', function (e) {
                    e.preventDefault();
                    if (confirm('{{ __("file.confirm_delete_selected") }}')) {
                        $.ajax({
                            url: this.action,
                            method: 'POST',
                            data: $(this).serialize(),
                            success: function (response) {
                                table.draw(false);
                                $('.row-checkbox').prop('checked', false);
                                $('#select-all').prop('checked', false);
                                updateBulkDelete();
                                if (response.success) {
                                    if (typeof showNotification === 'function') showNotification('Success', response.message, 'success');
                                } else {
                                    if (typeof showNotification === 'function') showNotification('Error', response.message, 'error');
                                    else alert(response.message || 'Error deleting categories');
                                }
                            },
                            error: function (xhr) {
                                const msg = xhr.responseJSON?.message || 'Something went wrong';
                                if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                                else alert('Error: ' + msg);
                            }
                        });
                    }
                });

                // Drawer helpers
                const profileDrawer = document.getElementById('profile-drawer');
                const editDrawer = document.getElementById('edit-drawer');
                let bodyScrollPos = 0;

                window.openProfileDrawer = function (cat) {
                    document.getElementById('drawer-name').textContent = cat.name;
                    document.getElementById('drawer-code').textContent = cat.code || '—';
                    document.getElementById('drawer-name-detail').textContent = cat.name || '—';
                    document.getElementById('drawer-description').textContent = cat.description || '—';

                    const statusHtml = cat.is_active
                        ? `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">{{ __('file.active') }}</span>`
                        : `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">{{ __('file.inactive') }}</span>`;
                    document.getElementById('drawer-status').innerHTML = statusHtml;

                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${bodyScrollPos}px`;
                    document.body.style.width = '100%';

                    profileDrawer.classList.remove('hidden');
                };

                window.closeProfileDrawer = function () {
                    profileDrawer.classList.add('hidden');
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    window.scrollTo(0, bodyScrollPos);
                };

                window.openEditDrawer = function (cat) {
                    document.getElementById('edit-id').value = cat.id;
                    document.getElementById('edit-drawer-name').textContent = cat.name || '';
                    document.getElementById('edit-code').value = cat.code || '';
                    document.getElementById('edit-name').value = cat.name || '';
                    document.getElementById('edit-status').value = cat.is_active ? 1 : 0;
                    document.getElementById('edit-description').value = cat.description || '';

                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${bodyScrollPos}px`;
                    document.body.style.width = '100%';

                    editDrawer.classList.remove('hidden');
                };

                window.closeEditDrawer = function () {
                    editDrawer.classList.add('hidden');
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    window.scrollTo(0, bodyScrollPos);
                };

                const addDrawer = document.getElementById('add-drawer');
                window.openAddDrawer = function () {
                    document.getElementById('add-form').reset();
                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${bodyScrollPos}px`;
                    document.body.style.width = '100%';
                    addDrawer.classList.remove('hidden');
                };

                window.closeAddDrawer = function () {
                    addDrawer.classList.add('hidden');
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    window.scrollTo(0, bodyScrollPos);
                };

                // Add form submit (AJAX)
                document.getElementById('add-form').addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const url = `{{ route('expense-categories.store') }}`;

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(async res => {
                        if (!res.ok) {
                            const err = await res.json();
                            throw new Error(err.message || 'Creation failed');
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            table.draw(false);
                            closeAddDrawer();
                            if (typeof showNotification === 'function') showNotification('Success', data.message, 'success');
                        } else {
                            if (typeof showNotification === 'function') showNotification('Error', data.message, 'error');
                            else alert(data.message || 'Creation failed');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        const msg = err.message || 'Failed to create category';
                        if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                        else alert(msg);
                    });
                });

                // Edit form submit (AJAX)
                document.getElementById('edit-form').addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const id = formData.get('id');
                    const url = `{{ route('expense-categories.update', ':id') }}`.replace(':id', id);

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(async res => {
                        if (!res.ok) {
                            const err = await res.json();
                            throw new Error(err.message || 'Update failed');
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            table.draw(false);
                            closeEditDrawer();
                            if (typeof showNotification === 'function') showNotification('Success', data.message, 'success');
                        } else {
                            if (typeof showNotification === 'function') showNotification('Error', data.message, 'error');
                            else alert(data.message || 'Update failed');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        const msg = err.message || 'Failed to update category';
                        if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                        else alert(msg);
                    });
                });

                // ESC key close drawers
                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') {
                        if (!profileDrawer.classList.contains('hidden')) closeProfileDrawer();
                        if (!editDrawer.classList.contains('hidden')) closeEditDrawer();
                        if (!addDrawer.classList.contains('hidden')) closeAddDrawer();
                    }
                });
            });
        </script>
    @endpush
@endsection