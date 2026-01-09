@extends('layouts.app')
@section('title', __('file.create_invoice'))

@section('content')
<div class="w-full px-2 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('invoices.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">{{ __('file.invoices') }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">{{ __('file.create_invoice') }}</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.create_new_invoice') }}</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.create_invoice_description') }}</p>
    </div>

    <form method="POST" action="{{ route('invoices.store') }}" class="space-y-6">
        @csrf

        <!-- Patient Information -->
        <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gray-900 dark:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.patient_information') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.select_patient_for_invoice') }}</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.patient') }} <span class="text-red-500">*</span>
                    </label>
                    <select name="patient_id" required 
                            class="w-full px-4 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                        <option value="">{{ __('file.search_patients') }}</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->first_name }} {{ $patient->last_name }} - {{ $patient->patient_id }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id') 
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> 
                    @enderror
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gray-900 dark:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.invoice_details') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.enter_invoice_details') }}</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.invoice_number') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="invoice_number" value="{{ old('invoice_number', 'INV-' . date('YmdHis')) }}" required 
                               class="w-full px-4 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                               placeholder="INV-001">
                        @error('invoice_number') 
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> 
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.invoice_type') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="invoice_type" required 
                                class="w-full px-4 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                            <option value="standard" {{ old('invoice_type') == 'standard' ? 'selected' : '' }}>{{ __('file.standard_invoice') }}</option>
                            <option value="recurring" {{ old('invoice_type') == 'recurring' ? 'selected' : '' }}>{{ __('file.recurring_invoice') }}</option>
                            <option value="pro_forma" {{ old('invoice_type') == 'pro_forma' ? 'selected' : '' }}>{{ __('file.pro_forma_invoice') }}</option>
                        </select>
                        @error('invoice_type') 
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> 
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.invoice_date') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required 
                               class="w-full px-4 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                        @error('invoice_date') 
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> 
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.due_date') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required 
                               class="w-full px-4 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                        @error('due_date') 
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> 
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.reference_po_number') }}
                    </label>
                    <input type="text" name="reference_number" value="{{ old('reference_number') }}" 
                           class="w-full px-4 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                           placeholder="{{ __('file.optional') }}">
                    @error('reference_number') 
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> 
                    @enderror
                </div>
            </div>
        </div>

        <!-- Items & Services -->
        <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-gray-900 dark:bg-gray-700 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.items_services') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.add_items_to_invoice') }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="addInvoiceItem()" 
                            class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('file.add_item') }}
                    </button>
                </div>
            </div>
            <div class="p-6">
                <!-- Table Header -->
                <div class="hidden md:grid md:grid-cols-12 gap-4 mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                    <div class="col-span-5 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('file.description') }}
                    </div>
                    <div class="col-span-2 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-center">
                        {{ __('file.quantity') }}
                    </div>
                    <div class="col-span-2 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-center">
                        {{ __('file.unit_price') }}
                    </div>
                    <div class="col-span-2 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-right">
                        {{ __('file.total') }}
                    </div>
                    <div class="col-span-1"></div>
                </div>

                <!-- Invoice Items Container -->
                <div id="invoice-items" class="space-y-4">
                    <!-- Initial Item Row -->
                    <div class="invoice-item grid grid-cols-1 md:grid-cols-12 gap-4 p-4 bg-gray-50 dark:bg-gray-800/30 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="md:col-span-5">
                            <label class="block md:hidden text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.description') }}</label>
                            <select name="items[0][service_id]" 
                                    class="item-service w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    onchange="updateItemPrice(this)">
                                <option value="">{{ __('file.select_service_or_item') }}</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                                        {{ $service->name }} - ${{ number_format($service->price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block md:hidden text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.quantity') }}</label>
                            <input type="number" name="items[0][quantity]" value="1" min="1" 
                                   class="item-quantity w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow text-center"
                                   onchange="calculateItemTotal(this)">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block md:hidden text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.unit_price') }}</label>
                            <input type="number" name="items[0][unit_price]" value="0.00" step="0.01" min="0" 
                                   class="item-price w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow text-center"
                                   onchange="calculateItemTotal(this)">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block md:hidden text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('file.total') }}</label>
                            <div class="item-total px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-900/50 border border-gray-300 dark:border-gray-600 rounded-lg text-right">
                                $0.00
                            </div>
                        </div>
                        <div class="md:col-span-1 flex items-end md:items-center justify-end">
                            <button type="button" onclick="removeInvoiceItem(this)" 
                                    class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Section -->
                <div class="mt-8 flex flex-col items-end">
                    <div class="w-full md:w-96 space-y-3 bg-gray-50 dark:bg-gray-800/30 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('file.subtotal') }}:</span>
                            <span id="subtotal" class="font-semibold text-gray-900 dark:text-white">$0.00</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-600 dark:text-gray-400">{{ __('file.tax') }}</span>
                                <input type="number" name="tax_percentage" value="8" min="0" max="100" step="0.01" 
                                       class="w-16 px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-transparent dark:text-white"
                                       onchange="calculateTotals()">
                                <span class="text-gray-600 dark:text-gray-400">%:</span>
                            </div>
                            <span id="tax-amount" class="font-semibold text-gray-900 dark:text-white">$0.00</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('file.discount') }}:</span>
                            <div class="flex items-center gap-2">
                                <input type="number" name="discount" value="0.00" min="0" step="0.01" 
                                       class="w-24 px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-transparent dark:text-white text-right"
                                       onchange="calculateTotals()">
                            </div>
                        </div>
                        <div class="pt-3 border-t border-gray-300 dark:border-gray-600">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-semibold text-gray-900 dark:text-white">{{ __('file.total') }}:</span>
                                <span id="grand-total" class="text-2xl font-bold text-gray-900 dark:text-white">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gray-900 dark:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.additional_information') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.optional_notes_terms') }}</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('file.notes') }}
                    </label>
                    <textarea name="notes" rows="4" 
                              class="w-full px-4 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                              placeholder="{{ __('file.notes_placeholder') }}">{{ old('notes') }}</textarea>
                    @error('notes') 
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> 
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button type="submit" 
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 border border-gray-300 dark:border-gray-600 dark:bg-white dark:text-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('file.create_invoice') }}
            </button>
            <button type="button" onclick="previewInvoice()" 
                    class="inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                {{ __('file.preview') }}
            </button>
            <a href="{{ route('invoices.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ __('file.cancel') }}
            </a>
        </div>
    </form>
