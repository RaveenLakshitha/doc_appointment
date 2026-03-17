@extends('layouts.app')

@section('title', __('file.invoices'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20" x-data="{ 
                openPaymentDrawer: false, 
                invoiceId: null, 
                invoiceNumber: '', 
                balanceDue: 0,
                paymentUrl: ''
            }" @open-payment-drawer.window="
                invoiceId = $event.detail.id;
                invoiceNumber = $event.detail.number;
                balanceDue = $event.detail.balance;
                paymentUrl = '{{ url('invoices') }}/' + invoiceId + '/payments';
                openPaymentDrawer = true;
            ">
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.invoices') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_invoices') }}
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

                <a href="{{ route('invoices.pos') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('file.new_pos_sale') }}
                </a>
            </div>
        </div>

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('invoices.bulkDelete') }}" id="bulk-delete-form-el"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
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
                                {{ __('file.invoice_number') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.patient') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.invoice_date') }}
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.total') }}
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.balance_due') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.status') }}
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.printed') }}
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

        <div id="invoice-modal" class="fixed inset-0 z-[60] hidden">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" id="invoice-modal-backdrop"></div>
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative w-full max-w-5xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl transform transition-all overflow-hidden flex flex-col h-[90vh]">
                        <div
                            class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/40">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __('file.invoice_details') }}
                            </h3>
                            <div class="flex items-center gap-4">
                                <button type="button" id="modal-print-btn"
                                    class="text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                </button>
                                <button type="button" id="close-invoice-modal"
                                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex-1 w-full overflow-hidden relative bg-gray-50 dark:bg-gray-950">
                            <div id="modal-loader"
                                class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-gray-900/80 z-10">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                            </div>
                            <iframe id="invoice-iframe" src="" class="w-full h-full border-none"></iframe>
                        </div>
                    </div>
                </div>
            </div>
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
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                            <select id="filter-status"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="">{{ __('file.all_statuses') }}</option>
                                <option value="paid">{{ __('file.paid') }}</option>
                                <option value="partially_paid">{{ __('file.partially_paid') }}</option>
                                <option value="sent">{{ __('file.sent') }}</option>
                                <option value="overdue">{{ __('file.overdue') }}</option>
                            </select>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 uppercase tracking-wider">
                                {{ __('file.invoice_date_range') }}</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.from') }}</label>
                                    <input type="date" id="filter-from"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.to') }}</label>
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

        <!-- Payment Drawer -->
        <div x-show="openPaymentDrawer" class="fixed inset-0 z-[100] overflow-hidden">
            <div x-show="openPaymentDrawer" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
                @click="openPaymentDrawer = false"></div>

            <div class="fixed inset-y-0 right-0 w-full max-w-md">
                <div x-show="openPaymentDrawer" x-transition:enter="transform transition ease-in-out duration-500"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-500"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                    class="h-full flex flex-col bg-white dark:bg-gray-800 shadow-2xl">

                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ __('file.add_payment') }} — <span x-text="invoiceNumber"></span>
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.record_payment_for_invoice') }}
                            </p>
                        </div>
                        <button @click="openPaymentDrawer = false"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto px-5 py-6">
                        <form id="payment-form" :action="paymentUrl" method="POST" class="space-y-6">
                            @csrf

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.amount') }}
                                    ({{ $currency_code }})</label>
                                <input type="number" name="amount" step="0.01" :max="balanceDue" :value="balanceDue"
                                    required
                                    class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent dark:bg-gray-900 dark:text-white">
                                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('file.balance_due') }}: <strong
                                        x-text="balanceDue.toLocaleString('en-US', {minimumFractionDigits: 2})"></strong>
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.date') }}</label>
                                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                                    class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent dark:bg-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.method') }}</label>
                                <select name="method" required
                                    class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent dark:bg-gray-900 dark:text-white">
                                    <option value="cash">{{ __('file.cash') }}</option>
                                    <option value="card">{{ __('file.card') }}</option>
                                    <option value="bank_transfer">{{ __('file.bank_transfer') }}</option>
                                    <option value="cheque">{{ __('file.cheque') }}</option>
                                    <option value="other">{{ __('file.other') }}</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.reference') }}</label>
                                <input type="text" name="reference"
                                    placeholder="{{ __('file.reference_receipt_optional') }}"
                                    class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent dark:bg-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('file.notes') }}</label>
                                <textarea name="notes" rows="4"
                                    class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent dark:bg-gray-900 dark:text-white"></textarea>
                            </div>
                        </form>
                    </div>

                    <div class="px-5 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex gap-3">
                            <button @click="openPaymentDrawer = false"
                                class="flex-1 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                {{ __('file.cancel') }}
                            </button>
                            <button type="submit" form="payment-form"
                                class="flex-1 px-5 py-2.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition shadow-sm">
                                {{ __('file.record_payment') }}
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
                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: {
                        url: '{{ route('invoices.datatable') }}',
                        data: d => {
                            d.status = $('#filter-status').val();
                            d.from = $('#filter-from').val();
                            d.to = $('#filter-to').val();
                        }
                    },
                    columnDefs: [
                        { orderable: false, targets: [0, 8] },
                        { searchable: false, targets: [0, 4, 5, 6, 7, 8] }
                    ],
                    columns: [
                        { data: 'id', render: d => `<input type="checkbox" name="ids[]" value="${d}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`, className: 'text-center' },
                        { data: 'invoice_number' },
                        { data: 'patient_name', render: d => d || '-' },
                        { data: 'invoice_date' },
                        { data: 'total', className: 'text-right font-medium' },
                        { data: 'balance_due', className: 'text-right font-medium' },
                        { data: 'status_html' },
                        { data: 'is_printed_html', className: 'text-center' },
                        {
                            data: null,
                            render: (data, type, row) => {
                                let html = `
                                                <div class="flex items-center justify-end gap-1">
                                                    <a href="${row.show_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </a>
                                                    <a href="${row.print_url}" class="p-2 text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                    </a>`;

                                if (parseFloat(row.balance_due_raw) > 0) {
                                    html += `
                                                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-payment-drawer', { detail: { id: ${row.id}, number: '${row.invoice_number}', balance: ${row.balance_due_raw} } }))"
                                                        class="p-2 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 transition-colors" title="{{ __('file.add_payment') }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                                    </button>`;
                                }

                                html += `
                                                    <button type="button" onclick="confirmDelete('${row.delete_url}')" class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
                                { extend: 'pageLength', className: 'inline-flex items-center gap-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium transition shadow-sm' },
                                {
                                    extend: 'collection', text: "{{ __('file.Export') }}", className: 'bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium', buttons: [
                                        { extend: 'copy', text: "{{ __('file.copy') }}" },
                                        { extend: 'excel', text: 'Excel', filename: 'Invoices_{{ date("Y-m-d") }}' },
                                        { extend: 'csv', text: 'CSV', filename: 'Invoices_{{ date("Y-m-d") }}' },
                                        { extend: 'pdf', text: 'PDF', filename: 'Invoices_{{ date("Y-m-d") }}', title: 'Invoice List' },
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
                        searchPlaceholder: "{{ __('file.search_invoices') }}",
                        lengthMenu: "{{ __('file.show_entries') }}",
                        info: "{{ __('file.showing_entries_invoices') }}",
                        emptyTable: "{{ __('file.no_invoices_found') }}",
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
                    if (!confirm('{{ __("file.confirm_delete_selected_invoices") }}')) return;
                    $.ajax({
                        url: this.action,
                        method: 'POST',
                        data: $(this).serialize() + '&_method=DELETE',
                        success: (res) => {
                            table.draw(false);
                            $('#select-all').prop('checked', false);
                            updateBulkDelete();
                            if (res.success) {
                                if (typeof showNotification === 'function') showNotification('Success', res.message, 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', res.message, 'error');
                                else alert(res.message || 'Bulk delete failed');
                            }
                        },
                        error: (xhr) => {
                            const msg = xhr.responseJSON?.message || 'Bulk delete failed';
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                            else alert(msg);
                        }
                    });
                });

                window.confirmDelete = function (url) {
                    if (!confirm('{{ __("file.confirm_delete_invoice") }}')) return;
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                        success: (res) => {
                            table.draw(false);
                            if (res.success) {
                                if (typeof showNotification === 'function') showNotification('Success', res.message, 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', res.message, 'error');
                                else alert(res.message || 'Delete failed');
                            }
                        },
                        error: (xhr) => {
                            const msg = xhr.responseJSON?.message || 'Delete failed';
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                            else alert(msg);
                        }
                    });
                };

                const filterButton = document.getElementById('filter-button');
                const filterDrawer = document.getElementById('filter-drawer');
                const drawerPanel = document.getElementById('drawer-panel');
                const closeDrawerBtn = document.getElementById('close-drawer');
                const drawerBackdrop = document.getElementById('drawer-backdrop');

                filterButton.addEventListener('click', () => {
                    filterDrawer.classList.remove('hidden');
                    setTimeout(() => drawerPanel.classList.remove('translate-x-full'), 10);
                });

                function closeFilterDrawer() {
                    drawerPanel.classList.add('translate-x-full');
                    setTimeout(() => filterDrawer.classList.add('hidden'), 300);
                }

                closeDrawerBtn.addEventListener('click', closeFilterDrawer);
                drawerBackdrop.addEventListener('click', closeFilterDrawer);

                function updateFilterCount() {
                    const count = [
                        $('#filter-status').val(),
                        $('#filter-from').val(),
                        $('#filter-to').val()
                    ].filter(v => v).length;

                    const el = document.getElementById('filter-count');
                    if (count > 0) {
                        el.textContent = count;
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                }

                $('#apply-filters').on('click', () => {
                    table.draw();
                    closeFilterDrawer();
                    updateFilterCount();
                });

                $('#clear-filters').on('click', () => {
                    $('#filter-status, #filter-from, #filter-to').val('');
                    table.draw();
                    updateFilterCount();
                });

                $('#filter-status, #filter-from, #filter-to').on('change', updateFilterCount);
                updateFilterCount();

                const invoiceModal = document.getElementById('invoice-modal');
                const invoiceIframe = document.getElementById('invoice-iframe');
                const modalLoader = document.getElementById('modal-loader');
                const closeModalBtn = document.getElementById('close-invoice-modal');
                const modalBackdrop = document.getElementById('invoice-modal-backdrop');
                const modalPrintBtn = document.getElementById('modal-print-btn');

                window.openInvoice = function (viewUrl, printUrl) {
                    invoiceIframe.src = viewUrl;
                    modalLoader.style.display = 'flex';
                    invoiceModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    modalPrintBtn.onclick = () => window.open(printUrl, '_blank');
                };

                function closeInvoiceModal() {
                    invoiceModal.classList.add('hidden');
                    invoiceIframe.src = '';
                    document.body.style.overflow = '';
                }

                invoiceIframe.onload = () => modalLoader.style.display = 'none';
                closeModalBtn.onclick = closeInvoiceModal;
                modalBackdrop.onclick = closeInvoiceModal;

                // AJAX Payment Submission
                $('#payment-form').on('submit', function (e) {
                    e.preventDefault();
                    const form = $(this);
                    const submitBtn = $('button[form="' + form.attr('id') + '"]');
                    const originalBtnText = submitBtn.html();

                    submitBtn.prop('disabled', true).html('<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __("file.processing") }}');

                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: function (response) {
                            if (response.success) {
                                // Reset and close drawer
                                form[0].reset();

                                // Use Alpine to close drawer if available, otherwise hide manually
                                if (window.Alpine) {
                                    // Find the Alpine scope for the drawer and close it
                                    // This is a bit hacky but works if the scope is correctly identified
                                    const drawerEl = document.querySelector('[x-data]');
                                    if (drawerEl && drawerEl.__x) {
                                        drawerEl.__x.$data.openPaymentDrawer = false;
                                    }
                                } else {
                                    // Fallback if Alpine is not used correctly
                                    window.dispatchEvent(new CustomEvent('close-payment-drawer'));
                                }

                                // Refresh table
                                table.draw(false);

                                // Show success message
                                if (typeof showNotification === 'function') showNotification('Success', response.message, 'success');
                                else alert(response.message);
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', response.message, 'error');
                                else alert(response.message || 'Error occurred');
                            }
                        },
                        error: function (xhr) {
                            console.error('Payment Error:', xhr);
                            let message = 'Something went wrong';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            } else if (xhr.status === 419) {
                                message = 'Session expired, please refresh the page.';
                            }
                            if (typeof showNotification === 'function') showNotification('Error', message, 'error');
                            else alert(message);
                        },
                        complete: function () {
                            submitBtn.prop('disabled', false).html(originalBtnText);
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection