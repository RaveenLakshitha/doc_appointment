@extends('layouts.app')

@section('title', __('file.categories'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 pb-4 sm:py-12 pt-20">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.categories') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_categories') }}
                </p>
            </div>
            <button onclick="openCreateDrawer()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('file.add_category') }}
            </button>
        </div>

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('categories.bulkDelete') }}" id="bulk-delete-form-el"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
                <div id="bulk-ids-container"></div>
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.category_selected') }}
                </span>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                    {{ __('file.delete_selected') }}
                </button>
            </form>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700 display nowrap"
                    style="width:100%">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-right all pr-6" style="width: 80px; min-width: 80px;">
                                <input type="checkbox" id="select-all"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider all">
                                {{ __('file.name') }}</th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.description') }}</th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.parent_category') }}</th>
                            <th
                                class="px-4 sm:px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.status') }}</th>
                            <th
                                class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Drawer -->
    <div id="profile-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="drawer-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
            onclick="closeProfileDrawer()"></div>
        <div id="drawer-panel"
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
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
                        <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.details') }}
                        </h4>
                        <div class="grid grid-cols-1 gap-3">
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.name') }}</label>
                                <div class="text-gray-900 dark:text-white" id="drawer-name-detail"></div>
                            </div>
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.parent_category') }}</label>
                                <div class="text-gray-900 dark:text-white" id="drawer-parent"></div>
                            </div>
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                                <div id="drawer-status"></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.description') }}</h4>
                        <div class="text-gray-900 dark:text-white whitespace-pre-wrap" id="drawer-description">—</div>
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

    <!-- Edit Drawer -->
    <div id="edit-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="edit-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
            onclick="closeEditDrawer()"></div>
        <div id="edit-panel"
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
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
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.name') }}</label>
                        <input type="text" name="name" id="edit-name" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.parent_category') }}</label>
                        <select name="parent_id" id="edit-parent"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.no_parent') }}</option>
                            @foreach(\App\Models\Category::whereNull('parent_id')->orderBy('name')->get() as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                        <select name="is_active" id="edit-status"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="1">{{ __('file.active') }}</option>
                            <option value="0">{{ __('file.inactive') }}</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.description') }}</label>
                        <textarea name="description" id="edit-description" rows="6"
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

    <!-- Create Drawer -->
    <div id="create-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="create-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
            onclick="closeCreateDrawer()"></div>
        <div id="create-panel"
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.add_category') }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.create_new_category') }}</p>
                </div>
                <button onclick="closeCreateDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 text-sm">
                <form id="create-form" class="space-y-5">
                    @csrf
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.name') }}</label>
                        <input type="text" name="name" id="create-name" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.parent_category') }}</label>
                        <select name="parent_id" id="create-parent"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.no_parent') }}</option>
                            @foreach(\App\Models\Category::whereNull('parent_id')->orderBy('name')->get() as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                        <select name="is_active" id="create-status"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="1">{{ __('file.active') }}</option>
                            <option value="0">{{ __('file.inactive') }}</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.description') }}</label>
                        <textarea name="description" id="create-description" rows="6"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"></textarea>
                    </div>
                </form>
            </div>
            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <button onclick="closeCreateDrawer()"
                        class="flex-1 px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                        {{ __('file.cancel') }}
                    </button>
                    <button type="submit" form="create-form"
                        class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        {{ __('file.create_category') }}
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
                    ajax: '{{ route('categories.datatable') }}',
                    order: [[1, 'asc']],
                    columnDefs: [
                        { targets: 0, orderable: false, className: 'dtr-control', responsivePriority: 1 },
                        { targets: 1, responsivePriority: 2 },
                        { targets: -1, orderable: false, searchable: false, responsivePriority: 1 }
                    ],
                    columns: [
                        {
                            data: 'id',
                            render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`,
                            className: 'text-center',
                            orderable: false
                        },
                        {
                            data: 'name',
                            render: data => `<div class="font-medium text-gray-900 dark:text-white">${data || '-'}</div>`
                        },
                        {
                            data: 'description',
                            render: data => `<div class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">${data || '—'}</div>`
                        },
                        {
                            data: 'parent_name',
                            render: data => data || '—'
                        },
                        {
                            data: 'status_html',
                            className: 'text-center',
                            render: data => data || '-'
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-right whitespace-nowrap',
                            render: (data, type, row) => `
                            <div class="flex items-center justify-end gap-1">
                                <button onclick='openProfileDrawer(${JSON.stringify(row).replace(/'/g, "\\'")})' class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="{{ __('file.view') }}">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                <button onclick='openEditDrawer(${JSON.stringify(row).replace(/'/g, "\\'")})' class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="{{ __('file.edit') }}">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                ${row.delete_url ? `
                                    <button type="button" onclick="confirmDelete('${row.delete_url}')" class="p-1.5 sm:p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="{{ __('file.delete') }}">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                { extend: 'pageLength', className: 'btn btn-sm btn-light' },
                                { extend: 'collection', text: "{{ __('file.Export') }}", className: 'btn btn-sm btn-dark', buttons: ['copy', 'excel', 'csv', 'pdf', 'print'] }
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
                        infoEmpty: "{{ __('file.no_items_found') }}",
                        emptyTable: "{{ __('file.no_items_found') }}",
                        processing: false,
                    },
                    autoWidth: false,
                    scrollX: false
                });

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
                        success: function (response) {
                            table.draw(false);
                            updateBulkDelete();
                            $('#select-all').prop('checked', false);
                            if (response.success) {
                                if (typeof showNotification === 'function') showNotification('Success', response.message || 'Categories deleted successfully.', 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', response.message || 'Error deleting categories', 'error');
                                else alert(response.message);
                            }
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON?.message || 'Delete failed.';
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                            else alert(msg);
                        }
                    });
                });

                window.confirmDelete = function (url) {
                    if (!confirm('{{ __("file.confirm_delete_item") }}')) return;
                    
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            table.draw(false);
                            $('.row-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            if (typeof updateBulkDelete === 'function') updateBulkDelete();
                            if (typeof updateBulkDeleteUI === 'function') updateBulkDeleteUI();
                            if (response.success) {
                                if (typeof showNotification === 'function') showNotification('Success', response.message, 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', response.message, 'error');
                                else alert(response.message);
                            }
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON?.message || 'Delete failed.';
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                            else alert(msg);
                        }
                    });
                };

                const profileDrawer = document.getElementById('profile-drawer');
                const editDrawer = document.getElementById('edit-drawer');
                const createDrawer = document.getElementById('create-drawer');
                let bodyScrollPos = 0;

                window.openProfileDrawer = function (category) {
                    document.getElementById('drawer-name').textContent = category.name;
                    document.getElementById('drawer-name-detail').textContent = category.name || '—';
                    document.getElementById('drawer-parent').textContent = category.parent_name || '—';
                    document.getElementById('drawer-description').textContent = category.description || '—';

                    document.getElementById('drawer-status').innerHTML = category.status_html;

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

                window.openEditDrawer = function (category) {
                    document.getElementById('edit-id').value = category.id;
                    document.getElementById('edit-drawer-name').textContent = category.name || '';
                    document.getElementById('edit-name').value = category.name || '';
                    document.getElementById('edit-parent').value = category.parent_id || '';
                    document.getElementById('edit-status').value = category.is_active ? 1 : 0;
                    document.getElementById('edit-description').value = category.description || '';

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

                window.openCreateDrawer = function () {
                    document.getElementById('create-form').reset();
                    document.getElementById('create-status').value = 1;
                    document.getElementById('create-parent').value = '';

                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${bodyScrollPos}px`;
                    document.body.style.width = '100%';

                    createDrawer.classList.remove('hidden');
                };

                window.closeCreateDrawer = function () {
                    createDrawer.classList.add('hidden');
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    window.scrollTo(0, bodyScrollPos);
                };

                document.getElementById('edit-form').addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const id = formData.get('id');

                    fetch(`{{ route('categories.update', ':id') }}`.replace(':id', id), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                table.draw(false);
                                closeEditDrawer();
                                if (typeof showNotification === 'function') showNotification('Success', data.message, 'success');
                                else alert(data.message);
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', data.message || 'Update failed', 'error');
                                else alert(data.message || 'Update failed');
                            }
                        })
                        .catch(() => {
                            if (typeof showNotification === 'function') showNotification('Error', 'Failed to update category', 'error');
                            else alert('Failed to update category');
                        });
                });

                document.getElementById('create-form').addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch('{{ route('categories.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                table.draw(false);
                                closeCreateDrawer();
                                if (typeof showNotification === 'function') showNotification('Success', data.message, 'success');
                                else alert(data.message);
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', data.message || 'Create failed', 'error');
                                else alert(data.message || 'Create failed');
                            }
                        })
                        .catch(() => {
                            if (typeof showNotification === 'function') showNotification('Error', 'Failed to create category', 'error');
                            else alert('Failed to create category');
                        });
                });

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') {
                        if (!profileDrawer.classList.contains('hidden')) closeProfileDrawer();
                        if (!editDrawer.classList.contains('hidden')) closeEditDrawer();
                        if (!createDrawer.classList.contains('hidden')) closeCreateDrawer();
                    }
                });
            });
        </script>
    @endpush
@endsection