@extends('layouts.app')

@section('title', __('file.Edit Payroll') ?? 'Edit Conciliation')

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20" x-data="payrollForm()">
    <div class="space-y-6">
        
        <div class=" mb-6">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                <a href="{{ route('payrolls.index') }}"
                    class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">{{ __('file.payrolls') ?? 'Conciliations' }}</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white">{{ __('file.Edit Payroll') ?? 'Edit Conciliation' }}</span>
            </div>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.Edit Payroll') ?? 'Edit Conciliation' }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.edit_payroll_desc') }}</p>
        </div>

        <form action="{{ route('payrolls.update', $payroll->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('file.payroll_information') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('file.complete_payroll_details') }}</p>
                </div>
                
                <div class="p-6 space-y-8">
                    
                    <!-- Recipient Type & Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.recipient_type') }} <span class="text-red-500">*</span></label>
                            <select name="recipient_type" x-model="recipientType" @change="loadAppointments" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-400 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="doctor">{{ __('file.therapist') }}</option>
                                <option value="employee">{{ __('file.employee') }}</option>
                            </select>
                            @error('recipient_type') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Recipient -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.recipient') }} <span class="text-red-500">*</span></label>
                            
                            <!-- Doctor Select -->
                            <div x-show="recipientType === 'doctor'" x-cloak>
                                <select x-bind:name="recipientType === 'doctor' ? 'recipient_id' : ''" x-model="recipientId" @change="loadAppointments" :required="recipientType === 'doctor'"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-400 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                    <option value="" disabled>{{ __('file.select_therapist') }}</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}">{{ $doctor->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Employee Select -->
                            <div x-show="recipientType === 'employee'" x-cloak>
                                <select x-bind:name="recipientType === 'employee' ? 'recipient_id' : ''" x-model="recipientId" :required="recipientType === 'employee'"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-400 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                    <option value="" disabled>{{ __('file.select_employee') }}</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            @error('recipient_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Therapist Appointments (Dynamic) -->
                    <div x-show="recipientType === 'doctor' && recipientId" x-cloak class="border rounded-xl p-5 bg-gray-50 dark:bg-gray-900/50 dark:border-gray-700">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.filter_by_month') }} <span class="text-red-500">*</span></label>
                            <input type="text" id="month_picker_input" placeholder="{{ __('file.select_month') ?? 'Select Month' }}" readonly :required="recipientType === 'doctor'"
                                class="w-full sm:w-1/3 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-400 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow [color-scheme:light] dark:[color-scheme:dark]">
                        </div>
                        
                        <div class="flex items-center justify-between mb-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider">{{ __('file.select_unpaid_appointments') }}</h3>
                            <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 rounded-md" x-text="appointments.length + ' {{ __('file.appointments') ?? 'Appointments' }} | ' + new Set(appointments.map(a => a.date.split(' ')[0])).size + ' {{ __('file.days') ?? 'Days' }}'"></span>
                        </div>
                        
                        <div x-show="loadingAppointments" class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400 py-4">
                            <svg class="animate-spin h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('file.loading_appointments') }}
                        </div>
                        
                        <div class="mt-4 p-8 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl text-center" x-show="!loadingAppointments && recipientId && selectedMonth && appointments.length === 0">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('file.no_unpaid_appointments') }}</p>
                        </div>

                        <div class="space-y-2 max-h-72 overflow-y-auto pr-2 custom-scrollbar" x-show="appointments.length > 0">
                            <template x-for="app in appointments" :key="app.id">
                                <label class="flex items-start p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-indigo-500 dark:hover:border-indigo-400 transition-all shadow-sm">
                                    <div class="flex-shrink-0 mt-1">
                                        <input type="checkbox" name="appointments[]" :value="app.id" :checked="selectedAppointmentIds.includes(app.id)" @change="toggleAppointment(app.id)" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 appointment-checkbox">
                                    </div>
                                        <div class="ml-4 flex-1 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="app.appointment_number"></p>
                                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full uppercase"
                                                        :class="app.status === 'Paid' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'"
                                                        x-text="app.status === 'Paid' ? '{{ __('file.paid') }}' : app.status"></span>
                                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full uppercase bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300" x-show="app.payment_method" x-text="app.payment_method"></span>
                                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full uppercase"
                                                        :class="app.is_invoiced ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'"
                                                        x-text="app.is_invoiced ? '{{ __('file.printed') ?? 'Printed' }}' : '{{ __('file.not_printed') ?? 'Not Printed' }}'"></span>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="'{{ __('file.patient') }}: ' + app.patient_name + ' | ' + app.date"></p>
                                            </div>
                                        <div class="flex items-center gap-4">
                                            <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400" x-text="'{{ \App\Models\Setting::getCurrencySymbol() }}' + app.amount"></div>
                                            <button type="button" x-show="app.invoice_id" @click.prevent="openInvoice(app.invoice_id)"
                                                class="px-2.5 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                                {{ __('file.view_details') ?? 'View Details' }}
                                            </button>
                                        </div>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.amount') }} <span class="text-red-500">*</span></label>
                            <div class="flex items-stretch border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 dark:focus-within:ring-indigo-400 focus-within:border-transparent transition-shadow">
                                <span class="inline-flex items-center px-3 bg-gray-50 dark:bg-gray-800 border-r border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 text-sm font-medium shrink-0 select-none">
                                    {{ \App\Models\Setting::getCurrencySymbol() }}
                                </span>
                                <input type="number" step="0.01" name="amount" id="payroll_amount" required x-model="amount"
                                    class="flex-1 min-w-0 px-3 py-2 text-sm bg-transparent dark:bg-transparent dark:text-white focus:outline-none">
                            </div>
                            @error('amount') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.corresponds_to_therapist') ?? 'Corresponds to therapist' }}</label>
                            <div class="flex items-stretch border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 dark:focus-within:ring-indigo-400 focus-within:border-transparent transition-shadow">
                                <span class="inline-flex items-center px-3 bg-gray-50 dark:bg-gray-800 border-r border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 text-sm font-medium shrink-0 select-none">
                                    {{ \App\Models\Setting::getCurrencySymbol() }}
                                </span>
                                <input type="number" step="0.01" name="therapist_amount" id="therapist_amount" x-model="therapistAmount"
                                    class="flex-1 min-w-0 px-3 py-2 text-sm bg-transparent dark:bg-transparent dark:text-white focus:outline-none">
                            </div>
                            @error('therapist_amount') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.corresponds_to_caped') ?? 'Corresponds to Caped' }}</label>
                            <div class="flex items-stretch border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 dark:focus-within:ring-indigo-400 focus-within:border-transparent transition-shadow">
                                <span class="inline-flex items-center px-3 bg-gray-50 dark:bg-gray-800 border-r border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 text-sm font-medium shrink-0 select-none">
                                    {{ \App\Models\Setting::getCurrencySymbol() }}
                                </span>
                                <input type="number" step="0.01" name="caped_amount" id="caped_amount" x-model="capedAmount"
                                    class="flex-1 min-w-0 px-3 py-2 text-sm bg-transparent dark:bg-transparent dark:text-white focus:outline-none">
                            </div>
                            @error('caped_amount') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.date') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="date" required value="{{ $payroll->date->format('Y-m-d') }}"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-400 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow [color-scheme:light] dark:[color-scheme:dark]">
                            @error('date') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.payment_method') }} <span class="text-red-500">*</span></label>
                            <select name="payment_method" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-400 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="Cash" {{ $payroll->payment_method === 'Cash' ? 'selected' : '' }}>{{ __('file.cash') }}</option>
                                <option value="Bank Transfer" {{ $payroll->payment_method === 'Bank Transfer' ? 'selected' : '' }}>{{ __('file.bank_transfer') }}</option>
                                <option value="Check" {{ $payroll->payment_method === 'Check' ? 'selected' : '' }}>{{ __('file.check') }}</option>
                                <option value="Other" {{ $payroll->payment_method === 'Other' ? 'selected' : '' }}>{{ __('file.other') }}</option>
                            </select>
                            @error('payment_method') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.internal_notes') }}</label>
                        <textarea name="notes" rows="4"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-400 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none placeholder-gray-400"
                            placeholder="{{ __('file.internal_notes_placeholder') }}">{{ $payroll->notes }}</textarea>
                    </div>
                </div>

                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 transition-colors duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('file.update_record') }}
                </button>
                <a href="{{ route('payrolls.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 dark:bg-transparent dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ __('file.cancel') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Invoice Modal -->
    <div id="invoice-modal" class="fixed inset-0 z-[60] hidden">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" id="invoice-modal-backdrop" @click="closeInvoiceModal"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 w-full max-w-4xl max-h-[90vh] flex flex-col">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modal-title">
                            {{ __('file.invoice_details') ?? 'Sales Details' }}
                        </h3>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="closeInvoiceModal"
                                class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="relative flex-1 w-full bg-gray-50 dark:bg-gray-900 min-h-[600px]">
                        <div id="modal-loader"
                            class="absolute inset-0 flex flex-col items-center justify-center bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm z-10">
                            <svg class="animate-spin h-10 w-10 text-indigo-600 mb-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                        <iframe id="invoice-iframe" class="w-full h-full border-0" title="Invoice Preview" onload="document.getElementById('modal-loader').style.display = 'none'"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
    }
