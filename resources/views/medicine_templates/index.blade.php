@extends('layouts.app')

@section('title', __('file.medicine_templates'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.medicine_templates') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_reusable_medication_templates') }}
                </p>
            </div>

            <div class="flex flex-row-reverse sm:flex-row gap-3 w-full sm:w-auto justify-between sm:justify-end">
                <a href="{{ route('medicine-templates.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('file.add_template') }}
                </a>
            </div>
        </div>

        <div id="bulk-delete-bar" class="hidden mb-6">
            <div
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.templates_selected') }}
                </span>
                <button id="bulk-delete-btn"
                    class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                    {{ __('file.delete_selected') }}
                </button>
            </div>
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
                            {{ __('file.template_name') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.category') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.description') }}
                        </th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('file.medications') }}
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: '{{ route('medicine-templates.datatable') }}',
                    order: [[1, 'asc']],
                    columnDefs: [
                        { orderable: false, targets: [0, 5] },
                        { searchable: false, targets: [0, 4, 5] }
                    ],
                    columns: [
                        {
                            data: 'id',
                            render: data => `<input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="${data}">`,
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        { data: 'name', render: data => data || '-' },
                        { data: 'category', render: data => data || '-' },
                        { data: 'description', render: data => data ? `<span title="${data}">${data.substring(0, 60)}${data.length > 60 ? '...' : ''}</span>` : '-' },
                        {
                            data: 'medications_count',
                            className: 'text-center',
                            render: data => `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300">${data} {{ __('file.medications') }}</span>`
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-right whitespace-nowrap',
                            render: (data, type, row) => `
                                                            <div class="flex items-center justify-end gap-1">
                                                                <a href="${row.show_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                                </a>
                                                                <a href="${row.edit_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                                </a>
                                                                <button type="button" onclick="confirmDelete('${row.delete_url}')"
                                                                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                </button>
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
                                    text: "{{ __('file.export') }}",
                                    buttons: [
                                        { extend: 'copy', text: "{{ __('file.copy') }}" },
                                        { extend: 'excel', text: 'Excel', filename: 'Medicine_Templates_{{ date("Y-m-d") }}' },
                                        { extend: 'csv', text: 'CSV', filename: 'Medicine_Templates_{{ date("Y-m-d") }}' },
                                        { extend: 'pdf', text: 'PDF', filename: 'Medicine_Templates_{{ date("Y-m-d") }}', title: 'Medicine Templates' },
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
                        searchPlaceholder: "{{ __('file.search_templates') }}",
                        lengthMenu: "{{ __('file.show_entries') }}",
                        info: "{{ __('file.showing_entries') }}",
                        emptyTable: "{{ __('file.no_templates_found') }}",
                        processing: "{{ __('file.processing') }}"
                    }
                });

                $('#select-all').on('change', function () {
                    $('.row-checkbox').prop('checked', this.checked);
                    updateBulkUI();
                });

                $(document).on('change', '.row-checkbox', updateBulkUI);

                function updateBulkUI() {
                    const count = $('.row-checkbox:checked').length;
                    $('#bulk-delete-bar').toggleClass('hidden', count === 0);
                    $('#selected-count').text(count);
                }

                $('#bulk-delete-btn').on('click', function () {
                    const ids = $('.row-checkbox:checked')
                        .map(function () { return parseInt(this.value, 10); })
                        .get();

                    if (ids.length === 0) return;

                    if (!confirm('{{ __('file.confirm_delete_selected_templates') }}')) return;

                    $.ajax({
                        url: '{{ route('medicine-templates.bulkDelete') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE',
                            ids: ids
                        },
                        success: function (response) {
                            table.draw(false);
                            $('.row-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            updateBulkUI();
                        },
                        error: function (xhr) {
                            let msg = 'Error deleting templates';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            showNotification(msg, 'error');
                        }
                    });
                });

                window.confirmDelete = function (url) {
                    if (confirm('{{ __('file.confirm_delete_template') }}')) {
                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function (response) {
                                table.draw(false);
                            },
                            error: function () {
                                showNotification('Error deleting template.', 'error');
                            }
                        });
                    }
                };
            });
        </script>
    @endpush
@endsection