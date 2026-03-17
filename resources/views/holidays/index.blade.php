@extends('layouts.app')

@section('title', __('file.holidays'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.holidays') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_holiday_records') }}
                </p>
            </div>
            <button onclick="openCreateDrawer()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('file.add_holiday') }}
            </button>
        </div>

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('holidays.bulkDelete') }}" id="bulk-delete-form-el"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
                <div id="bulk-ids-container"></div>
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.holiday_selected') }}
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
                            <th class="px-4 sm:px-6 py-3 text-right all pr-6" style="width: 80px; min-width: 80px;">
                                <input type="checkbox" id="select-all"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.name') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.start_date') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.end_date') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.description') }}
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create/Edit Drawer -->
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
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="edit-drawer-title">
                        {{ __('file.add_holiday') }}
                    </h3>
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
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    <input type="hidden" name="id" id="edit-id">

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.name') }}</label>
                        <input type="text" name="name" id="edit-name" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.start_date') }}</label>
                            <input type="date" name="start_date" id="edit-start-date" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.end_date') }}</label>
                            <input type="date" name="end_date" id="edit-end-date" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow" />
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.description') }}</label>
                        <textarea name="description" id="edit-description" rows="4"
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
                        {{ __('file.save') }}
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
                    ajax: '{{ route('holidays.datatable') }}',
                    order: [[2, 'desc']], // Order by end_date by default
                    columnDefs: [
                        { orderable: false, targets: [0, 5] },
                        { searchable: false, targets: [0, 5] }
                    ],
                    columns: [
                        { data: 'id', render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`, className: 'text-center' },
                        { data: 'name', render: data => data || '-' },
                        { data: 'start_date', render: data => data || '-' },
                        { data: 'end_date', render: data => data || '-' },
                        { data: 'description', render: data => data || '-' },
                        {
                            data: null, orderable: false, searchable: false, className: 'text-right whitespace-nowrap', render: (data, type, row) => `
                                                <div class="flex items-center justify-end gap-1">
                                                    <button type="button" class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors edit-btn">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </button>
                                                    <form method="POST" action="${row.delete_url}" class="inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" onclick="confirmDelete(this)"
                                                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>`
                        }
                    ],
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    language: {
                        search: "",
                        searchPlaceholder: "{{ __('file.search_holidays') }}",
                        lengthMenu: "{{ __('file.show_entries') }}",
                        info: "{{ __('file.showing_entries') }}",
                        emptyTable: "{{ __('file.no_holidays_found') }}",
                        processing: "{{ __('file.processing') }}"
                    }
                });

                $('#docapp-table tbody').on('click', '.edit-btn', function () {
                    let tr = $(this).closest('tr');
                    if (tr.hasClass('child')) {
                        tr = tr.prev('.parent');
                    }
                    let row = table.row(tr).data();
                    if (row) {
                        openEditDrawer(row);
                    }
                });

                window.confirmDelete = function (button) {
                    if (confirm('{{ __("file.confirm_delete_holiday") }}')) {
                        const form = button.closest('.delete-form');
                        fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form)
                        }).then(r => r.json()).then(res => {
                            if (res.success) {
                                table.draw(false);
                                if (typeof showNotification === 'function') showNotification('Success', res.message, 'success');
                            }
                        });
                    }
                };

                const editDrawer = document.getElementById('edit-drawer');
                let bodyScrollPos = 0;

                window.openCreateDrawer = function () {
                    document.getElementById('edit-id').value = '';
                    document.getElementById('edit-name').value = '';
                    document.getElementById('edit-start-date').value = '';
                    document.getElementById('edit-end-date').value = '';
                    document.getElementById('edit-description').value = '';
                    document.getElementById('form-method').value = 'POST';
                    document.getElementById('edit-drawer-title').textContent = '{{ __('file.add_holiday') }}';

                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = '-' + bodyScrollPos + 'px';
                    document.body.style.width = '100%';

                    editDrawer.classList.remove('hidden');
                };

                window.openEditDrawer = function (holiday) {
                    document.getElementById('edit-id').value = holiday.id;
                    document.getElementById('edit-name').value = holiday.name || '';
                    document.getElementById('edit-start-date').value = holiday.start_date || '';
                    document.getElementById('edit-end-date').value = holiday.end_date || '';
                    document.getElementById('edit-description').value = holiday.description || '';
                    document.getElementById('form-method').value = 'PATCH';
                    document.getElementById('edit-drawer-title').textContent = '{{ __('file.edit_holiday') }}';

                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = '-' + bodyScrollPos + 'px';
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
                    const method = formData.get('_method');

                    const url = method === 'PATCH'
                        ? "{{ route('holidays.update', ['holiday' => ':id']) }}".replace(':id', id)
                        : "{{ route('holidays.store') }}";

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                        .then(async response => {
                            if (!response.ok) {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    const err = await response.json();
                                    throw new Error(err.message || 'Validation failed');
                                } else {
                                    throw new Error("Server responded with status " + response.status);
                                }
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                table.draw(false);
                                closeEditDrawer();
                                if (typeof showNotification === 'function') showNotification('Success', data.message, 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Update error:', error);
                            if (typeof showNotification === 'function') showNotification('Error', error.message, 'error');
                        });
                });
            });
        </script>
    @endpush
@endsection