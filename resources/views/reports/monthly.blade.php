@extends('layouts.app')

@section('title', __('file.monthly_report') ?? 'Monthly Report')

@section('content')

    <style>
        .month-select { appearance: none; -webkit-appearance: none; }
    </style>

    {{-- ── Main Screen UI ── --}}
    <div class="px-4 sm:px-6 lg:px-8 pb-4 sm:py-12 pt-20">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                {{ __('file.monthly_report') ?? 'Monthly Report' }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('file.track_therapist_monthly') ?? 'View a monthly breakdown of all therapists\' sales and conciliations.' }}
            </p>
        </div>

        {{-- Filter card --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('file.filters') }}</h2>
            <div class="flex flex-wrap gap-4 items-end">

                {{-- Language-aware Month + Year dropdowns --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.month') }}</label>
                    <div class="flex gap-2">
                        @php
                            $locale    = app()->getLocale();
                            $months_en = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                            $months_es = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                            $months    = $locale === 'es' ? $months_es : $months_en;
                        @endphp
                        <select id="month_select"
                            class="month-select px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($months as $i => $name)
                                <option value="{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}"
                                    {{ (int)date('m') === ($i+1) ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        <select id="year_select"
                            class="month-select px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="flex gap-2 ml-auto">
                    <button type="button" id="generate_report"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm">
                        {{ __('file.generate_report') }}
                    </button>
                    <button type="button" id="reset_filters"
                        class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        {{ __('file.reset_filters') ?? 'Reset' }}
                    </button>
                    <button type="button" id="print_btn" onclick="triggerPrint()"
                        style="display:none;"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-slate-700 hover:bg-slate-900 dark:bg-slate-600 dark:hover:bg-slate-500 rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        {{ __('file.print') ?? 'Print' }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Loading spinner --}}
        <div id="loading" class="hidden text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <p class="text-gray-500 dark:text-gray-400 mt-3">{{ __('file.loading_report_data') }}</p>
        </div>

        {{-- Report Table --}}
        <div id="report_content" class="hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ __('file.therapist_breakdown') ?? 'Therapist Breakdown' }}
                        <span id="screen_month_label" class="ml-2 text-sm font-normal text-gray-500"></span>
                    </h3>
                </div>
                <div class="overflow-x-auto max-h-[550px] overflow-y-auto">
                    <table class="w-full min-w-max text-center border-collapse">
                        <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0 z-10">
                            <tr>
                                <th rowspan="2" class="px-2 py-2 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase border-r border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                    {{ __('file.therapist') ?? 'Therapist' }}
                                </th>
                                <th colspan="5" class="px-2 py-2 text-center text-[11px] font-semibold text-teal-600 dark:text-teal-400 uppercase border-r border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                    {{ __('file.sales_list') ?? 'Sales List' }}
                                </th>
                                <th colspan="5" class="px-2 py-2 text-center text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 uppercase border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                    {{ __('file.payroll') ?? 'Conciliation' }}
                                </th>
                            </tr>
                            <tr>
                                {{-- Sales sub-headers --}}
                                <th class="px-2 py-1 text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase border-r border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">{{ __('file.how_many_appointments') ?? 'Appts' }}</th>
                                <th class="px-2 py-1 text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase border-r border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">{{ __('file.cash') ?? 'Cash' }} ({{ $currency_code ?? '$' }})</th>
                                <th class="px-2 py-1 text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase border-r border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">{{ __('file.card') ?? 'Card' }} ({{ $currency_code ?? '$' }})</th>
                                <th class="px-2 py-1 text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase border-r border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">{{ __('file.transfers') ?? 'Transf.' }} ({{ $currency_code ?? '$' }})</th>
                                <th class="px-2 py-1 text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase border-r border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">{{ __('file.how_many_days_worked') ?? 'Days' }}</th>
                                {{-- Payroll sub-headers --}}
                                <th class="px-2 py-1 text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase border-r border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">{{ __('file.how_many_appointments') ?? 'Appts' }}</th>
                                <th class="px-2 py-1 text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase border-r border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">{{ __('file.cash') ?? 'Cash' }} ({{ $currency_code ?? '$' }})</th>
                                <th class="px-2 py-1 text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase border-r border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">{{ __('file.card') ?? 'Card' }} ({{ $currency_code ?? '$' }})</th>
                                <th class="px-2 py-1 text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase border-r border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">{{ __('file.transfers') ?? 'Transf.' }} ({{ $currency_code ?? '$' }})</th>
                                <th class="px-2 py-1 text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">{{ __('file.how_many_days_worked') ?? 'Days' }}</th>
                            </tr>
                        </thead>
                        <tbody id="daily_data_table" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                        <tfoot id="daily_data_footer" class="hidden">
                            <tr class="bg-gray-100 dark:bg-gray-900 border-t-2 border-gray-400 dark:border-gray-500 font-bold">
                                <td class="px-2 py-2 text-left text-xs text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700">{{ __('file.total_overall') ?? 'Total' }}</td>
                                <td class="px-2 py-2 text-xs text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700" id="footer_sales_appts">0</td>
                                <td class="px-2 py-2 text-xs text-green-700 dark:text-green-400 border-r border-gray-200 dark:border-gray-700" id="footer_sales_cash">0</td>
                                <td class="px-2 py-2 text-xs text-blue-700 dark:text-blue-400 border-r border-gray-200 dark:border-gray-700" id="footer_sales_card">0</td>
                                <td class="px-2 py-2 text-xs text-orange-700 dark:text-orange-400 border-r border-gray-200 dark:border-gray-700" id="footer_sales_transfer">0</td>
                                <td class="px-2 py-2 text-xs text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700" id="footer_sales_days">0</td>
                                <td class="px-2 py-2 text-xs text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700" id="footer_payrolls_appts">0</td>
                                <td class="px-2 py-2 text-xs text-green-700 dark:text-green-400 border-r border-gray-200 dark:border-gray-700" id="footer_payrolls_cash">0</td>
                                <td class="px-2 py-2 text-xs text-blue-700 dark:text-blue-400 border-r border-gray-200 dark:border-gray-700" id="footer_payrolls_card">0</td>
                                <td class="px-2 py-2 text-xs text-orange-700 dark:text-orange-400 border-r border-gray-200 dark:border-gray-700" id="footer_payrolls_transfer">0</td>
                                <td class="px-2 py-2 text-xs text-gray-900 dark:text-white" id="footer_payrolls_days">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Locale-aware month names (from server)
        const monthNames   = @json($months);
        const currencyCode = '{{ $currency_code ?? "$" }}';
        const appLocale    = '{{ app()->getLocale() }}';

        // Translated labels (server-side, so they work in the popup window too)
        const T = {
            therapist   : @json(__('file.therapist')               ?? 'Therapist'),
            salesList   : @json(__('file.sales_list')              ?? 'Sales List'),
            payroll     : @json(__('file.payroll')                 ?? 'Conciliation'),
            appts       : @json(__('file.how_many_appointments')   ?? 'Appts'),
            cash        : @json(__('file.cash')                    ?? 'Cash'),
            card        : @json(__('file.card')                    ?? 'Card'),
            transfers   : @json(__('file.transfers')               ?? 'Transf.'),
            days        : @json(__('file.how_many_days_worked')    ?? 'Days'),
            total       : @json(__('file.total_overall')           ?? 'Total'),
            report      : @json(__('file.monthly_report')          ?? 'Monthly Report'),
            breakdown   : @json(__('file.therapist_breakdown')     ?? 'Therapist Breakdown'),
            generatedOn : @json(__('file.generated_on')            ?? 'Generated on'),
        };

        // Cache last fetched data for printing
        let _data    = [];
        let _summary = {};

        /* ── Print popup ─────────────────────────────────── */
        function triggerPrint() {
            const month     = document.getElementById('month_select').value;
            const year      = document.getElementById('year_select').value;
            const monthName = monthNames[parseInt(month) - 1];
            const genDate   = new Date().toLocaleDateString(
                appLocale === 'es' ? 'es-MX' : 'en-US',
                { day: '2-digit', month: 'long', year: 'numeric' }
            );
            const fmt = v => parseFloat(v || 0).toFixed(2);
            const cur = currencyCode;

            // Body rows
            let rows = '';
            _data.forEach((item, i) => {
                const bg = i % 2 ? 'background:#f8fafc;' : '';
                rows += `<tr style="${bg}">
                    <td style="text-align:left;font-weight:600;padding:4px 6px;border:1px solid #d1d5db;">${item.name}</td>
                    <td>${item.sales.appointments || '-'}</td>
                    <td>${item.sales.cash   > 0 ? fmt(item.sales.cash)   : '-'}</td>
                    <td>${item.sales.card   > 0 ? fmt(item.sales.card)   : '-'}</td>
                    <td>${item.sales.transfer > 0 ? fmt(item.sales.transfer) : '-'}</td>
                    <td>${item.sales.days_worked || '-'}</td>
                    <td>${item.payrolls.count   || '-'}</td>
                    <td>${item.payrolls.cash   > 0 ? fmt(item.payrolls.cash)   : '-'}</td>
                    <td>${item.payrolls.card   > 0 ? fmt(item.payrolls.card)   : '-'}</td>
                    <td>${item.payrolls.transfer > 0 ? fmt(item.payrolls.transfer) : '-'}</td>
                    <td>${item.payrolls.days_worked || '-'}</td>
                </tr>`;
            });

            // Footer totals row
            const s = _summary;
            const footerRow = `<tr style="background:#1e293b;color:#fff;font-weight:700;-webkit-print-color-adjust:exact;print-color-adjust:exact;">
                <td style="text-align:left;padding:5px 6px;border:1px solid #334155;">${T.total}</td>
                <td style="padding:5px 6px;border:1px solid #334155;">${s.sales?.appointments ?? 0}</td>
                <td style="padding:5px 6px;border:1px solid #334155;">${fmt(s.sales?.cash)}</td>
                <td style="padding:5px 6px;border:1px solid #334155;">${fmt(s.sales?.card)}</td>
                <td style="padding:5px 6px;border:1px solid #334155;">${fmt(s.sales?.transfer)}</td>
                <td style="padding:5px 6px;border:1px solid #334155;">${s.total_days_worked ?? 0}</td>
                <td style="padding:5px 6px;border:1px solid #334155;">${s.payrolls?.count ?? 0}</td>
                <td style="padding:5px 6px;border:1px solid #334155;">${fmt(s.payrolls?.cash)}</td>
                <td style="padding:5px 6px;border:1px solid #334155;">${fmt(s.payrolls?.card)}</td>
                <td style="padding:5px 6px;border:1px solid #334155;">${fmt(s.payrolls?.transfer)}</td>
                <td style="padding:5px 6px;border:1px solid #334155;">${s.total_days_paid ?? 0}</td>
            </tr>`;

            const html = `<!DOCTYPE html>
<html lang="${appLocale}">
<head>
<meta charset="UTF-8">
<title>${T.report} — ${monthName} ${year}</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:Arial,sans-serif;font-size:9pt;color:#111;background:#fff;padding:8mm;}

/* Report header */
.rh{display:flex;justify-content:space-between;align-items:flex-start;
    padding-bottom:8px;border-bottom:2.5px solid #1e293b;margin-bottom:12px;}
.rh-left h1{font-size:15pt;font-weight:700;color:#1e293b;margin-bottom:2px;}
.rh-left p{font-size:8.5pt;color:#64748b;}
.rh-right{text-align:right;font-size:8.5pt;color:#64748b;line-height:1.7;}
.rh-right strong{color:#1e293b;font-size:10pt;}

/* Table */
table{width:100%;border-collapse:collapse;font-size:8pt;}
thead th{
    padding:5px 5px;text-align:center;
    border:1px solid #334155;font-size:7pt;
    text-transform:uppercase;letter-spacing:.04em;
    -webkit-print-color-adjust:exact;print-color-adjust:exact;
}
th.th-left{text-align:left;}
th.h-main{background:#1e293b;color:#fff;}
th.h-sales{background:#0f766e;color:#fff;}
th.h-payroll{background:#4338ca;color:#fff;}
tbody td{padding:4px 5px;text-align:center;border:1px solid #d1d5db;color:#111;}

/* Page setup — A4 Landscape */
@page{size:A4 landscape;margin:10mm;}
table{page-break-inside:auto;}
tr{page-break-inside:avoid;}
thead{display:table-header-group;}
tfoot{display:table-footer-group;}
</style>
</head>
<body>
<div class="rh">
    <div class="rh-left">
        <h1>${T.breakdown}</h1>
        <p>${T.report}</p>
    </div>
    <div class="rh-right">
        <strong>${monthName} ${year}</strong><br>
        ${T.generatedOn}: ${genDate}
    </div>
</div>
<table>
    <thead>
        <tr>
            <th rowspan="2" class="th-left h-main">${T.therapist}</th>
            <th colspan="5" class="h-sales">${T.salesList}</th>
            <th colspan="5" class="h-payroll">${T.payroll}</th>
        </tr>
        <tr>
            <th class="h-main">${T.appts}</th>
            <th class="h-main">${T.cash} (${cur})</th>
            <th class="h-main">${T.card} (${cur})</th>
            <th class="h-main">${T.transfers} (${cur})</th>
            <th class="h-main">${T.days}</th>
            <th class="h-main">${T.appts}</th>
            <th class="h-main">${T.cash} (${cur})</th>
            <th class="h-main">${T.card} (${cur})</th>
            <th class="h-main">${T.transfers} (${cur})</th>
            <th class="h-main">${T.days}</th>
        </tr>
    </thead>
    <tbody>${rows}</tbody>
    <tfoot>${footerRow}</tfoot>
</table>
</body>
</html>`;

            const iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            document.body.appendChild(iframe);

            const doc = iframe.contentDocument || iframe.contentWindow.document;
            doc.open();
            doc.write(html);
            doc.close();

            iframe.contentWindow.focus();
            setTimeout(() => {
                iframe.contentWindow.print();
                document.body.removeChild(iframe);
            }, 300);
        }

        /* ── Main page logic ─────────────────────────────── */
        document.addEventListener('DOMContentLoaded', function () {

            document.getElementById('generate_report').addEventListener('click', generateReport);

            document.getElementById('reset_filters').addEventListener('click', function () {
                document.getElementById('report_content').classList.add('hidden');
                document.getElementById('print_btn').style.display = 'none';
            });

            function generateReport() {
                const month  = document.getElementById('month_select').value;
                const year   = document.getElementById('year_select').value;
                const period = year + '-' + month;

                document.getElementById('loading').classList.remove('hidden');
                document.getElementById('report_content').classList.add('hidden');
                document.getElementById('print_btn').style.display = 'none';

                const monthName = monthNames[parseInt(month) - 1];
                document.getElementById('screen_month_label').textContent = '— ' + monthName + ' ' + year;

                fetch(`{{ route('reports.monthly.data') }}?month=${period}`)
                    .then(r => {
                        if (!r.ok) throw new Error('Network error');
                        return r.json();
                    })
                    .then(data => {
                        _data    = data.daily_data;
                        _summary = data.summary;
                        updateFooter(data.summary, data.daily_data);
                        buildTable(data.daily_data);
                        document.getElementById('loading').classList.add('hidden');
                        document.getElementById('report_content').classList.remove('hidden');
                        document.getElementById('print_btn').style.display = 'inline-flex';
                    })
                    .catch(err => {
                        console.error(err);
                        if (typeof showNotification === 'function')
                            showNotification("{{ __('file.something_went_wrong') }}", 'error');
                        document.getElementById('loading').classList.add('hidden');
                    });
            }

            function updateFooter(summary, rows) {
                const el = document.getElementById('daily_data_footer');
                if (!rows.length) { el.classList.add('hidden'); return; }
                el.classList.remove('hidden');
                const fmt = v => parseFloat(v || 0).toFixed(2);
                document.getElementById('footer_sales_appts').textContent    = summary.sales.appointments;
                document.getElementById('footer_sales_cash').textContent     = fmt(summary.sales.cash);
                document.getElementById('footer_sales_card').textContent     = fmt(summary.sales.card);
                document.getElementById('footer_sales_transfer').textContent = fmt(summary.sales.transfer);
                document.getElementById('footer_sales_days').textContent     = summary.total_days_worked;
                document.getElementById('footer_payrolls_appts').textContent    = summary.payrolls.count || 0;
                document.getElementById('footer_payrolls_cash').textContent     = fmt(summary.payrolls.cash);
                document.getElementById('footer_payrolls_card').textContent     = fmt(summary.payrolls.card);
                document.getElementById('footer_payrolls_transfer').textContent = fmt(summary.payrolls.transfer);
                document.getElementById('footer_payrolls_days').textContent     = summary.total_days_paid;
            }

            function buildTable(dailyData) {
                const tbody = document.getElementById('daily_data_table');
                if (!dailyData.length) {
                    tbody.innerHTML = `<tr><td colspan="11" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_matching_records') }}</td></tr>`;
                    return;
                }
                const fmt = v => parseFloat(v || 0).toFixed(2);
                tbody.innerHTML = '';
                dailyData.forEach(item => {
                    tbody.innerHTML += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-2 py-2 text-left text-xs font-semibold text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700">${item.name}</td>
                        <td class="px-2 py-2 text-xs text-gray-600 dark:text-gray-300 border-r border-gray-200 dark:border-gray-700">${item.sales.appointments || '-'}</td>
                        <td class="px-2 py-2 text-xs font-medium text-green-600 dark:text-green-400 border-r border-gray-200 dark:border-gray-700">${item.sales.cash   > 0 ? fmt(item.sales.cash)   : '-'}</td>
                        <td class="px-2 py-2 text-xs font-medium text-blue-600  dark:text-blue-400  border-r border-gray-200 dark:border-gray-700">${item.sales.card   > 0 ? fmt(item.sales.card)   : '-'}</td>
                        <td class="px-2 py-2 text-xs font-medium text-orange-600 dark:text-orange-400 border-r border-gray-200 dark:border-gray-700">${item.sales.transfer > 0 ? fmt(item.sales.transfer) : '-'}</td>
                        <td class="px-2 py-2 text-xs text-gray-600 dark:text-gray-300 border-r border-gray-200 dark:border-gray-700">${item.sales.days_worked || '-'}</td>
                        <td class="px-2 py-2 text-xs text-gray-600 dark:text-gray-300 border-r border-gray-200 dark:border-gray-700">${item.payrolls.count   || '-'}</td>
                        <td class="px-2 py-2 text-xs font-medium text-green-600 dark:text-green-400 border-r border-gray-200 dark:border-gray-700">${item.payrolls.cash   > 0 ? fmt(item.payrolls.cash)   : '-'}</td>
                        <td class="px-2 py-2 text-xs font-medium text-blue-600  dark:text-blue-400  border-r border-gray-200 dark:border-gray-700">${item.payrolls.card   > 0 ? fmt(item.payrolls.card)   : '-'}</td>
                        <td class="px-2 py-2 text-xs font-medium text-orange-600 dark:text-orange-400 border-r border-gray-200 dark:border-gray-700">${item.payrolls.transfer > 0 ? fmt(item.payrolls.transfer) : '-'}</td>
                        <td class="px-2 py-2 text-xs text-gray-600 dark:text-gray-300">${item.payrolls.days_worked || '-'}</td>
                    </tr>`;
                });
            }
        });
    </script>
    @endpush

@endsection
