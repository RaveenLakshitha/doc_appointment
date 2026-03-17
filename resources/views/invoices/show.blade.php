@extends('layouts.app')

@section('title', __('file.invoice') . ' ' . $invoice->invoice_number)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 pb-6 sm:py-12 pt-20"
     x-data="{
         openPaymentDrawer: false,
         balanceDue: {{ $invoice->balance_due }},
         currency: '{{ $currency_code }}'
     }">

    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('invoices.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                {{ __('file.invoices') }}
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white font-medium">
                {{ $invoice->invoice_number }}
            </span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.invoice') }} {{ $invoice->invoice_number }}
                </h1>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full
                        @switch($invoice->status)
                            @case('paid')           bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 @break
                            @case('partially_paid') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 @break
                            @case('overdue')        bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 @break
                            @default                bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 @break
                        @endswitch">
                        {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                    </span>
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                        {{ $invoice->type }}
                    </span>
                    @if($invoice->is_printed)
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                            {{ __('file.printed') }}
                        </span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                            {{ __('file.not_printed') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex gap-3 flex-shrink-0">
                @if($invoice->balance_due > 0)
                    <button @click="openPaymentDrawer = true"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        {{ __('file.add_payment') }}
                    </button>
                @endif
                <a href="{{ route('invoices.print', $invoice) }}?redirect=invoices"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    {{ __('file.print') }}
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                        <div class="text-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.invoice_date') }}</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $invoice->invoice_date->format('d M') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $invoice->invoice_date->format('Y') }}</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.total') }}</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $currency_code }} {{ number_format($invoice->total, 2) }}</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.paid_amount') }}</div>
                            <div class="text-lg font-semibold text-green-600 dark:text-green-400">{{ $currency_code }} {{ number_format($invoice->paid_amount, 2) }}</div>
                        </div>
                        <div class="text-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/50 @if($invoice->balance_due > 0) bg-red-50 dark:bg-red-950/30 @endif">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('file.balance_due') }}</div>
                            <div class="text-xl font-bold @if($invoice->balance_due > 0) text-red-600 dark:text-red-400 @else text-gray-900 dark:text-white @endif">
                                {{ $currency_code }} {{ number_format($invoice->balance_due, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('file.items') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.description') }}</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.unit_price') }}</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.qty') }}</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.total') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($invoice->items as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $item->description }}
                                    @if($item->itemable_type === 'App\Models\Treatment' && $item->doctor_id)
                                        <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">Dr. {{ $item->doctor->getFullNameAttribute() }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-right text-gray-700 dark:text-gray-300">
                                    {{ $currency_code }} {{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right text-gray-700 dark:text-gray-300">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-gray-900 dark:text-gray-100">
                                    {{ $currency_code }} {{ number_format($item->total, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.subtotal') }}</td>
                                <td class="px-6 py-3 text-right text-base font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $currency_code }} {{ number_format($invoice->subtotal, 2) }}
                                </td>
                            </tr>
                            @if($invoice->tax_amount > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.tax') }}</td>
                                <td class="px-6 py-3 text-right text-base font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $currency_code }} {{ number_format($invoice->tax_amount, 2) }}
                                </td>
                            </tr>
                            @endif
                            @if($invoice->discount_amount > 0)
                            <tr class="text-red-600 dark:text-red-400">
                                <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('file.discount') }}</td>
                                <td class="px-6 py-3 text-right text-base font-semibold">
                                    - {{ $currency_code }} {{ number_format($invoice->discount_amount, 2) }}
                                </td>
                            </tr>
                            @endif
                            <tr class="font-bold border-t-2 border-gray-200 dark:border-gray-700">
                                <td colspan="3" class="px-6 py-4 text-right text-base uppercase text-gray-900 dark:text-gray-100">{{ __('file.grand_total') }}</td>
                                <td class="px-6 py-4 text-right text-xl font-black text-indigo-600 dark:text-indigo-400">
                                    {{ $currency_code }} {{ number_format($invoice->total, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('file.payments') }}</h2>
                </div>
                @if($invoice->payments->isEmpty())
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400 italic">
                        {{ __('file.no_payments_found') }}
                    </div>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.date') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.method') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.reference') }}</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('file.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($invoice->payments as $payment)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $payment->payment_date->format('d M Y') }}
                                    <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $payment->payment_date->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 capitalize">
                                    {{ str_replace('_', ' ', $payment->method) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $payment->reference ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-semibold text-green-600 dark:text-green-400">
                                    {{ $currency_code }} {{ number_format($payment->amount, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            @if($invoice->notes)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('file.notes') }}</h2>
                </div>
                <div class="p-6 text-sm text-gray-600 dark:text-gray-300 whitespace-pre-wrap">
                    {{ $invoice->notes }}
                </div>
            </div>
            @endif

        </div>

        <div class="space-y-6">

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('file.patient') }}</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xl">
                            {{ substr($invoice->patient?->first_name ?? '', 0, 1) }}{{ substr($invoice->patient?->last_name ?? '', 0, 1) }}
                        </div>
                        <div>
                            <a href="{{ route('patients.show', $invoice->patient) }}" class="text-base font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ $invoice->patient?->full_name ?? '—' }}
                            </a>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $invoice->patient?->medical_record_number ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if($invoice->appointment_id)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('file.reference') }}</h2>
                </div>
                <div class="p-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1.5">{{ __('file.appointment') }}</label>
                        <a href="{{ route('appointments.show', $invoice->appointment_id) }}" class="inline-flex items-center gap-2 text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ __('file.appointment_number') }}{{ $invoice->appointment_id }}
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('file.properties') }}</h2>
                </div>
                <div class="p-6 space-y-5 text-sm">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1.5">{{ __('file.created_at') }}</label>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $invoice->created_at->format('d M Y • h:i A') }}</p>
                    </div>
                    @if($invoice->payments->isNotEmpty() && $invoice->payments->last()->user)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1.5">{{ __('file.processed_by') }}</label>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $invoice->payments->last()->user->name }}</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentForm = document.getElementById('payment-form');
        if (paymentForm) {
            paymentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const submitBtn = document.querySelector('button[form="' + form.id + '"]');
                const originalBtnText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __("file.processing") }}';

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(async response => {
                    const isJson = response.headers.get('content-type')?.includes('application/json');
                    const data = isJson ? await response.json() : null;

                    if (!response.ok) {
                        const error = (data && data.message) || response.statusText;
                        throw new Error(error);
                    }
                    return data;
                })
                .then(data => {
                    if (data && data.success) {
                        window.location.replace(window.location.href);
                    } else {
                        alert(data ? data.message : 'Something went wrong');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                })
                .catch(error => {
                    console.error('Payment Error:', error);
                    alert(error.message || 'An error occurred. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
            });
        }
    });
