@extends('layouts.app')

@section('title', __('file.therapist_sales_report') ?? 'Therapist Sales Report')

@section('content')

    <div class="px-4 sm:px-6 lg:px-8 pb-4 sm:py-12 pt-20">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.therapist_sales_report') ?? 'Therapist Monthly Sales Report' }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('file.track_therapist_sales') ?? 'View a daily breakdown of a therapist\'s appointments and sales for a selected month.' }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.filters') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="doctor_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.therapist') }}
                    </label>
                    <select id="doctor_filter" name="doctor_filter"
                        class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">{{ __('file.select_therapist') }}</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="month_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.month') }}
                    </label>
                    <input type="month" id="month_filter" name="month_filter" value="{{ date('Y-m') }}"
                        class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition [color-scheme:light] dark:[color-scheme:dark]">
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

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">{{ __('file.days_worked') ?? 'Days Worked' }}</h3>
                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="p-4 pt-0">
                        <div class="text-2xl font-bold text-gray-700 dark:text-gray-300" id="total_days_worked">0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">{{ __('file.total_appointments') ?? 'Total Appointments' }}</h3>
                        <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div class="p-4 pt-0">
                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400" id="total_appointments">0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">{{ __('file.total_cash') ?? 'Total Cash' }}</h3>
                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="p-4 pt-0">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="total_cash">{{ $currency_code ?? '$' }}0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">{{ __('file.total_card') ?? 'Total Card' }}</h3>
                        <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <div class="p-4 pt-0">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="total_card">{{ $currency_code ?? '$' }}0</div>
                    </div>
                </div>
                
                <div class="border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                    <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">{{ __('file.total_transfer') ?? 'Total Transfer' }}</h3>
                        <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    </div>
                    <div class="p-4 pt-0">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400" id="total_transfer">{{ $currency_code ?? '$' }}0</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.daily_breakdown') ?? 'Daily Breakdown' }}</h3>
                </div>
                <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                    <table class="w-full min-w-max">
                        <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('file.date') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('file.appointments') ?? 'Appointments' }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('file.cash') ?? 'Cash' }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('file.card') ?? 'Card' }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('file.bank_transfer') ?? 'Transfer' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody id="daily_data_table" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                document.getElementById('generate_report').addEventListener('click', generateReport);

                document.getElementById('reset_filters').addEventListener('click', function () {
                    document.getElementById('doctor_filter').value = "";
                    document.getElementById('month_filter').value = "{{ date('Y-m') }}";
                    document.getElementById('report_content').classList.add('hidden');
                });

                function generateReport() {
                    const doctorId = document.getElementById('doctor_filter').value;
                    const month = document.getElementById('month_filter').value;

                    if (!doctorId || !month) {
                        alert("{{ __('file.please_select_therapist_and_month') ?? 'Please select a therapist and a month.' }}");
                        return;
                    }

                    document.getElementById('loading').classList.remove('hidden');
                    document.getElementById('report_content').classList.add('hidden');

                    const params = new URLSearchParams();
                    params.append('doctor_id', doctorId);
                    params.append('month', month);
                    
                    fetch(`{{ route('reports.therapist-sales.data') }}?${params}`)
                        .then(response => response.json())
                        .then(data => {
                            updateSummaryCards(data.summary);
                            updateDailyTable(data.daily_data);

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
                    document.getElementById('total_days_worked').textContent = summary.total_days_worked;
                    document.getElementById('total_appointments').textContent = summary.total_appointments;
                    
                    const formatCurrency = (val) => '{{ $currency_code ?? '$' }}' + parseFloat(val).toFixed(2);
                    
                    document.getElementById('total_cash').textContent = formatCurrency(summary.total_cash);
                    document.getElementById('total_card').textContent = formatCurrency(summary.total_card);
                    document.getElementById('total_transfer').textContent = formatCurrency(summary.total_transfer);
                }

                function updateDailyTable(dailyData) {
                    const tbody = document.getElementById('daily_data_table');
                    tbody.innerHTML = dailyData.length ? '' : '<tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_matching_records') }}</td></tr>';

                    const formatCurrency = (val) => '{{ $currency_code ?? '$' }}' + parseFloat(val).toFixed(2);

                    dailyData.forEach(item => {
                        tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">${item.date}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">${item.appointments}</td>
                            <td class="px-6 py-4 text-sm font-medium text-green-600 dark:text-green-400">${formatCurrency(item.cash)}</td>
                            <td class="px-6 py-4 text-sm font-medium text-blue-600 dark:text-blue-400">${formatCurrency(item.card)}</td>
                            <td class="px-6 py-4 text-sm font-medium text-orange-600 dark:text-orange-400">${formatCurrency(item.transfer)}</td>
                        </tr>
                    `;
                    });
                }
            });
        </script>
    @endpush

@endsection