</div>

<script>
let itemIndex = 1;

function addInvoiceItem() {
    const container = document.getElementById('invoice-items');
    const newItem = document.querySelector('.invoice-item').cloneNode(true);
    
    // Update input names with new index
    newItem.querySelectorAll('select, input').forEach(input => {
        if (input.name) {
            input.name = input.name.replace(/\[0\]/, `[${itemIndex}]`);
        }
        if (input.classList.contains('item-service')) {
            input.value = '';
        }
        if (input.classList.contains('item-quantity')) {
            input.value = '1';
        }
        if (input.classList.contains('item-price')) {
            input.value = '0.00';
        }
    });
    
    newItem.querySelector('.item-total').textContent = '$0.00';
    container.appendChild(newItem);
    itemIndex++;
    calculateTotals();
}

function removeInvoiceItem(button) {
    const items = document.querySelectorAll('.invoice-item');
    if (items.length > 1) {
        button.closest('.invoice-item').remove();
        calculateTotals();
    }
}

function updateItemPrice(select) {
    const row = select.closest('.invoice-item');
    const selectedOption = select.options[select.selectedIndex];
    const price = selectedOption.getAttribute('data-price') || 0;
    
    row.querySelector('.item-price').value = parseFloat(price).toFixed(2);
    calculateItemTotal(select);
}

function calculateItemTotal(input) {
    const row = input.closest('.invoice-item');
    const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const total = quantity * price;
    
    row.querySelector('.item-total').textContent = '$' + total.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    
    document.querySelectorAll('.invoice-item').forEach(item => {
        const quantity = parseFloat(item.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(item.querySelector('.item-price').value) || 0;
        subtotal += quantity * price;
    });
    
    const taxPercentage = parseFloat(document.querySelector('input[name="tax_percentage"]').value) || 0;
    const discount = parseFloat(document.querySelector('input[name="discount"]').value) || 0;
    
    const taxAmount = (subtotal * taxPercentage) / 100;
    const grandTotal = subtotal + taxAmount - discount;
    
    // Fixed: Properly format with $ and two decimal places
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax-amount').textContent = '$' + taxAmount.toFixed(2);
    document.getElementById('grand-total').textContent = '$' + grandTotal.toFixed(2);
}

function previewInvoice() {
    // Add preview functionality here
    alert('Invoice preview functionality would open a modal or new window with the invoice preview.');
}

// Initialize calculations on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotals();
});
</script>

@endsection