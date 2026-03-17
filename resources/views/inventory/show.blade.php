{{-- resources/views/inventory-items/show.blade.php --}}
@extends('layouts.app')
@section('title', $inventoryitem->name)

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <!-- Header Section -->
        <div class=" mb-6">
            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-3">
                <a href="{{ route('inventory.index') }}" class="hover:text-gray-900 dark:hover:text-gray-200">
                    {{ __('file.inventory_items') }}
                </a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white">{{ Str::limit($inventoryitem->name, 30) }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-3xl font-semibold text-gray-900 dark:text-white mb-2">{{ $inventoryitem->name }}</h1>
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="px-2.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300">
                            {{ $inventoryitem->sku }}
                        </span>
                        @if($inventoryitem->category)
                            <span
                                class="px-2.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300">
                                {{ $inventoryitem->category->name }}
                            </span>
                        @endif
                        @php
                            $minStock = $inventoryitem->minimum_stock_level ?? 0;
                            $currentStock = $inventoryitem->current_stock ?? 0;
                        @endphp
                        @if($currentStock <= $minStock && $currentStock > 0)
                            <span
                                class="px-2.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300">
                                Low Stock
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('inventory.edit', $inventoryitem) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm rounded hover:bg-gray-800 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ __('file.edit') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Item Information -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-medium text-gray-900 dark:text-white">
                            {{ __('file.item_information') }}
                        </h2>
                    </div>
                    <div class="p-5 space-y-5">
                        <!-- Description -->
                        @if($inventoryitem->description)
                            <div>
                                <label
                                    class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">{{ __('file.description') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $inventoryitem->description }}</p>
                            </div>
                        @endif

                        <!-- Stock & Pricing Summary -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="text-center p-3 border border-gray-200 dark:border-gray-700 rounded">
                                <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('file.current_stock') }}
                                </div>
                                <div class="text-xl font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($inventoryitem->current_stock ?? 0) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $inventoryitem->unit_of_measure }}
                                </div>
                            </div>

                            <div class="text-center p-3 border border-gray-200 dark:border-gray-700 rounded">
                                <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('file.unit_cost') }}</div>
                                <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ $currency_code }}
                                    {{ number_format($inventoryitem->unit_cost ?? 0, 2) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">per
                                    {{ $inventoryitem->unit_of_measure }}
                                </div>
                            </div>

                            <div class="text-center p-3 border border-gray-200 dark:border-gray-700 rounded">
                                <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('file.unit_price') }}</div>
                                <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ $currency_code }}
                                    {{ number_format($inventoryitem->unit_price ?? 0, 2) }}
                                </div>
                                @php
                                    $margin = ($inventoryitem->unit_price && $inventoryitem->unit_cost)
                                        ? (($inventoryitem->unit_price - $inventoryitem->unit_cost) / $inventoryitem->unit_price) * 100
                                        : 0;
                                @endphp
                                <div class="text-xs text-gray-600 dark:text-gray-400">{{ number_format($margin, 1) }}%
                                    margin</div>
                            </div>

                            <div class="text-center p-3 border border-gray-200 dark:border-gray-700 rounded">
                                <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('file.total_value') }}
                                </div>
                                @php
                                    $totalValue = ($inventoryitem->current_stock ?? 0) * ($inventoryitem->unit_cost ?? 0);
                                @endphp
                                <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ $currency_code }}
                                    {{ number_format($totalValue, 2) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">inventory value</div>
                            </div>
                        </div>

                        <!-- Unit Details -->
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div>
                                <label
                                    class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">{{ __('file.unit_of_measure') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $inventoryitem->unit_of_measure ?? 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">{{ __('file.unit_quantity') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $inventoryitem->unit_quantity ?? 1 }}
                                </p>
                            </div>
                        </div>

                        <!-- Manufacturer / Brand / Model -->
                        @if($inventoryitem->manufacturer || $inventoryitem->brand || $inventoryitem->model_version)
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="grid grid-cols-3 gap-4">
                                    @if($inventoryitem->manufacturer)
                                        <div>
                                            <label
                                                class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">{{ __('file.manufacturer') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $inventoryitem->manufacturer }}
                                            </p>
                                        </div>
                                    @endif
                                    @if($inventoryitem->brand)
                                        <div>
                                            <label
                                                class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">{{ __('file.brand') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $inventoryitem->brand }}</p>
                                        </div>
                                    @endif
                                    @if($inventoryitem->model_version)
                                        <div>
                                            <label
                                                class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">{{ __('file.model_version') }}</label>
                                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $inventoryitem->model_version }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Storage Location -->
                        @if($inventoryitem->storage_location)
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <label
                                    class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">{{ __('file.storage_location') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $inventoryitem->storage_location }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Stock Levels Card -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-medium text-gray-900 dark:text-white">
                            {{ __('file.stock_levels') }}
                        </h2>
                    </div>
                    <div class="p-5">
                        <div class="mb-5">
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Stock Status</span>
                                <span class="text-sm text-gray-900 dark:text-white">
                                    {{ $inventoryitem->current_stock ?? 0 }} {{ $inventoryitem->unit_of_measure }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded h-2 overflow-hidden">
                                @php
                                    $currentStock = $inventoryitem->current_stock ?? 0;
                                    $minStock = $inventoryitem->minimum_stock_level ?? 0;

                                    $color = $currentStock <= $minStock
                                        ? ($currentStock == 0 ? 'bg-red-500' : 'bg-yellow-500')
                                        : 'bg-green-500';

                                    // Progress bar logic when there's no max stock: 
                                    // if current > min, show green. if current <= min, show yellow/red.
                                    $percentage = $currentStock > 0 ? 100 : 0;
                                @endphp
                                <div class="{{ $color }} h-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            @if($currentStock <= $minStock && $currentStock > 0)
                                <p class="mt-2 text-xs text-yellow-600 dark:text-yellow-400">
                                    ⚠️ Low stock - Consider restocking
                                </p>
                            @elseif($currentStock == 0)
                                <p class="mt-2 text-xs text-red-600 dark:text-red-400">
                                    ⚠️ Out of stock
                                </p>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 gap-3">
                            <div class="text-center p-3 border border-gray-200 dark:border-gray-700 rounded">
                                <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">
                                    {{ __('file.minimum_stock_level') }}
                                </div>
                                <div class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $inventoryitem->minimum_stock_level ?: '0' }}
                                </div>
                            </div>
                        </div>

                        @if($inventoryitem->tax_rate)
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="text-sm text-gray-600 dark:text-gray-400">Tax Rate</div>
                                <div class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $inventoryitem->tax_rate }}%
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Supplier Information Card -->
                @if($inventoryitem->primarySupplier)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-base font-medium text-gray-900 dark:text-white">
                                {{ __('file.supplier_details') }}
                            </h2>
                        </div>
                        <div class="p-5 space-y-3">
                            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded">
                                <div class="text-xs text-gray-600 dark:text-gray-400 uppercase mb-2">Primary Supplier</div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-3">
                                    {{ $inventoryitem->primarySupplier->name }}
                                </h3>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">Item Code</div>
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $inventoryitem->supplier_item_code ?: '—' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">Price</div>
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $currency_code }}
                                            {{ number_format($inventoryitem->supplier_price ?? 0, 2) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">Lead Time</div>
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $inventoryitem->lead_time_days ?? 0 }} days
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">Min. Order Qty</div>
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $inventoryitem->minimum_order_quantity ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Medicine Information -->
                @if($inventoryitem->generic_name || $inventoryitem->medicine_type || $inventoryitem->dosage || $inventoryitem->side_effects || $inventoryitem->precautions_warnings)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-base font-medium text-gray-900 dark:text-white">Medicine Information</h2>
                        </div>
                        <div class="p-5">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @if($inventoryitem->generic_name)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">Generic Name</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ $inventoryitem->generic_name }}
                                        </dd>
                                    </div>
                                @endif
                                @if($inventoryitem->medicine_type)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">Medicine Type
                                        </dt>
                                        <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ $inventoryitem->medicine_type }}
                                        </dd>
                                    </div>
                                @endif
                                @if($inventoryitem->dosage)
                                    <div>
                                        <dt class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">Dosage</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ $inventoryitem->dosage }}</dd>
                                    </div>
                                @endif
                            </dl>
                            @if($inventoryitem->side_effects)
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <dt class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">Side Effects</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ $inventoryitem->side_effects }}</dd>
                                </div>
                            @endif
                            @if($inventoryitem->precautions_warnings)
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <dt class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">Precautions &
                                        Warnings</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white mt-1">
                                        {{ $inventoryitem->precautions_warnings }}
                                    </dd>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Batches -->
                @if($inventoryitem->expiry_tracking && $inventoryitem->batches->count() > 0)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-base font-medium text-gray-900 dark:text-white">Batches</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">
                                            Batch Number</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">
                                            Mfg. Date</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">
                                            Expiry Date</th>
                                        <th
                                            class="px-5 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">
                                            Quantity</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($inventoryitem->batches as $batch)
                                        <tr>
                                            <td class="px-5 py-3 text-sm text-gray-900 dark:text-white">
                                                {{ $batch->batch_number ?? 'N/A' }}
                                            </td>
                                            <td class="px-5 py-3 text-sm text-gray-900 dark:text-white">
                                                {{ $batch->manufacturing_date ? $batch->manufacturing_date->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td class="px-5 py-3 text-sm text-gray-900 dark:text-white">
                                                {{ $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td class="px-5 py-3 text-right text-sm text-gray-900 dark:text-white">
                                                {{ $batch->current_quantity }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-6">
                <!-- Properties -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-medium text-gray-900 dark:text-white">Properties</h2>
                    </div>
                    <div class="p-5 space-y-2">
                        <div
                            class="flex items-center justify-between p-2.5 border border-gray-200 dark:border-gray-700 rounded">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.expiry_tracking') }}</span>
                            <div class="flex items-center">
                                @if($inventoryitem->expiry_tracking)
                                    <svg class="w-5 h-5 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-600" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between p-2.5 border border-gray-200 dark:border-gray-700 rounded">
                            <span
                                class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.requires_refrigeration') }}</span>
                            <div class="flex items-center">
                                @if($inventoryitem->requires_refrigeration)
                                    <svg class="w-5 h-5 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-600" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between p-2.5 border border-gray-200 dark:border-gray-700 rounded">
                            <span
                                class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.controlled_substance') }}</span>
                            <div class="flex items-center">
                                @if($inventoryitem->controlled_substance)
                                    <svg class="w-5 h-5 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-600" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between p-2.5 border border-gray-200 dark:border-gray-700 rounded">
                            <span
                                class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.hazardous_material') }}</span>
                            <div class="flex items-center">
                                @if($inventoryitem->hazardous_material)
                                    <svg class="w-5 h-5 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-600" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between p-2.5 border border-gray-200 dark:border-gray-700 rounded">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.sterile') }}</span>
                            <div class="flex items-center">
                                @if($inventoryitem->sterile)
                                    <svg class="w-5 h-5 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-600" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Storage Conditions -->
                @if($inventoryitem->storage_conditions)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-base font-medium text-gray-900 dark:text-white">Storage Conditions</h2>
                        </div>
                        <div class="p-5">
                            <div class="space-y-2">
                                @foreach(json_decode($inventoryitem->storage_conditions) as $condition)
                                    <div
                                        class="text-sm text-gray-900 dark:text-white p-2.5 border border-gray-200 dark:border-gray-700 rounded">
                                        {{ $condition }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Images -->
                @if($inventoryitem->medicine_image || $inventoryitem->package_image)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-base font-medium text-gray-900 dark:text-white">Images</h2>
                        </div>
                        <div class="p-5">
                            <div class="space-y-4">
                                @if($inventoryitem->medicine_image)
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Medicine Image</p>
                                        <img src="{{ Str::startsWith($inventoryitem->medicine_image, ['http://', 'https://']) ? $inventoryitem->medicine_image : asset('storage/' . $inventoryitem->medicine_image) }}"
                                            alt="Medicine" class="w-full rounded border border-gray-200 dark:border-gray-700">
                                    </div>
                                @endif
                                @if($inventoryitem->package_image)
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Package Image</p>
                                        <img src="{{ Str::startsWith($inventoryitem->package_image, ['http://', 'https://']) ? $inventoryitem->package_image : asset('storage/' . $inventoryitem->package_image) }}"
                                            alt="Package" class="w-full rounded border border-gray-200 dark:border-gray-700">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Additional Information -->
                @if($inventoryitem->additional_info)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-base font-medium text-gray-900 dark:text-white">{{ __('file.additional_info') }}
                            </h2>
                        </div>
                        <div class="p-5">
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $inventoryitem->additional_info }}</p>
                        </div>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-medium text-gray-900 dark:text-white">Quick Actions</h2>
                    </div>
                    <div class="p-5 space-y-2">
                        <button
                            class="w-full flex items-center justify-center px-4 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Stock
                        </button>
                        <button
                            class="w-full flex items-center justify-center px-4 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            Create Order
                        </button>
                        <button
                            class="w-full flex items-center justify-center px-4 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection