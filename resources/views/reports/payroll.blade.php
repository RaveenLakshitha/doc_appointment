@extends('layouts.app')

@section('title', __('file.report_payroll'))

@section('content')

    <div class="px-4 sm:px-6 lg:px-8 pb-4 sm:py-12 pt-20">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.report_payroll') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('file.track_payroll_payments') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.filters') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="date_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.date_range') }}
                    </label>
                    <input type="text" id="date_range" name="date_range"
                        class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        placeholder="{{ __('file.select_date_range') }}">
                </div>
                <div>
                    <label for="recipient_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.recipient') }}
                    </label>
                    <select id="recipient_filter" name="recipient_filter"
                        class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">{{ __('file.all') }}</option>
                        <optgroup label="Doctors">
                            @foreach($doctors as $doctor)
                                <option value="doctor_{{ $doctor->id }}">{{ $doctor->full_name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Employees">
                            @foreach($employees as $employee)
                                <option value="employee_{{ $employee->id }}">{{ $employee->full_name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="button" id="generate_report"
                    class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm">
                    {{ __('file.generate_report') }}
                </button>
                <button type="button" id="reset_filters"
                    class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    {{ __('file.reset_filters') }}
                </button>
            </div>
        </div>

        <div id="loading" class="hidden text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <p class="text-gray-500 dark:text-gray-400 mt-3">{{ __('file.loading_report_data') }}</p>
        </div>

        <div id="report_content" class="hidden space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">{{ __('file.total_payroll_paid') }}</h3>
                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="total_paid">{{ $currency_code ?? '$' }}0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">{{ __('file.total_transactions') }}</h3>
                        <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="total_transactions">0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">{{ __('file.therapists_paid') }}</h3>
                        <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400" id="total_therapists">0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">{{ __('file.employees_paid') }}</h3>
                        <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400" id="total_employees">0</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Breakdown Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.recipient_breakdown') }}</h3>
                    </div>
                    <div class="overflow-x-auto max-h-64 overflow-y-auto">
                        <table class="w-full min-w-max">
                            <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('file.recipient') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('file.type') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('file.transactions') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('file.amount') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="breakdown_table" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment Methods Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.payment_methods') }}</h3>
                    <div class="h-64">
                        <canvas id="payment_methods_chart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.recent_payments') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-max">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('file.recipient') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('file.type') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('file.amount') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('file.date') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('file.method') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('file.recorded_by') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody id="recent_payments_table" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
        <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let paymentMethodsChart = null;

                const currentLang = document.documentElement.lang || 'en';
                const calendarLocale = currentLang.startsWith('es') ? 'es' : 'default';

                const dateRangePicker = flatpickr("#date_range", {
                    locale: calendarLocale,
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
                    document.getElementById('recipient_filter').value = "";
                    generateReport();
                });

                function generateReport() {
                    const dateRange = document.getElementById('date_range').value;
                    const recipientFilter = document.getElementById('recipient_filter').value;

                    document.getElementById('loading').classList.remove('hidden');
                    document.getElementById('report_content').classList.add('hidden');

                    const params = new URLSearchParams();
                    if (dateRange) params.append('date_range', dateRange);
                    
                    if (recipientFilter) {
                        const parts = recipientFilter.split('_');
                        params.append('recipient_type', parts[0]);
                        params.append('recipient_id', parts[1]);
                    }

                    fetch(`{{ route('reports.payroll.summary') }}?${params}`)
                        .then(response => response.json())
                        .then(data => {
                            updateSummaryCards(data.summary);
                            updateBreakdownTable(data.breakdown);
                            updatePaymentMethodsChart(data.payment_methods);
                            updateRecentPayments(data.recent_payments);

                            document.getElementById('loading').classList.add('hidden');
                            document.getElementById('report_content').classList.remove('hidden');
                        })
                        .catch(err => {
                            console.error(err);
                            if(typeof showNotification === 'function') {
                                showNotification("{{ __('file.something_went_wrong') }}", 'error');
                            }
                            document.getElementById('loading').classList.add('hidden');
                        });
                }

                function updateSummaryCards(summary) {
                    document.getElementById('total_paid').textContent = '{{ $currency_code ?? '$' }}' + summary.total_paid;
                    document.getElementById('total_transactions').textContent = summary.total_transactions;
                    document.getElementById('total_therapists').textContent = summary.total_therapists;
                    document.getElementById('total_employees').textContent = summary.total_employees;
                }

                function updateBreakdownTable(breakdown) {
                    const tbody = document.getElementById('breakdown_table');
                    tbody.innerHTML = breakdown.length ? '' : '<tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_matching_records') }}</td></tr>';

                    breakdown.forEach(item => {
                        const typeClass = item.type === "{{ __('file.doctor') }}" 
                            ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' 
                            : 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
                            
                        tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">${item.name}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${typeClass}">
                                    ${item.type}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">${item.transactions}</td>
                            <td class="px-6 py-4 text-sm font-medium text-green-600 dark:text-green-400">{{ $currency_code ?? '$' }}${item.total_amount}</td>
                        </tr>
                    `;
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

                function updateRecentPayments(payments) {
                    const tbody = document.getElementById('recent_payments_table');
                    tbody.innerHTML = payments.length ? '' : '<tr><td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_matching_records') }}</td></tr>';

                    payments.forEach(pmt => {
                        const typeClass = pmt.type === "{{ __('file.doctor') }}" 
                            ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' 
                            : 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
                            
                        tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">${pmt.recipient}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${typeClass}">
                                    ${pmt.type}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-green-600 dark:text-green-400">{{ $currency_code ?? '$' }}${pmt.amount}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">${pmt.date}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">${pmt.method}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">${pmt.user}</td>
                        </tr>
                    `;
                    });
                }

                generateReport();
            });
        </script>
    @endpush

@endsection
