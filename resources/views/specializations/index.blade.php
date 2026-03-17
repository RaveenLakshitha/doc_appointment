@extends('layouts.app')

@section('title', __('file.specializations'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.specializations') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.manage_specialization_records') }}</p>
            </div>
            <a href="{{ route('specializations.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('file.add_specialization') }}
            </a>
        </div>

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('specializations.bulkDelete') }}" id="bulk-delete-form-el"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
                <div id="bulk-ids-container"></div>
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.specialization_selected') }}
                </span>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                    {{ __('file.delete_selected') }}
                </button>
            </form>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
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
                            {{ __('file.description') }}
                        </th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.doctors') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.department') }}
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

    <div id="profile-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="profile-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
            onclick="closeProfileDrawer()"></div>
        <div id="profile-panel"
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="drawer-name"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.specialization_details') }}</p>
                </div>
                <button onclick="closeProfileDrawer()"
                    class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-5 py-5 text-sm space-y-6">
                <div>
                    <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.department') }}
                    </h4>
                    <div class="text-gray-900 dark:text-white font-medium" id="drawer-department"></div>
                </div>
                <div>
                    <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.doctors') }}
                    </h4>
                    <div class="text-gray-900 dark:text-white font-medium" id="drawer-doctors"></div>
                </div>
                <div>
                    <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.description') }}
                    </h4>
                    <div class="text-gray-900 dark:text-white whitespace-pre-wrap" id="drawer-description"></div>
                </div>
            </div>
            <div class="px-5 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeProfileDrawer()"
                    class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                    {{ __('file.close') }}
                </button>
            </div>
        </div>
    </div>

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
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.edit_specialization') }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.specialization_details') }}</p>
                </div>
                <button onclick="closeEditDrawer()" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="edit-form" class="flex-1 flex flex-col">
                @csrf
                @method('PATCH')
                <input type="hidden" name="id" id="edit-id">
                <div class="flex-1 overflow-y-auto px-5 py-5 text-sm space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.name') }}</label>
                        <input type="text" name="name" id="edit-name" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.department') }}</label>
                        <select name="department_id" id="edit-department_id" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.select_department') }}</option>
                            @foreach(\App\Models\Department::where('status', true)->orderBy('name')->get() as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.description') }}</label>
                        <textarea name="description" id="edit-description" rows="6"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"></textarea>
                    </div>
                </div>
                <div class="px-5 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                        class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        {{ __('file.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: '{{ route('specializations.datatable') }}',
                    order: [[1, 'asc']],
                    columnDefs: [
                        { orderable: false, targets: [0, 5] },
                        { searchable: false, targets: [0, 3, 5] }
                    ],
                    columns: [
                        { data: 'id', render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`, className: 'text-center' },
                        { data: 'name', render: data => data || '-' },
                        { data: 'description', render: data => data ? `<div class="max-w-md truncate" title="${data}">${data}</div>` : '<span class="text-gray-400">—</span>' },
                        { data: 'doctors_count', className: 'text-center font-medium' },
                        { data: 'department_name', render: data => data || '-' },
                        {
                            data: null, orderable: false, searchable: false, className: 'text-right whitespace-nowrap', render: (data, type, row) => `
                                                                                        <div class="flex items-center justify-end gap-1">
                                                                                            <button onclick='openProfileDrawer(${JSON.stringify(row).replace(/'/g, "\\'")})' class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                                                </svg>
                                                                                            </button>
                                                                                            ${row.edit_url ? `<button onclick='openEditDrawer(${JSON.stringify(row).replace(/'/g, "\\'")})' class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                                                </svg>
                                                                                            </button>` : ''}
                                                                                          ${row.delete_url ? `<button type="button" onclick="confirmDelete('${row.delete_url}')" class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="{{ __('file.delete') }}">
                                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                                </svg>
                                                                            </button>` : ''}
                                                                                        </div>` }
                    ],
                    layout: {
                        topStart: {
                            buttons: [
                                { extend: 'pageLength', className: 'inline-flex items-center gap-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium transition shadow-sm' },
                                {
                                    extend: 'collection', text: "{{ __('file.Export') }}", className: 'bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium',
                                    buttons: [
                                        { extend: 'copy', text: "{{ __('file.copy') }}", exportOptions: { columns: [0, 1, 2, 3, 4] } },
                                        { extend: 'excel', text: 'Excel', filename: 'Specializations_{{ date("Y-m-d") }}', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                                        { extend: 'csv', text: 'CSV', filename: 'Specializations_{{ date("Y-m-d") }}', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                                        { extend: 'pdf', text: 'PDF', filename: 'Specializations_{{ date("Y-m-d") }}', title: 'Specialization List', exportOptions: { columns: [0, 1, 2, 3, 4] } },
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
                        searchPlaceholder: "{{ __('file.search_specializations') }}",
                        lengthMenu: "{{ __('file.show_entries') }}",
                        info: "{{ __('file.showing_entries') }}",
                        emptyTable: "{{ __('file.no_specializations_found') }}",
                        processing: "{{ __('file.processing') }}"
                    }
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
                        success: (response) => {
                            table.draw(false);
                            updateBulkDelete();
                            $('#select-all').prop('checked', false);
                            if (typeof showNotification === 'function') showNotification('Success', response.message || 'Items deleted successfully.', 'success');
                        },
                        error: (xhr) => {
                            const msg = xhr.responseJSON?.message || 'Delete failed.';
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
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
                            if (response.success) {
                                table.draw(false);
                                $('.row-checkbox').prop('checked', false);
                                $('#select-all').prop('checked', false);
                                updateBulkDelete();
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

                const profileDrawer = document.getElementById('profile-drawer');
                const editDrawer = document.getElementById('edit-drawer');
                let bodyScrollPos = 0;

                window.openProfileDrawer = function (spec) {
                    document.getElementById('drawer-name').textContent = spec.name;
                    document.getElementById('drawer-department').textContent = spec.department_name || '—';
                    document.getElementById('drawer-doctors').textContent = spec.doctors_count;
                    document.getElementById('drawer-description').textContent = spec.description || '—';
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

                window.openEditDrawer = function (spec) {
                    document.getElementById('edit-id').value = spec.id;
                    document.getElementById('edit-name').value = spec.name || '';
                    document.getElementById('edit-description').value = spec.description || '';
                    document.getElementById('edit-department_id').value = spec.department?.id || '';
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
                    fetch(`{{ url('specializations') }}/${id}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(async response => {
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                const data = await response.json();
                                if (!response.ok) {
                                    let errorMsg = data.message || 'Update failed';
                                    if (data.errors) {
                                        errorMsg = Object.values(data.errors).flat().join('\n');
                                    }
                                    throw new Error(errorMsg);
                                }
                                return data;
                            }
                            throw new Error(`Unexpected response: ${response.status}`);
                        })
                        .then(data => {
                            if (data.success) {
                                table.draw(false);
                                closeEditDrawer();
                                showNotification(data.message || 'Specialization updated successfully!', 'success');
                            }
                        })
                        .catch(error => {
                            console.error('Update error:', error);
                            showNotification(error.message, 'error');
                        });
                });

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') {
                        if (!profileDrawer.classList.contains('hidden')) closeProfileDrawer();
                        if (!editDrawer.classList.contains('hidden')) closeEditDrawer();
                    }
                });
            });
        </script>
    @endpush
@endsection