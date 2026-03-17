@extends('layouts.app')

@section('title', 'Financial Reports')

@section('content')

    <div class="px-4 sm:px-6 lg:px-8 pb-4 sm:py-12 pt-20">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">Financial Reports</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Track sales, payments, outstanding balances, and overdue invoices
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filters</h2>
            <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                <div>
                    <label for="date_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Date Range
                    </label>
                    <input type="text" id="date_range" name="date_range"
                        class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        placeholder="Select date range">
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="button" id="generate_report"
                    class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm">
                    Generate Report
                </button>
                <button type="button" id="reset_filters"
                    class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Reset
                </button>
            </div>
        </div>

        <div id="loading" class="hidden text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <p class="text-gray-500 dark:text-gray-400 mt-3">Loading financial data...</p>
        </div>

        <div id="report_content" class="hidden space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Total Sales</h3>
                        <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold" id="total_sales">{{ $currency_code }}0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Total Paid</h3>
                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="total_paid">{{ $currency_code }}0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Outstanding</h3>
                        <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400" id="outstanding">{{ $currency_code }}0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Overdue Invoices</h3>
                        <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="overdue_count">0</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Revenue Over Time</h3>
                    <div class="h-64">
                        <canvas id="revenue_chart"></canvas>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Methods</h3>
                    <div class="h-64">
                        <canvas id="payment_methods_chart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Invoices</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-max">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Invoice #</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Patient</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Date</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Total</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Paid</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Balance</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody id="recent_invoices_table" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Payments</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-max">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Invoice #</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Patient</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Amount</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Date</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Method</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Received By</th>
                            </tr>
                        </thead>
                        <tbody id="recent_payments_table" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Overdue Invoices</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-max">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Invoice #</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Patient</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Due Date</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Days Overdue</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Balance Due</th>
                            </tr>
                        </thead>
                        <tbody id="overdue_invoices_table" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let revenueChart = null;
                let paymentMethodsChart = null;

                const dateRangePicker = flatpickr("#date_range", {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    defaultDate: [
                        new Date(new Date().setMonth(new Date().getMonth() - 3)),
                        new Date()
                    ]
                });

                document.getElementById('generate_report').addEventListener('click', generateReport);

                document.getElementById('reset_filters').addEventListener('click', function () {
                    dateRangePicker.clear();
                    dateRangePicker.setDate([
                        new Date(new Date().setMonth(new Date().getMonth() - 3)),
                        new Date()
                    ]);
                    generateReport();
                });

                function generateReport() {
                    const dateRange = document.getElementById('date_range').value;

                    document.getElementById('loading').classList.remove('hidden');
                    document.getElementById('report_content').classList.add('hidden');

                    const params = new URLSearchParams();
                    if (dateRange) params.append('date_range', dateRange);

                    fetch(`{{ route('reports.financial.summary') }}?${params}`)
                        .then(response => response.json())
                        .then(data => {
                            updateSummaryCards(data.summary);
                            updateRevenueChart(data.revenue_by_day);
                            updatePaymentMethodsChart(data.payment_methods);
                            updateRecentInvoices(data.recent_invoices);
                            updateRecentPayments(data.recent_payments);
                            updateOverdueInvoices(data.overdue_invoices);

                            document.getElementById('loading').classList.add('hidden');
                            document.getElementById('report_content').classList.remove('hidden');
                        })
                        .catch(err => {
                            console.error(err);
                            showNotification('Failed to load report.', 'error');
                            document.getElementById('loading').classList.add('hidden');
                        });
                }

                function updateSummaryCards(summary) {
                    document.getElementById('total_sales').textContent = '{{ $currency_code }}' + summary.total_sales;
                    document.getElementById('total_paid').textContent = '{{ $currency_code }}' + summary.total_paid;
                    document.getElementById('outstanding').textContent = '{{ $currency_code }}' + summary.outstanding;
                    document.getElementById('overdue_count').textContent = summary.overdue_count;
                }

                function updateRevenueChart(data) {
                    const ctx = document.getElementById('revenue_chart').getContext('2d');
                    if (revenueChart) revenueChart.destroy();

                    revenueChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: Object.keys(data),
                            datasets: [{
                                label: 'Revenue',
                                data: Object.values(data),
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                tension: 0.3,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });
                }

                function updatePaymentMethodsChart(data) {
                    const ctx = document.getElementById('payment_methods_chart').getContext('2d');
                    if (paymentMethodsChart) paymentMethodsChart.destroy();

                    paymentMethodsChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(data),
                            datasets: [{
                                data: Object.values(data),
                                backgroundColor: [
                                    'rgb(34, 197, 94)', 'rgb(59, 130, 246)', 'rgb(249, 115, 22)',
                                    'rgb(139, 92, 246)', 'rgb(234, 179, 8)', 'rgb(239, 68, 68)'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                }

                function updateRecentInvoices(invoices) {
                    const tbody = document.getElementById('recent_invoices_table');
                    tbody.innerHTML = invoices.length ? '' : '<tr><td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No invoices found</td></tr>';

                    invoices.forEach(inv => {
                        tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-medium">${inv.invoice_number}</td>
                            <td class="px-6 py-4 text-sm">${inv.patient}</td>
                            <td class="px-6 py-4 text-sm">${inv.date}</td>
                            <td class="px-6 py-4 text-sm">{{ $currency_code }}${inv.total}</td>
                            <td class="px-6 py-4 text-sm text-green-600">{{ $currency_code }}${inv.paid}</td>
                            <td class="px-6 py-4 text-sm text-orange-600">{{ $currency_code }}${inv.balance}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${inv.status_class}">
                                    ${inv.status}
                                </span>
                            </td>
                        </tr>
                    `;
                    });
                }

                function updateRecentPayments(payments) {
                    const tbody = document.getElementById('recent_payments_table');
                    tbody.innerHTML = payments.length ? '' : '<tr><td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No payments found</td></tr>';

                    payments.forEach(pmt => {
                        tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-medium">${pmt.invoice_number}</td>
                            <td class="px-6 py-4 text-sm">${pmt.patient}</td>
                            <td class="px-6 py-4 text-sm font-medium text-green-600">{{ $currency_code }}${pmt.amount}</td>
                            <td class="px-6 py-4 text-sm">${pmt.date}</td>
                            <td class="px-6 py-4 text-sm">${pmt.method}</td>
                            <td class="px-6 py-4 text-sm">${pmt.user}</td>
                        </tr>
                    `;
                    });
                }

                function updateOverdueInvoices(invoices) {
                    const tbody = document.getElementById('overdue_invoices_table');
                    tbody.innerHTML = invoices.length ? '' : '<tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No overdue invoices</td></tr>';

                    invoices.forEach(inv => {
                        tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-medium">${inv.invoice_number}</td>
                            <td class="px-6 py-4 text-sm">${inv.patient}</td>
                            <td class="px-6 py-4 text-sm">${inv.due_date}</td>
                            <td class="px-6 py-4 text-sm text-red-600 font-medium">${inv.days_overdue}</td>
                            <td class="px-6 py-4 text-sm font-medium text-orange-600">{{ $currency_code }}${inv.balance}</td>
                        </tr>
                    `;
                    });
                }

                generateReport();
            });
        </script>
    @endpush

@endsection