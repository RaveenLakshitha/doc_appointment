@extends('layouts.app')

@section('title', 'Edit Treatments - Appointment #' . $appointment->id)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('appointments.show', $appointment) }}"
               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 flex items-center text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Appointment #{{ $appointment->id }}
            </a>
            <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">
                Edit Treatments for Appointment #{{ $appointment->id }}
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Select treatments, set quantities, prices, and notes.
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('appointments.treatments.update', $appointment) }}" id="treatmentsForm">
            @csrf
            @method('PATCH')

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-8">
                <!-- Treatments list (dynamic rows) -->
                <div id="treatments-container" class="space-y-6">
                    @if($appointment->treatments->isNotEmpty())
                        @foreach($appointment->treatments as $index => $treatment)
                            <div class="treatment-row border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/50">
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                                    <!-- Treatment Select -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Treatment <span class="text-red-500">*</span>
                                        </label>
                                        <select name="treatments[{{ $index }}][treatment_id]" required
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">Select Treatment</option>
                                            @foreach($treatments as $t)
                                                <option value="{{ $t->id }}"
                                                        {{ $t->id == $treatment->id ? 'selected' : '' }}
                                                        data-price="{{ $t->price }}"
                                                        data-code="{{ $t->code ?? '' }}">
                                                    {{ $t->name }} {{ $t->code ? "({$t->code})" : '' }} - {{ $currency_code }} {{ number_format($t->price, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Quantity -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Quantity <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="treatments[{{ $index }}][quantity]" min="1" max="99" required
                                               value="{{ old('treatments.' . $index . '.quantity', $treatment->pivot->quantity) }}"
                                               class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    <!-- Price -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Unit Price {{ $currency_code }} <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="treatments[{{ $index }}][price]" step="0.01" min="0" required
                                               value="{{ old('treatments.' . $index . '.price', $treatment->pivot->price_at_time) }}"
                                               class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    <!-- Remove Button -->
                                    <div class="flex justify-end">
                                        <button type="button" class="remove-treatment text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                                            Remove
                                        </button>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Notes (optional)
                                    </label>
                                    <textarea name="treatments[{{ $index }}][notes]" rows="2"
                                              class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('treatments.' . $index . '.notes', $treatment->pivot->notes) }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Add New Treatment Row Button -->
                <div class="mt-6">
                    <button type="button" id="add-treatment"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Another Treatment
                    </button>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('appointments.show', $appointment) }}"
                   class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition font-medium">
                    Save Treatments
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for dynamic rows -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('treatments-container');
    const addButton = document.getElementById('add-treatment');
    let rowIndex = {{ $appointment->treatments->count() }}; // Start from next index

    // Remove row
    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-treatment')) {
            e.target.closest('.treatment-row').remove();
        }
    });

    // Add new row
    addButton.addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'treatment-row border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/50';
        row.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Treatment <span class="text-red-500">*</span>
                    </label>
                    <select name="treatments[${rowIndex}][treatment_id]" required
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select Treatment</option>
                        @foreach($treatments as $t)
                            <option value="{{ $t->id }}" data-price="{{ $t->price }}" data-code="{{ $t->code ?? '' }}">
                                {{ $t->name }} {{ $t->code ? "({$t->code})" : '' }} - {{ $currency_code }} {{ number_format($t->price, 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Quantity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="treatments[${rowIndex}][quantity]" min="1" max="99" required
                           value="1"
                           class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Unit Price {{ $currency_code }} <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="treatments[${rowIndex}][price]" step="0.01" min="0" required
                           value="0.00"
                           class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div class="flex justify-end">
                    <button type="button" class="remove-treatment text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                        Remove
                    </button>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Notes (optional)
                </label>
                <textarea name="treatments[${rowIndex}][notes]" rows="2"
                          class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
            </div>
        `;

        container.appendChild(row);
        rowIndex++;
    });

    // Auto-fill price when treatment is selected
    container.addEventListener('change', function (e) {
        if (e.target.tagName === 'SELECT' && e.target.value) {
            const option = e.target.options[e.target.selectedIndex];
            const priceInput = e.target.closest('.grid').querySelector('input[name$="[price]"]');
            if (priceInput) {
                priceInput.value = option.dataset.price || '0.00';
            }
        }
    });
});
</script>
@endsection