</script>
@endpush

    <!-- Add Payment Drawer -->
    <div x-show="openPaymentDrawer" class="fixed inset-0 z-[100] overflow-hidden">
        <div x-show="openPaymentDrawer"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-gray-900/70 dark:bg-black/80 backdrop-blur-sm"
             @click="openPaymentDrawer = false"></div>

        <div class="fixed inset-y-0 right-0 w-full max-w-md">
            <div x-show="openPaymentDrawer"
                 x-transition:enter="transform transition ease-in-out duration-500"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-500"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="h-full flex flex-col bg-white dark:bg-gray-800 shadow-2xl">

                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-900/50">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ __('file.add_payment') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $invoice->invoice_number }}
                        </p>
                    </div>
                    <button @click="openPaymentDrawer = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-8">
                    <form id="payment-form" action="{{ route('invoices.payments.store', $invoice) }}" method="POST" class="space-y-7">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.amount') }} ({{ $currency_code ?? 'LKR' }})
                            </label>
                            <input type="number" name="amount" step="0.01" :max="balanceDue" :value="balanceDue" required
                                   class="w-full px-4 py-3 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition">
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('file.balance_due') }}: <span class="font-medium" x-text="currency + ' ' + balanceDue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.date') }}
                            </label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                                   class="w-full px-4 py-3 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.method') }}
                            </label>
                            <select name="method" required
                                    class="w-full px-4 py-3 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-900 dark:text-white">
                                <option value="cash">{{ __('file.cash') }}</option>
                                <option value="card">{{ __('file.card') }}</option>
                                <option value="bank_transfer">{{ __('file.bank_transfer') }}</option>
                                <option value="cheque">{{ __('file.cheque') }}</option>
                                <option value="other">{{ __('file.other') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.reference') }}
                            </label>
                            <input type="text" name="reference" placeholder="{{ __('file.reference_receipt_optional') }}"
                                   class="w-full px-4 py-3 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.notes') }}
                            </label>
                            <textarea name="notes" rows="4"
                                      class="w-full px-4 py-3 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-900 dark:text-white resize-y"></textarea>
                        </div>
                    </form>
                </div>

                <div class="px-6 py-5 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex gap-4">
                        <button @click="openPaymentDrawer = false"
                                class="flex-1 py-3 px-6 text-base font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            {{ __('file.cancel') }}
                        </button>
                        <button type="submit" form="payment-form"
                                class="flex-1 py-3 px-6 text-base font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition shadow-md">
                            {{ __('file.record_payment') }}
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection