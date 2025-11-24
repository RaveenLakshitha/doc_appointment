{{-- resources/views/inventory-items/show.blade.php --}}
@extends('layouts.app')
@section('title', $inventoryitem->name)

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
            <a href="{{ route('inventoryitems.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                {{ __('file.inventory_items') }}
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white font-medium">{{ Str::limit($inventoryitem->name, 30) }}</span>
        </div>
        
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="flex-1">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ $inventoryitem->name }}</h1>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                        {{ $inventoryitem->sku }}
                    </span>
                    @if($inventoryitem->category)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        {{ $inventoryitem->category->full_path }}
                    </span>
                    @endif
                    @if($inventoryitem->current_stock <= $inventoryitem->reorder_point)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Low Stock
                    </span>
                    @endif
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('inventoryitems.edit', $inventoryitem) }}"
                   class="inline-flex items-center px-5 py-2.5 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
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
            <!-- Item Information (formerly Basic Information + Stock Overview Data) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('file.item_information') }}
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Description -->
                    @if($inventoryitem->description)
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('file.description') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $inventoryitem->description }}</p>
                    </div>
                    @endif

                    <!-- Stock & Pricing Summary (from cards) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="flex items-center justify-center mb-1">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('file.current_stock') }}</span>
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($inventoryitem->current_stock) }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $inventoryitem->unit_of_measure }}</div>
                        </div>

                        <div class="text-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                            <div class="flex items-center justify-center mb-1">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('file.unit_cost') }}</span>
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">Rs. {{ number_format($inventoryitem->unit_cost, 2) }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">per {{ $inventoryitem->unit_of_measure }}</div>
                        </div>

                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="flex items-center justify-center mb-1">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('file.unit_price') }}</span>
                            </div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">Rs. {{ number_format($inventoryitem->unit_price, 2) }}</div>
                            <div class="text-xs text-green-600 dark:text-green-400 font-medium">{{ $inventoryitem->profit_margin }}% margin</div>
                        </div>

                        <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <div class="flex items-center justify-center mb-1">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('file.total_value') }}</span>
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">Rs. {{ number_format($inventoryitem->total_value, 2) }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">inventory value</div>
                        </div>
                    </div>

                    <!-- Unit Details -->
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('file.unit_of_measure') }}</label>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $inventoryitem->unit_of_measure }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('file.unit_quantity') }}</label>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $inventoryitem->unit_quantity ?? 1 }}</p>
                        </div>
                    </div>

                    <!-- Manufacturer / Brand / Model -->
                    @if($inventoryitem->manufacturer || $inventoryitem->brand || $inventoryitem->model_version)
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-3 gap-4">
                            @if($inventoryitem->manufacturer)
                            <div>
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('file.manufacturer') }}</label>
                                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $inventoryitem->manufacturer }}</p>
                            </div>
                            @endif
                            @if($inventoryitem->brand)
                            <div>
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('file.brand') }}</label>
                                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $inventoryitem->brand }}</p>
                            </div>
                            @endif
                            @if($inventoryitem->model_version)
                            <div>
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('file.model_version') }}</label>
                                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $inventoryitem->model_version }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Storage Location -->
                    @if($inventoryitem->storage_location)
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('file.storage_location') }}</label>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $inventoryitem->storage_location }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Stock Levels Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        {{ __('file.stock_pricing') }}
                    </h2>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Stock Level</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $inventoryitem->current_stock }} / {{ $inventoryitem->maximum_stock_level ?: '∞' }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                            @php
                                $percentage = $inventoryitem->maximum_stock_level 
                                    ? min(($inventoryitem->current_stock / $inventoryitem->maximum_stock_level) * 100, 100)
                                    : 50;
                                $color = $inventoryitem->current_stock <= $inventoryitem->reorder_point 
                                    ? 'bg-red-500' 
                                    : ($percentage < 50 ? 'bg-yellow-500' : 'bg-green-500');
                            @endphp
                            <div class="{{ $color }} h-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>
                        @if($inventoryitem->current_stock <= $inventoryitem->reorder_point)
                        <p class="mt-2 text-xs text-red-600 dark:text-red-400 font-medium">
                            ⚠️ Below reorder point - Consider restocking
                        </p>
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('file.reorder_point') }}</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $inventoryitem->reorder_point }}</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('file.minimum_stock_level') }}</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $inventoryitem->minimum_stock_level ?: '—' }}</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('file.reorder_quantity') }}</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $inventoryitem->reorder_quantity ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplier Information Card -->
            @if($inventoryitem->primarySupplier || count($secondary_suppliers) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h-4m-4 0H5m14 0h-4m-4 0H5"/>
                        </svg>
                        {{ __('file.supplier_details') }}
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    @if($inventoryitem->primarySupplier)
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-blue-700 dark:text-blue-400 uppercase tracking-wide">Primary Supplier</span>
                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-xs font-medium rounded">Active</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ $inventoryitem->primarySupplier->name }}</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Item Code</div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $inventoryitem->supplier_item_code ?: '—' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Price</div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Rs. {{ number_format($inventoryitem->supplier_price ?? 0, 2) }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Lead Time</div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventoryitem->lead_time_days ?? 0 }} days</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Min. Order Qty</div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventoryitem->minimum_order_quantity ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(count($secondary_suppliers) > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Alternative Suppliers ({{ count($secondary_suppliers) }})</h3>
                        <div class="space-y-3">
                            @foreach($secondary_suppliers as $s)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $s['name'] }}</h4>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Code:</span>
                                        <span class="text-gray-900 dark:text-white font-mono ml-1">{{ $s['item_code'] ?? '—' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Price:</span>
                                        <span class="text-gray-900 dark:text-white font-medium ml-1">Rs. {{ number_format($s['price'] ?? 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Lead:</span>
                                        <span class="text-gray-900 dark:text-white ml-1">{{ $s['lead_time'] ?? 0 }}d</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">MOQ:</span>
                                        <span class="text-gray-900 dark:text-white ml-1">{{ $s['min_qty'] ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Status Indicators Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Properties
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.expiry_tracking') }}</span>
                        <div class="flex items-center">
                            @if($inventoryitem->expiry_tracking)
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.requires_refrigeration') }}</span>
                        <div class="flex items-center">
                            @if($inventoryitem->requires_refrigeration)
                                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.controlled_substance') }}</span>
                        <div class="flex items-center">
                            @if($inventoryitem->controlled_substance)
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.hazardous_material') }}</span>
                        <div class="flex items-center">
                            @if($inventoryitem->hazardous_material)
                                <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.sterile') }}</span>
                        <div class="flex items-center">
                            @if($inventoryitem->sterile)
                                <svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info Card -->
            @if($inventoryitem->additional_info)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        {{ __('file.additional_info') }}
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $inventoryitem->additional_info }}</p>
                </div>
            </div>
            @endif

            <!-- Quick Actions Card -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Quick Actions
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <button class="w-full flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Stock
                    </button>
                    <button class="w-full flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        Create Order
                    </button>
                    <button class="w-full flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection