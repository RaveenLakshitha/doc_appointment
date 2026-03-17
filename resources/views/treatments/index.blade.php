@extends('layouts.app')

@section('title', __('file.treatments'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.treatments') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_treatment_records') }}
                </p>
            </div>
            <button onclick="openCreateDrawer()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('file.add_treatment') }}
            </button>
        </div>

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('treatments.bulkDelete') }}"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
                <input type="hidden" name="ids" id="bulk-ids">
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.treatments_selected') }}
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
                <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-right pr-6" style="width: 80px; min-width: 80px;">
                                <input type="checkbox" id="select-all"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.name') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.code') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.appointments') }}
                            </th>
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
        </div>
    </div>

    <!-- Create Drawer -->
    <div id="create-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="create-overlay" class="absolute inset-0 bg-gray-   900/60 dark:bg-black/80 backdrop-blur-sm"
            onclick="closeCreateDrawer()"></div>
        <div id="create-panel"
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.create_new_treatment') }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.add_treatment_template') }}</p>
                </div>
                <button onclick="closeCreateDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-5 py-5 text-sm">
                <form id="create-form" class="space-y-5">
                    @csrf
                    <div>
                        <label
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">{{ __('file.name') }}
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="create-name" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                            placeholder="e.g. Root Canal, Dental Cleaning" />
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="active" id="create-active" value="1" checked
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                        <label for="create-active"
                            class="ml-2 block text-sm text-gray-900 dark:text-gray-300">{{ __('file.active_visible_for_doctors') }}</label>
                    </div>
                </form>
            </div>
            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <button onclick="closeCreateDrawer()"
                        class="flex-1 px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">{{ __('file.cancel') }}</button>
                    <button type="submit" form="create-form"
                        class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">{{ __('file.create') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile / View Drawer -->
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
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.treatment_details') }}</p>
                </div>
                <button onclick="closeProfileDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-5 py-5 text-sm">
                <div class="space-y-6">
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                            {{ __('file.basic_info') }}
                        </h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.name') }}</label>
                                <div class="text-gray-900 dark:text-white mt-1" id="drawer-name-detail"></div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.code') }}</label>
                                <div class="text-gray-900 dark:text-white mt-1" id="drawer-code"></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            {{ __('file.status') }}
                        </h4>
                        <div id="drawer-status"></div>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            {{ __('file.usage') }}
                        </h4>
                        <p id="drawer-usage" class="text-gray-900 dark:text-white"></p>
                    </div>
                </div>
            </div>
            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeProfileDrawer()"
                    class="w-full px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">{{ __('file.close') }}</button>
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
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.edit_treatment') }}</p>
                </div>
                <button onclick="closeEditDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-5 py-5 text-sm">
                <form id="edit-form" class="space-y-5">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="id" id="edit-id">
                    <div>
                        <label
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">{{ __('file.name') }}</label>
                        <input type="text" name="name" id="edit-name" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>
                    <div>
                        <label
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">{{ __('file.code') }}
                            (auto-generated)</label>
                        <input type="text" id="edit-code"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow opacity-60 cursor-not-allowed"
                            disabled>
                        <input type="hidden" name="code" id="edit-code-hidden">
                    </div>
                    <div>
                        <label
                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">
                            {{ __('file.status') }}
                        </label>
                        <select name="active" id="edit-active"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="1">{{ __('file.active') }}</option>
                            <option value="0">{{ __('file.inactive') }}</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <button onclick="closeEditDrawer()"
                        class="flex-1 px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">{{ __('file.cancel') }}</button>
                    <button type="submit" form="edit-form"
                        class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">{{ __('file.save_changes') }}</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openCreateDrawer() {
                document.getElementById('create-drawer').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeCreateDrawer() {
                document.getElementById('create-drawer').classList.add('hidden');
                document.body.style.overflow = '';
            }

            document.addEventListener('DOMContentLoaded', function () {
                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: '{{ route('treatments.datatable') }}',
                    order: [[1, 'asc']],
                    columnDefs: [
                        { orderable: false, targets: [0, -1] },
                        { searchable: false, targets: [0, 4, -1] }
                    ],
                    columns: [
                        {
                            data: 'id',
                            render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`,
                            className: 'text-center'
                        },
                        { data: 'name', render: data => data || '-' },
                        { data: 'code', render: data => data || '—' },
                        { data: 'appointment_count', className: 'text-left', render: data => data || 0 },
                        {
                            data: 'active',
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
                                                                                                ${row.delete_url ? `
                                                                                                    <button type="button"
                                                                                                            onclick="deleteTreatment('${row.delete_url}')"
                                                                                                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors">
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
                                { extend: 'pageLength', className: 'inline-flex items-center gap-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium transition shadow-sm' },
                                {
                                    extend: 'collection',
                                    text: "{{ __('file.Export') }}",
                                    className: 'bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium',
                                    buttons: [
                                        { extend: 'copy', text: "{{ __('file.copy') }}" },
                                        { extend: 'excel', text: 'Excel', filename: 'Treatments_{{ date("Y-m-d") }}' },
                                        { extend: 'csv', text: 'CSV', filename: 'Treatments_{{ date("Y-m-d") }}' },
                                        { extend: 'pdf', text: 'PDF', filename: 'Treatments_{{ date("Y-m-d") }}', title: 'Treatment List' },
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
                        searchPlaceholder: "{{ __('file.search_treatments') }}",
                        lengthMenu: "{{ __('file.show_entries') }}",
                        info: "{{ __('file.showing_entries') }}",
                        emptyTable: "{{ __('file.no_treatments_found') }}",
                        processing: "{{ __('file.processing') }}"
                    }
                });

                // Checkbox logic
                $('#select-all').on('change', function () {
                    $('.row-checkbox').prop('checked', this.checked);
                    updateBulkDelete();
                });

                $(document).on('change', '.row-checkbox', updateBulkDelete);

                function updateBulkDelete() {
                    const count = $('.row-checkbox:checked').length;
                    $('#bulk-delete-form').toggleClass('hidden', count === 0);
                    $('#selected-count').text(count);
                    $('#bulk-ids').val($('.row-checkbox:checked').map(function () { return this.value; }).get().join(','));
                }

                // Bulk delete (already AJAX)
                $('#bulk-delete-form form').on('submit', function (e) {
                    e.preventDefault();
                    if (!confirm('{{ __("file.confirm_delete_selected") }}')) return;

                    $.ajax({
                        url: this.action,
                        method: 'POST',
                        data: $(this).serialize(),
                            success: function (response) {
                                table.draw(false);
                                $('.row-checkbox').prop('checked', false);
                                $('#select-all').prop('checked', false);
                                if (typeof updateBulkDelete === 'function') updateBulkDelete();
                                if (typeof updateBulkDeleteUI === 'function') updateBulkDeleteUI();
                                if (typeof showNotification === 'function') showNotification('Success', response.message, 'success');
                            },
                        error: (xhr) => {
                            showNotification(xhr.responseJSON?.message || 'Error deleting selected treatments', 'error');
                        }
                    });
                });

                // Create form (AJAX)
                document.getElementById('create-form')?.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch('{{ route("treatments.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) return response.json().then(err => { throw err; });
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                table.draw(false);
                                closeCreateDrawer();
                                showNotification(data.message || 'Treatment created successfully', 'success');
                            } else {
                                let errorMsg = data.message || 'Validation error';
                                if (data.errors) errorMsg = Object.values(data.errors).flat().join('\n');
                                showNotification(errorMsg, 'error');
                            }
                        })
                        .catch(() => showNotification('Failed to create treatment', 'error'));
                });

                // Profile drawer
                const profileDrawer = document.getElementById('profile-drawer');
                let bodyScrollPos = 0;

                window.openProfileDrawer = function (treatment) {
                    document.getElementById('drawer-name').textContent = treatment.name;
                    document.getElementById('drawer-name-detail').textContent = treatment.name;
                    document.getElementById('drawer-code').textContent = treatment.code || '—';
                    document.getElementById('drawer-usage').textContent = `Used in ${treatment.appointment_count || 0} appointment(s)`;

                    const statusHtml = treatment.active
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

                // Edit drawer
                const editDrawer = document.getElementById('edit-drawer');

                window.openEditDrawer = function (treatment) {
                    document.getElementById('edit-id').value = treatment.id;
                    document.getElementById('edit-drawer-name').textContent = treatment.name || '';
                    document.getElementById('edit-name').value = treatment.name || '';
                    document.getElementById('edit-code').value = treatment.code || '';
                    document.getElementById('edit-code-hidden').value = treatment.code || '';
                    document.getElementById('edit-active').value = treatment.active ? 1 : 0;

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

                document.getElementById('edit-form').addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const id = formData.get('id');

                    fetch(`{{ url('treatments') }}/${id}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) return response.json().then(err => { throw err; });
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                table.draw(false);
                                closeEditDrawer();
                                showNotification(data.message || 'Updated successfully', 'success');
                            } else {
                                let errorMsg = data.message || 'Validation error';
                                if (data.errors) errorMsg = Object.values(data.errors).flat().join('\n');
                                showNotification(errorMsg, 'error');
                            }
                        })
                        .catch(() => showNotification('Failed to update treatment', 'error'));
                });

                // Single delete - AJAX
                window.deleteTreatment = function (url) {
                    if (!confirm('{{ __("file.confirm_delete_treatment") }}')) return;

                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) return response.json().then(err => { throw err; });
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                table.draw(false);
                            } else {
                                showNotification(data.message || 'Cannot delete this treatment', 'error');
                            }
                        })
                        .catch(() => showNotification('Delete failed. Please try again.', 'error'));
                };

                // ESC key to close drawers
                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') {
                        closeCreateDrawer();
                        if (!profileDrawer.classList.contains('hidden')) closeProfileDrawer();
                        if (!editDrawer.classList.contains('hidden')) closeEditDrawer();
                    }
                });
            });
        </script>
    @endpush
@endsection