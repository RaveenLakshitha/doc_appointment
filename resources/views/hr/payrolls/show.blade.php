@extends('layouts.app')

@section('title', __('file.payroll_details'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class="space-y-6">

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.payroll_receipt') }}
                        #{{ $payroll->id }}</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.processed_on') }}
                        {{ $payroll->date->translatedFormat('F j, Y') }}</p>
                </div>

                <div
                    class="flex flex-row-reverse sm:flex-row gap-3 w-full sm:w-auto justify-between sm:justify-end print:hidden">
                    <a href="{{ route('payrolls.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-600 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('file.back') }}
                    </a>
                    <button type="button" onclick="sendPayrollEmail({{ $payroll->id }})" id="send-email-btn"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ __('file.send_email') ?? 'Send Email' }}
                    </button>
                    <button onclick="printReceipt()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded-lg text-sm font-medium transition shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        {{ __('file.print_receipt') ?? 'Print Receipt' }}
                    </button>
                    <iframe id="print-iframe" style="display:none;"></iframe>
                </div>
            </div>

            @push('scripts')
            <script>
                function printReceipt() {
                    const iframe = document.getElementById('print-iframe');
                    iframe.src = "{{ route('payrolls.print', $payroll->id) }}";
                    
                    iframe.onload = function() {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    };
                }

                function sendPayrollEmail(id) {
                    const btn = document.getElementById('send-email-btn');
                    const originalHtml = btn.innerHTML;
                    
                    btn.disabled = true;
                    btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> ' + ('{{ __("file.processing") ?? "Processing..." }}');

                    fetch('{{ route('payrolls.send-email', $payroll->id) }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) throw new Error(data.message || 'Something went wrong');
                        return data;
                    })
                    .then(data => {
                        alert(data.message || 'Email sent successfully');
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    })
                    .catch(error => {
                        console.error('Email Error:', error);
                        alert(error.message || 'An error occurred while sending the email.');
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    });
                }
            </script>
            @endpush

            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 sm:p-8 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Recipient Info -->
                        <div class="space-y-4">
                            <div>
                                <h3
                                    class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">
                                    {{ __('file.recipient') }}</h3>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    @if($payroll->payable)
                                        {{ $payroll->payable->full_name ?: ($payroll->payable->first_name ?: 'Unknown') }}
                                    @else
                                        {{ __('file.deleted_user') ?? 'Deleted User' }} (#{{ $payroll->payable_id }})
                                    @endif
                                </p>
                                <div class="mt-1">
                                    @if($payroll->payable_type === 'App\Models\Doctor')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                            {{ __('file.therapist') }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            {{ __('file.employee') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($payroll->payable_type === 'App\\Models\\Doctor')
    <div class="grid grid-cols-2 gap-4 border-t border-gray-100 dark:border-gray-700 pt-4">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-widest">{{ __('file.id') }}</p>
            <p class="text-sm font-medium text-gray-900 dark:text-white">#{{ $payroll->id }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-widest">{{ __('file.status') }}</p>
            <p class="text-sm font-bold text-green-600 dark:text-green-400 uppercase tracking-tight">{{ __('file.' . strtolower($payroll->status)) }}</p>
        </div>
    </div>
@endif
                        </div>

                        <!-- Payment Info -->
                        <div
                            class="bg-gray-50 dark:bg-gray-900/40 rounded-2xl p-6 border border-gray-100 dark:border-gray-700/50">
                            <h3
                                class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">
                                {{ __('file.payment_summary') }}</h3>
                            <dl class="space-y-4">
                                <div class="flex justify-between items-baseline">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.method') }}</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-white uppercase">
                                        {{ $payroll->payment_method }}</dd>
                                </div>
                                <div class="flex justify-between items-baseline">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.processing_date') }}</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $payroll->date->format('Y-m-d') }}</dd>
                                </div>
                                <div
                                    class="pt-4 border-t border-gray-200 dark:border-gray-700 space-y-4">
                                    <div class="flex justify-between items-center">
                                        <dt class="text-base font-semibold text-gray-900 dark:text-white">{{ __('file.total_amount') }}</dt>
                                        <dd class="text-xl font-black text-indigo-600 dark:text-indigo-400">
                                            {{ \App\Models\Setting::getCurrencySymbol() }}{{ number_format($payroll->amount, 2) }}
                                        </dd>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <dt class="text-base font-semibold text-gray-900 dark:text-white">{{ __('file.corresponds_to_therapist') ?? 'Therapist Amount' }}</dt>
                                        <dd class="text-xl font-black text-indigo-600 dark:text-indigo-400">
                                            {{ \App\Models\Setting::getCurrencySymbol() }}{{ number_format($payroll->therapist_amount, 2) }}
                                        </dd>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <dt class="text-base font-semibold text-gray-900 dark:text-white">{{ __('file.corresponds_to_caped') ?? 'Caped Amount' }}</dt>
                                        <dd class="text-xl font-black text-indigo-600 dark:text-indigo-400">
                                            {{ \App\Models\Setting::getCurrencySymbol() }}{{ number_format($payroll->caped_amount, 2) }}
                                        </dd>
                                    </div>
                                </div>
                            </dl>
                        </div>
                    </div>

                        <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                            <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">
                                {{ __('file.internal_notes') }}</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ $payroll->notes }}</p>
                        </div>

                    <!-- Included Appointments -->
                    @if($payroll->payable_type === 'App\Models\Doctor' && $payroll->appointments->count() > 0)
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">{{ __('file.breakdown_appointments') }}</h3>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $payroll->appointments->count() }}
                                    {{ __('file.items') }}</span>
                            </div>
                            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-900">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                                {{ __('file.apt_no') }}</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                                {{ __('file.date') }}</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                                {{ __('file.patient') }}</th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                                {{ __('file.amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($payroll->appointments as $appointment)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                                    {{ $appointment->appointment_number }}
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono">
                                                    {{ $appointment->scheduled_start ? $appointment->scheduled_start->format('Y-m-d H:i') : '-' }}
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                                    {{ $appointment->patient ? $appointment->patient->full_name : '-' }}
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right font-black">
                                                    {{ \App\Models\Setting::getCurrencySymbol() }}{{ number_format($appointment->pivot->amount, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Receipt Footer (Print Only) -->
                <div class="hidden print:block p-8 border-t border-gray-200 mt-8 text-center text-xs text-gray-400">
                    <p class="mt-1">© {{ date('Y') }} {{ config('app.name') }}. {{ __('file.all_rights_reserved') ?? 'All rights reserved.' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection