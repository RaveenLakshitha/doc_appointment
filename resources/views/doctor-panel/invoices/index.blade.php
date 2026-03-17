@extends('layouts.app')

@section('title', __('file.my_invoices'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.my_invoices') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.invoice_summary') }}
                </p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('file.total_invoiced') }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($stats->total_invoiced ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-50 dark:bg-green-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('file.total_treatment_charges') }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($treatmentCharges ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('file.total_paid') }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($stats->total_paid ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-red-50 dark:bg-red-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('file.balance_due') }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($stats->balance_due ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
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
                                {{ __('file.treatment_charges') }}
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

    <!-- Invoice Modal (Reusing existing pattern if needed) -->
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('doctor-panel.invoices.datatable') }}',
                    columns: [
                        { data: 'invoice_number' },
                        { data: 'patient_name' },
                        { data: 'invoice_date' },
                        { data: 'treatment_charges', className: 'text-right font-medium' },
                        { data: 'total', className: 'text-right font-medium' },
                        { data: 'balance_due', className: 'text-right font-medium' },
                        { data: 'status_html' },
                        {
                            data: null,
                            render: (data, type, row) => {
                                return `
                                            <div class="flex items-center justify-end gap-1">
                                                <button type="button" onclick="openInvoice('${row.show_url}', '${row.print_url}')" class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </button>
                                                <a href="${row.print_url}" target="_blank" class="p-2 text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                </a>
                                            </div>`;
                            },
                            className: 'text-right whitespace-nowrap'
                        }
                    ],
                    language: {
                        search: "",
                        searchPlaceholder: "{{ __('file.search_invoices') }}",
                        lengthMenu: "{{ __('file.show_entries') }}",
                        info: "{{ __('file.showing_entries_invoices') }}",
                        emptyTable: "{{ __('file.no_invoices_found') }}",
                        processing: "{{ __('file.processing') }}"
                    }
                });

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
            });
        </script>
    @endpush
@endsection