</style>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script>
    function payrollForm() {
        return {
            recipientType: '{{ $payroll->payable_type === "App\\Models\\Doctor" ? "doctor" : "employee" }}',
            recipientId: '{{ $payroll->payable_id }}',
            selectedMonth: '{{ $payroll->date->format("Y-m") }}',
            appointments: [],
            loadingAppointments: false,
            amount: '{{ number_format($payroll->amount, 2, ".", "") }}',
            therapistAmount: '{{ $payroll->therapist_amount ? number_format($payroll->therapist_amount, 2, ".", "") : "" }}',
            capedAmount: '{{ $payroll->caped_amount ? number_format($payroll->caped_amount, 2, ".", "") : "" }}',
            selectedAppointmentIds: {!! json_encode($selectedAppointments) !!},
            
            init() {
                const locale = "{{ app()->getLocale() }}" === 'es' ? 'es' : 'default';
                flatpickr("#month_picker_input", {
                    plugins: [
                        new monthSelectPlugin({
                            shorthand: true,
                            dateFormat: "Y-m",
                            altInput: true,
                            altFormat: "F Y",
                            theme: document.documentElement.classList.contains('dark') ? "dark" : "light"
                        })
                    ],
                    locale: locale,
                    defaultDate: this.selectedMonth,
                    onChange: (selectedDates, dateStr) => {
                        this.selectedMonth = dateStr;
                        this.loadAppointments();
                    }
                });

                if (this.recipientType === 'doctor' && this.recipientId) {
                    this.loadAppointments();
                }
            },
            
            loadAppointments() {
                if (this.recipientType !== 'doctor' || !this.recipientId || !this.selectedMonth) {
                    this.appointments = [];
                    this.therapistAmount = '';
                    this.capedAmount = '';
                    return;
                }
                
                this.loadingAppointments = true;
                
                let url = `{{ route('payrolls.get-appointments') }}?doctor_id=${this.recipientId}&month=${this.selectedMonth}&payroll_id={{ $payroll->id }}`;
                
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        this.appointments = data;
                        this.loadingAppointments = false;
                    })
                    .catch(err => {
                        console.error(err);
                        this.loadingAppointments = false;
                    });
            },
            
            toggleAppointment(id) {
                const index = this.selectedAppointmentIds.indexOf(id);
                if (index > -1) {
                    this.selectedAppointmentIds.splice(index, 1);
                } else {
                    this.selectedAppointmentIds.push(id);
                }
            },
            

            openInvoice(id) {
                if (!id) return;
                const viewUrl = `{{ url('invoices') }}/${id}/print-html?redirect=false`;
                const invoiceModal = document.getElementById('invoice-modal');
                const invoiceIframe = document.getElementById('invoice-iframe');
                const modalLoader = document.getElementById('modal-loader');
                
                invoiceIframe.src = viewUrl;
                modalLoader.style.display = 'flex';
                invoiceModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            },
            
            closeInvoiceModal() {
                const invoiceModal = document.getElementById('invoice-modal');
                const invoiceIframe = document.getElementById('invoice-iframe');
                invoiceModal.classList.add('hidden');
                invoiceIframe.src = '';
                document.body.style.overflow = '';
            }
        }
    }
</script>
@endpush
@endsection
