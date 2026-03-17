@extends('layouts.app')

@section('title', __('file.cash_registers'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.cash_registers') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_cash_registers') }}
                </p>
            </div>

            <div class="flex flex-row-reverse sm:flex-row gap-3 w-full sm:w-auto justify-between sm:justify-end">
                @if (!auth()->user()->cashRegisters()->whereNull('closed_at')->exists())
                    <button onclick="openCreateDrawer()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('file.open_new_register') }}
                    </button>
                @else
                    <span
                        class="inline-flex items-center px-4 py-2 bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 rounded-lg text-sm font-medium border border-amber-200 dark:border-amber-800">
                        {{ __('file.open_register_warning') }}
                    </span>
                @endif
            </div>
        </div>

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('cash-registers.bulkDelete') }}" id="bulk-delete-form-el"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
                @method('DELETE')
                <div id="bulk-ids-container"></div>
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.item_selected') }}
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
                            <th class="px-6 py-3 text-left w-12">
                                <input type="checkbox" id="select-all"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.id') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.user') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.opened_at') }}
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.opening_balance') }}
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.expected_closing') }}
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.actual_closing') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.status') }}
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.action') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                </table>
            </div>
        </div>

        <!-- View / Close Drawer -->
        <div id="register-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
            <div id="drawer-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
                onclick="closeDrawer()"></div>

            <div id="drawer-panel"
                class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-lg bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[90vh] md:h-full rounded-t-3xl md:rounded-none overflow-hidden">
                <div class="md:hidden flex justify-center pt-4 pb-2">
                    <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                </div>

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="drawer-title">
                            {{ __('file.register_details') }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.session_id') }}<span
                                id="drawer-id"></span></p>
                    </div>
                    <button onclick="closeDrawer()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-6 text-sm space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div
                            class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-xs uppercase text-gray-500 dark:text-gray-400 mb-1">{{ __('file.user') }}</p>
                            <p class="font-medium" id="drawer-user"></p>
                        </div>
                        <div
                            class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-xs uppercase text-gray-500 dark:text-gray-400 mb-1">{{ __('file.opened_at') }}
                            </p>
                            <p class="font-medium" id="drawer-opened"></p>
                        </div>
                        <div
                            class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-xs uppercase text-gray-500 dark:text-gray-400 mb-1">
                                {{ __('file.opening_balance') }}
                            </p>
                            <p class="font-medium text-green-600 dark:text-green-400" id="drawer-opening-balance"></p>
                        </div>
                        <div
                            class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-xs uppercase text-gray-500 dark:text-gray-400 mb-1">{{ __('file.status') }}</p>
                            <div id="drawer-status-badge"></div>
                        </div>
                        <div
                            class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-xs uppercase text-gray-500 dark:text-gray-400 mb-1">
                                {{ __('file.total_expenses') }}
                            </p>
                            <p class="font-medium text-red-600 dark:text-red-400" id="drawer-expenses"></p>
                        </div>
                        <div
                            class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-xs uppercase text-gray-500 dark:text-gray-400 mb-1">
                                {{ __('file.total_purchases') }}
                            </p>
                            <p class="font-medium text-red-600 dark:text-red-400" id="drawer-purchases"></p>
                        </div>
                    </div>

                    <div id="reconciliation-section" class="hidden">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                            {{ __('file.close_reconcile_register') }}
                        </h4>
                        <form id="close-form" class="space-y-5">
                            @csrf
                            <input type="hidden" name="id" id="close-id">

                            {{-- Actual Balance field removed as per user request --}}

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.notes_discrepancy_reason') }}</label>
                                <textarea name="notes" rows="3"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"></textarea>
                            </div>

                            <div class="pt-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('file.close_expected_balance_message') }} <span id="expected-value"
                                        class="font-semibold text-gray-900 dark:text-white"></span>
                                </p>
                            </div>
                        </form>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                            {{ __('file.cash_movements') }}
                        </h4>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-4 py-2 text-left">{{ __('file.time') }}</th>
                                        <th class="px-4 py-2 text-left">{{ __('file.type') }}</th>
                                        <th class="px-4 py-2 text-right">{{ __('file.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="transactions-body" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-3">
                    <button onclick="closeDrawer()"
                        class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                        Close
                    </button>
                    <button id="btn-close-register" form="close-form" type="submit"
                        class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition hidden">
                        Close & Reconcile
                    </button>
                </div>
            </div>
        </div>

        <!-- Open New Register Drawer -->
        <div id="open-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
            <div class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm" onclick="closeOpenDrawer()">
            </div>

            <div
                class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-[70vh] md:h-full rounded-t-3xl md:rounded-none overflow-hidden">
                <div class="md:hidden flex justify-center pt-4 pb-2">
                    <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                </div>

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.open_new_cash_register') }}
                    </h3>
                    <button onclick="closeOpenDrawer()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-8">
                    <form id="open-form" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.opening_cash_balance') }}
                            </label>
                            <input type="number" name="opening_balance" step="0.01" min="0" required autofocus
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('file.opening_balance_instruction') }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.notes_optional') }}
                            </label>
                            <textarea name="notes" rows="4"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"></textarea>
                        </div>
                    </form>
                </div>

                <div
                    class="px-6 py-5 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-4">
                    <button onclick="closeOpenDrawer()"
                        class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition">
                        {{ __('file.cancel') }}
                    </button>
                    <button id="btn-open-register" form="open-form" type="submit"
                        class="flex-1 py-3 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-lg font-medium transition">
                        {{ __('file.open_register') }}
                    </button>
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
                        ajax: '{{ route('cash-registers.datatable') }}',
                        order: [[3, 'desc']],
                        columnDefs: [
                            { orderable: false, targets: [0, 8] },
                            { searchable: false, targets: [0, 4, 5, 6, 7, 8] }
                        ],
                        columns: [
                            {
                                data: 'id',
                                render: d => `<input type="checkbox" name="ids[]" value="${d}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`,
                                className: 'text-center'
                            },
                            { data: 'id' },
                            { data: 'user_name', render: d => d || '-' },
                            { data: 'opened_at_formatted' },
                            { data: 'opening_balance_formatted', className: 'text-right font-medium text-green-600 dark:text-green-400' },
                            { data: 'expected_closing_formatted', className: 'text-right' },
                            { data: 'actual_closing_formatted', className: 'text-right' },
                            { data: 'status_html', className: 'text-center' },
                            {
                                data: null,
                                render: (data, type, row) => {
                                    let html = `
                                                        <div class="flex items-center justify-end gap-1">
                                                            <button onclick='openRegisterDrawer(${JSON.stringify(row).replace(/'/g, "\\'")})'
                                                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                                                title="{{ __('file.view') }}">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                </svg>
                                                            </button>`;

                                    html += `
                                                            <button onclick="deleteRegister(${row.id})"
                                                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors"
                                                                title="{{ __('file.delete') }}">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </div>`;
                                    return html;
                                },
                                className: 'text-right whitespace-nowrap'
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
                                        text: "{{ __('file.Export') }}",
                                        className: 'bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium',
                                        buttons: [
                                            { extend: 'copy', text: "{{ __('file.copy') }}" },
                                            { extend: 'excel', text: 'Excel', filename: 'CashRegisters_{{ date("Y-m-d") }}' },
                                            { extend: 'csv', text: 'CSV', filename: 'CashRegisters_{{ date("Y-m-d") }}' },
                                            { extend: 'pdf', text: 'PDF', filename: 'CashRegisters_{{ date("Y-m-d") }}', title: 'Cash Registers List' },
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
                            searchPlaceholder: "{{ __('file.search_registers') }}",
                            lengthMenu: "{{ __('file.show_entries') }}",
                            info: "{{ __('file.showing_entries_registers') }}",
                            emptyTable: "{{ __('file.no_cash_registers_found') }}",
                            processing: "{{ __('file.processing') }}"
                        }
                    });

                    $('#select-all').on('change', function () {
                        $('.row-checkbox').prop('checked', this.checked);
                        updateBulkDeleteUI();
                    });

                    $(document).on('change', '.row-checkbox', updateBulkDeleteUI);

                    function updateBulkDeleteUI() {
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
                        if (!confirm('{{ __("file.confirm_delete_selected_registers") }}')) return;

                        $.ajax({
                            url: this.action,
                            method: 'POST',
                            data: $(this).serialize(),
                            success: (res) => {
                                table.draw(false);
                                $('#select-all').prop('checked', false);
                                updateBulkDeleteUI();
                                if (res.success) {
                                    if (typeof showNotification === 'function') showNotification('Success', res.message, 'success');
                                } else {
                                    if (typeof showNotification === 'function') showNotification('Error', res.message, 'error');
                                }
                            },
                            error: (xhr) => {
                                const msg = xhr.responseJSON?.message || 'Bulk delete failed';
                                if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                                else alert(msg);
                            }
                        });
                    });

                    window.deleteRegister = function (id) {
                        if (!confirm('{{ __("file.confirm_delete_register") }}')) return;
                        $.ajax({
                            url: '{{ route("cash-registers.destroy", ":id") }}'.replace(':id', id),
                            method: 'POST',
                            data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                            success: (res) => {
                                table.draw(false);
                                if (res.success) {
                                    if (typeof showNotification === 'function') showNotification('Success', res.message, 'success');
                                } else {
                                    if (typeof showNotification === 'function') showNotification('Error', res.message, 'error');
                                }
                            },
                            error: (xhr) => {
                                const msg = xhr.responseJSON?.message || 'Delete failed';
                                if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                                else alert(msg);
                            }
                        });
                    };

                    window.bulkDelete = async function () {
                        const ids = $('.row-checkbox:checked').map(function () {
                            return this.value;
                        }).get();

                        if (ids.length === 0) return;

                        if (!confirm("{{ __('file.confirm_bulk_delete_registers', ['count' => ':count']) }}".replace(':count', ids.length))) return;

                        try {
                            const response = await fetch(`{{ route('cash-registers.bulkDelete') }}`, {
                                method: 'POST',
                                body: JSON.stringify({ ids: ids, _token: '{{ csrf_token() }}' }),
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            const data = await response.json();
                            if (data.success) {
                                table.draw(false);
                                document.getElementById('bulk-delete-btn').classList.add('hidden');
                                document.getElementById('select-all').checked = false;
                                if (typeof showNotification === 'function') showNotification('Success', data.message, 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', data.message, 'error');
                            }
                        } catch (err) {
                            console.error(err);
                        }
                    };

                    // ─── View Drawer ────────────────────────────────────────
                    window.openRegisterDrawer = function (register) {
                        document.getElementById('drawer-id').textContent = register.id;
                        document.getElementById('drawer-user').textContent = register.user_name;
                        document.getElementById('drawer-opened').textContent = register.opened_at_formatted;
                        document.getElementById('drawer-opening-balance').textContent = register.opening_balance_formatted;
                        document.getElementById('drawer-status-badge').innerHTML = register.status_html;
                        document.getElementById('drawer-expenses').textContent = register.expenses_total_formatted || '0.00';
                        document.getElementById('drawer-purchases').textContent = register.purchases_total_formatted || '0.00';
                        document.getElementById('expected-value').textContent = register.expected_closing_formatted || '—';

                        const isOpen = register.status === 'open';
                        document.getElementById('reconciliation-section').classList.toggle('hidden', !isOpen);
                        document.getElementById('btn-close-register').classList.toggle('hidden', !isOpen);

                        if (isOpen) {
                            document.getElementById('close-id').value = register.id;
                        }

                        const tbody = document.getElementById('transactions-body');
                        tbody.innerHTML = '';

                        if (register.transactions && register.transactions.length > 0) {
                            register.transactions.forEach(t => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                                                            <td class="px-4 py-2">${t.happened_at}</td>
                                                                            <td class="px-4 py-2">${t.type_formatted}</td>
                                                                            <td class="px-4 py-2 text-right ${t.amount > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'} font-medium">
                                                                                ${t.amount_formatted}
                                                                            </td>`;
                                tbody.appendChild(row);
                            });
                        } else {
                            tbody.innerHTML = `<tr><td colspan="3" class="text-center py-6 text-gray-500 dark:text-gray-400 italic">{{ __('file.no_transactions_recorded') }}</td></tr>`;
                        }

                        document.body.style.overflow = 'hidden';
                        document.getElementById('register-drawer').classList.remove('hidden');
                    };

                    window.closeDrawer = function () {
                        document.getElementById('register-drawer').classList.add('hidden');
                        document.body.style.overflow = '';
                    };

                    // Close form submit (AJAX)
                    document.getElementById('close-form')?.addEventListener('submit', async function (e) {
                        e.preventDefault();
                        const btn = document.getElementById('btn-close-register');
                        btn.disabled = true;
                        btn.innerHTML = "{{ __('file.closing') }}...";

                        try {
                            const formData = new FormData(this);
                            const id = formData.get('id');
                            const url = '{{ route("cash-registers.close", ":id") }}'.replace(':id', id);

                            const response = await fetch(url, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            const data = await response.json();

                            if (!response.ok) throw new Error(data.message || 'Failed to close register');

                            if (data.success || response.ok) {
                                table.draw(false);
                                closeDrawer();
                                if (typeof showNotification === 'function') showNotification("{{ __('file.success') }}", data.message || "{{ __('file.cash_register_closed_successfully') }}", 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification("{{ __('file.error') }}", data.message || "{{ __('file.failed_to_close_register') }}", 'error');
                            }
                        } catch (err) {
                            if (typeof showNotification === 'function') showNotification(err.message || "{{ __('file.error') }}", 'error');
                            else alert("{{ __('file.error') }}: " + err.message);
                        } finally {
                            btn.disabled = false;
                            btn.innerHTML = "{{ __('file.close_reconcile_register') }}";
                        }
                    });

                    // ─── Open Drawer ────────────────────────────────────────
                    window.openCreateDrawer = function () {
                        document.getElementById('open-drawer').classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                        document.querySelector('#open-form input[name="opening_balance"]')?.focus();
                    };

                    window.closeOpenDrawer = function () {
                        document.getElementById('open-drawer').classList.add('hidden');
                        document.body.style.overflow = '';
                    };

                    document.getElementById('open-form')?.addEventListener('submit', async function (e) {
                        e.preventDefault();

                        const btn = document.getElementById('btn-open-register');
                        if (!btn) return;

                        const originalText = btn.textContent;
                        btn.disabled = true;
                        btn.textContent = "{{ __('file.opening') }}...";

                        try {
                            const formData = new FormData(this);
                            const response = await fetch('{{ route("cash-registers.open") }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            const data = await response.json();

                            if (!response.ok) throw new Error(data.message || 'Failed');

                            if (data.success || response.ok) {
                                table.draw(false);
                                closeOpenDrawer();
                                if (typeof showNotification === 'function') showNotification("{{ __('file.success') }}", data.message || "{{ __('file.cash_register_opened_successfully') }}", 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification("{{ __('file.error') }}", data.message || "{{ __('file.failed_to_open_register') }}", 'error');
                            }
                        } catch (err) {
                            if (typeof showNotification === 'function') showNotification(err.message || "{{ __('file.error') }}", 'error');
                            else alert("{{ __('file.error') }}: " + err.message);
                        } finally {
                            btn.disabled = false;
                            btn.textContent = originalText;
                        }
                    });

                    // ESC key to close drawers
                    document.addEventListener('keydown', e => {
                        if (e.key === 'Escape') {
                            if (!document.getElementById('register-drawer').classList.contains('hidden')) {
                                closeDrawer();
                            }
                            if (!document.getElementById('open-drawer').classList.contains('hidden')) {
                                closeOpenDrawer();
                            }
                        }
                    });
                });
            </script>
        @endpush
@endsection