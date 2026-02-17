@extends('layouts.pos')
@section('title', __('file.point_of_sale'))

@section('content')

@php
    $preloadedItems = $preloadedItems ?? [];
    $preselectedPatientId = $preselectedPatientId ?? null;
@endphp

<script>
    window.preloadedItems = @json($preloadedItems);
    window.preselectedPatientId = @json($preselectedPatientId);
</script>

<div class="w-full min-h-screen lg:h-screen flex flex-col lg:flex-row bg-gray-50 dark:bg-gray-900">


    <div class="flex-1 flex flex-col overflow-hidden">
        
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors active:scale-95" title="{{ __('file.back') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <svg class="w-6 h-6 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <div id="register-status-container" class="cursor-pointer" onclick="showRegisterDetails()">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white" id="register-label">Register: Loading...</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" id="register-subtitle">Loading status...</p>
                    </div>

                    
                <p class="text-xs text-gray-500 dark:text-gray-400 ml-auto" id="current-time">Loading time...</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="showSalesStats()" class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors active:scale-95" title="Sales Statistics">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </button>
                    <button onclick="showRegisterDetails()" class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors active:scale-95" title="Register Details">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                    <button onclick="showLastTransaction()" class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors active:scale-95" title="Last Transaction">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="relative mb-3">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" id="search-products" placeholder="Search products or services..."
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-base">
            </div>

            <div class="flex gap-2 overflow-x-auto pb-1 hide-scrollbar">
                <button onclick="filterCategory('all')" class="category-btn active px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap bg-gray-900 text-white dark:bg-white dark:text-gray-900 active:scale-95 transition-all">
                    All Items
                </button>
                <button onclick="filterCategory('consultation')" class="category-btn px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 active:scale-95 transition-all">
                    Consultations
                </button>
                <button onclick="filterCategory('treatment')" class="category-btn px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 active:scale-95 transition-all">
                    Treatments
                </button>
                <button onclick="filterCategory('medication')" class="category-btn px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 active:scale-95 transition-all">
                    Medications
                </button>
                <button onclick="filterCategory('lab')" class="category-btn px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 active:scale-95 transition-all">
                    Lab Tests
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900" style="max-height: calc(100vh - 200px);">
            <div id="products-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                
                @foreach($services as $service)
                <button type="button" 
                        class="product-card p-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-900 dark:hover:border-gray-500 hover:shadow-lg active:scale-95 transition-all text-left group"
                        data-item-type="service"
                        data-item-id="{{ $service->id }}"
                        data-item-name="{{ $service->name }}"
                        data-item-price="{{ $service->price }}"
                        data-category="consultation"
                        onclick="addToCart('service', {{ $service->id }}, '{{ addslashes($service->name) }}', {{ $service->price }})">
                    <div class="flex flex-col h-full">
                        <div class="w-full h-28 bg-gray-100 dark:bg-gray-700 rounded-md mb-3 flex items-center justify-center overflow-hidden">
                            <svg class="w-14 h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        
                        <div class="flex-1">
                            <div class="flex items-center gap-1 mb-2">
                                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded font-medium">Service</span>
                            </div>
                            <h3 class="font-semibold text-sm text-gray-900 dark:text-white mb-1 line-clamp-2">
                                {{ $service->name }}
                            </h3>
                        </div>
                        <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-base font-bold text-gray-900 dark:text-white">
                                ${{ number_format($service->price, 2) }}
                            </span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </div>
                </button>
                @endforeach

                @foreach($inventoryItems as $item)
                <button type="button" 
                        class="product-card p-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-900 dark:hover:border-gray-500 hover:shadow-lg active:scale-95 transition-all text-left group {{ $item->current_stock <= 0 ? 'opacity-60' : '' }}"
                        data-item-type="inventory"
                        data-item-id="{{ $item->id }}"
                        data-item-name="{{ $item->name }} @if($item->generic_name) ({{ $item->generic_name }}) @endif"
                        data-item-price="{{ $item->unit_price }}"
                        data-stock="{{ $item->current_stock }}"
                        data-category="medication"
                        onclick="{{ $item->current_stock > 0 ? "addToCart('inventory', {$item->id}, '" . addslashes($item->name . ($item->generic_name ? ' (' . $item->generic_name . ')' : '')) . "', {$item->unit_price})" : "alert('Out of stock!')" }}"
                        {{ $item->current_stock <= 0 ? 'disabled' : '' }}>
                    <div class="flex flex-col h-full">
                        <div class="w-full h-28 bg-gray-100 dark:bg-gray-700 rounded-md mb-3 flex items-center justify-center overflow-hidden">
                            @if($item->medicine_image)
                                <img src="{{ $item->medicine_image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-14 h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <div class="flex items-center gap-1 mb-2 flex-wrap">
                                <span class="text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded font-medium">Medicine</span>
                                @if($item->current_stock < $item->reorder_point && $item->current_stock > 0)
                                    <span class="text-xs px-2 py-1 bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300 rounded font-medium">Low</span>
                                @elseif($item->current_stock <= 0)
                                    <span class="text-xs px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 rounded font-medium">Out</span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-sm text-gray-900 dark:text-white mb-1 line-clamp-2">
                                {{ $item->name }}
                            </h3>
                            @if($item->generic_name)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">({{ $item->generic_name }})</p>
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Stock: <strong class="{{ $item->current_stock <= 0 ? 'text-red-600 dark:text-red-400' : '' }}">{{ $item->current_stock }}</strong>
                            </p>
                        </div>
                        <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-base font-bold text-gray-900 dark:text-white">
                                ${{ number_format($item->unit_price, 2) }}
                            </span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </div>
                </button>
                @endforeach

                @foreach($treatments as $treatment)
                <button type="button" 
                        class="product-card p-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-900 dark:hover:border-gray-500 hover:shadow-lg active:scale-95 transition-all text-left group"
                        data-item-type="treatment"
                        data-item-id="{{ $treatment->id }}"
                        data-item-name="{{ $treatment->name }}"
                        data-item-price="{{ $treatment->price }}"
                        data-category="treatment"
                        onclick="addToCart('treatment', {{ $treatment->id }}, '{{ addslashes($treatment->name) }}', {{ $treatment->price }})">
                    <div class="flex flex-col h-full">
                        <div class="w-full h-28 bg-gray-100 dark:bg-gray-700 rounded-md mb-3 flex items-center justify-center overflow-hidden">
                            <svg class="w-14 h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        
                        <div class="flex-1">
                            <div class="flex items-center gap-1 mb-2 flex-wrap">
                                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 rounded font-medium">
                                    Treatment
                                </span>
                                @if($treatment->code)
                                    <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded font-medium">
                                        {{ $treatment->code }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-sm text-gray-900 dark:text-white mb-1 line-clamp-2">
                                {{ $treatment->name }}
                            </h3>
                        </div>
                        <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-base font-bold text-gray-900 dark:text-white">
                                ${{ number_format($treatment->price, 2) }}
                            </span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </div>
                </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="flex-1 flex flex-col overflow-hidden lg:w-1/2 bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700">
        
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Patient <span class="text-red-500">*</span>
            </label>
            <select id="patient-select" class="w-full px-4 py-3 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                <option value="">Select Patient</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }} - {{ $patient->medical_record_number }}</option>
                @endforeach
            </select>
        </div>

        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cart Items: <span id="items-count" class="font-bold text-gray-900 dark:text-white">0</span></span>
            </div>
            <button type="button" onclick="clearAll()" class="px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                Clear All
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4" style="max-height: calc(100vh - 180px);">
            <div id="cart-items" class="space-y-3">
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cart is empty</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Add items to get started</p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 p-4 space-y-3 flex-shrink-0">
            
            <div class="grid grid-cols-3 gap-3 text-sm">
                <div class="space-y-1">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                    <p id="subtotal-amount" class="font-semibold text-gray-900 dark:text-white">$0.00</p>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-1">
                        <span class="text-gray-600 dark:text-gray-400">Tax</span>
                        <input type="number" id="tax-input" value="8" min="0" max="100" step="0.1" 
                            class="w-14 px-1 py-0.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white text-center"
                            onchange="updateTotals()">
                        <span class="text-gray-600 dark:text-gray-400">%</span>
                    </div>
                    <p id="tax-amount" class="font-semibold text-gray-900 dark:text-white">$0.00</p>
                </div>
                <div class="space-y-1">
                    <span class="text-gray-600 dark:text-gray-400">Discount</span>
                    <input type="number" id="discount-input" value="0" min="0" step="0.01" 
                        class="w-full px-2 py-0.5 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white text-right"
                        placeholder="0.00"
                        onchange="updateTotals()">
                </div>
            </div>

            <div class="pt-2 border-t border-gray-300 dark:border-gray-600">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Total Amount</span>
                    <span id="grand-total" class="text-2xl font-bold text-gray-900 dark:text-white">$0.00</span>
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <button onclick="openPaymentModal('cash')" class="payment-method-btn px-3 py-2 text-xs font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-gray-400 dark:hover:border-gray-500 transition-all">
                            <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Cash
                        </button>
                        <button onclick="openPaymentModal('card')" class="payment-method-btn px-3 py-2 text-xs font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-gray-400 dark:hover:border-gray-500 transition-all">
                            <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Card
                        </button>
                        <button onclick="openPaymentModal('other')" class="payment-method-btn px-3 py-2 text-xs font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-gray-400 dark:hover:border-gray-500 transition-all">
                            <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Other
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <button onclick="saveAsDraft()" class="py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 active:scale-95 transition-all ">
                        Save as Draft
                    </button>
                    <button type="button" onclick="openPartialPaymentModal()"
                        class="py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium">
                    Down / Partial Pay
                </button>
                    <button onclick="clearCart()" class="py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 font-medium rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 active:scale-95 transition-all">
                        Cancel Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<div id="payment-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Complete Payment</h3>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 active:scale-95 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-4 space-y-4">
            <div class="text-center py-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">Amount Due</p>
                <p id="modal-grand-total" class="text-4xl font-bold text-gray-900 dark:text-white">$0.00</p>
            </div>

            <div id="cash-section" class="space-y-4 hidden">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount Tendered</label>
                    <input type="number" id="cash-received" step="0.01" min="0" value="0" class="w-full px-4 py-3 text-xl text-right font-semibold border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white" oninput="updateCashChange()">
                </div>

                <div class="grid grid-cols-4 gap-3">
                    <button onclick="addCashDenomination(100)" class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium">$100</button>
                    <button onclick="addCashDenomination(50)" class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium">$50</button>
                    <button onclick="addCashDenomination(20)" class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium">$20</button>
                    <button onclick="addCashDenomination(10)" class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium">$10</button>
                    <button onclick="addCashDenomination(5)" class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium">$5</button>
                    <button onclick="addCashDenomination(1)" class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium">$1</button>
                    <button onclick="addCashDenomination(0.25)" class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium text-xs">$0.25</button>
                    <button onclick="addCashDenomination(0.10)" class="py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium text-xs">$0.10</button>
                </div>

                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center">
                    <p class="text-sm font-medium text-green-700 dark:text-green-300">Change Due</p>
                    <p id="cash-change" class="text-3xl font-bold text-green-600 dark:text-green-400">$0.00</p>
                </div>
            </div>

            <div id="card-section" class="hidden text-center py-8">
                <svg class="w-20 h-20 mx-auto text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <p class="text-lg font-medium text-gray-900 dark:text-white">Card Payment</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Process card payment externally</p>
            </div>

            <div id="other-section" class="hidden text-center py-8">
                <svg class="w-20 h-20 mx-auto text-purple-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <p class="text-lg font-medium text-gray-900 dark:text-white">Other Payment</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Record payment manually</p>
            </div>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-b-lg space-y-3">
            <button id="complete-payment-btn" onclick="processPayment()" class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-semibold rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 active:scale-98 transition-all text-base">
                Complete Payment
            </button>
            <button onclick="closePaymentModal()" class="w-full py-3 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- ==================== Register Details Modal ==================== -->
<div id="register-details-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-xl font-bold">Cash Register Session</h3>
            <button onclick="closeRegisterDetailsModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-6 space-y-4" id="register-details-content">
            <!-- This will be replaced dynamically by JS -->
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                Loading register details...
            </div>
        </div>

        <div class="p-5 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700 flex gap-3">
            <button onclick="closeRegisterDetailsModal()" class="flex-1 py-3 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                Close
            </button>
            <button onclick="showCloseRegisterForm()" class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
                Close & Reconcile Register
            </button>
        </div>
    </div>
</div>

<!-- Last Transaction Modal -->
<div id="last-transaction-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Last Transaction</h3>
            <button onclick="closeLastTransaction()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-4 space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Invoice #</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">POS-20260105123045</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Patient</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">John Doe</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Time</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">02:15 PM</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Payment Method</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">Cash</span>
            </div>
            <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <span class="text-base font-semibold text-gray-900 dark:text-white">Total Amount</span>
                    <span class="text-base font-bold text-gray-900 dark:text-white">$125.50</span>
                </div>
            </div>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-b-lg flex gap-2">
            <button onclick="viewInvoice()" class="flex-1 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors text-sm">
                View Invoice
            </button>
            <button onclick="printLastInvoice()" class="flex-1 py-2 bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors text-sm">
                Print
            </button>
        </div>
    </div>
</div>

<div id="partial-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-xl font-bold">Record Payment</h3>
            <button onclick="closePartialModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-5 space-y-5">
            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Due</p>
                <p id="partial-total" class="text-3xl font-bold text-gray-900 dark:text-white mt-1">$0.00</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Amount Paying Now</label>
                <input type="number" step="0.01" min="0" id="partial-amount" 
                       class="w-full text-2xl text-right font-semibold p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Payment Method</label>
                <select id="partial-method" class="w-full p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cheque">Cheque</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Reference / Receipt # (optional)</label>
                <input type="text" id="partial-reference" class="w-full p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
            </div>

            <div class="pt-2 border-t dark:border-gray-700">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Remaining Balance</span>
                    <span id="partial-remaining" class="font-bold">$0.00</span>
                </div>
            </div>
        </div>

        <div class="p-5 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700 flex gap-3">
            <button onclick="closePartialModal()" class="flex-1 py-3 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                Cancel
            </button>
            <button onclick="submitPartialPayment()" class="flex-1 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Record Payment
            </button>
        </div>
    </div>
</div>

<!-- ==================== Register Open Modal ==================== -->
<div id="open-register-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-xl font-bold">Open Cash Register</h3>
            <button onclick="closeOpenRegisterModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="open-register-form" class="p-5 space-y-5">
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg text-sm text-yellow-800 dark:text-yellow-200">
                No cash register is currently open for this user. You must open one before processing sales.
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Opening Balance (counted cash)</label>
                <input type="number" name="opening_balance" step="0.01" min="0" required autofocus
                       class="w-full text-2xl text-right font-bold p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Notes (optional)</label>
                <textarea name="notes" rows="3" class="w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600"></textarea>
            </div>

            <!-- Buttons INSIDE the form now -->
            <div class="pt-4 flex gap-3">
                <button type="button" onclick="closeOpenRegisterModal()" class="flex-1 py-3 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    Cancel
                </button>
                <button type="submit" class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                    Open Register
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== Register Details Modal ==================== -->
<div id="register-details-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg">
        <div class="p-5 border-b dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-xl font-bold">Cash Register Session</h3>
            <button onclick="closeRegisterDetailsModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-5 space-y-4" id="register-details-content">
            <!-- Filled by JavaScript -->
        </div>

        <div class="p-5 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700 flex gap-3">
            <button onclick="closeRegisterDetailsModal()" class="flex-1 py-3 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                Close
            </button>
            <button onclick="showCloseRegisterForm()" class="w-full py-3 bg-red-600 ...">
                Close & Reconcile Register
            </button>
        </div>
    </div>
</div>

<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .line-clamp-2 { 
        display: -webkit-box; 
        -webkit-line-clamp: 2; 
        -webkit-box-orient: vertical; 
        overflow: hidden; 
    }
    .active\:scale-95:active { transform: scale(0.95); }
    .active\:scale-98:active { transform: scale(0.98); }
</style>

<script>
let cart = [];
let selectedPaymentMethod = '';
let currentRegister = null;

function addToCart(type, id, name, price) {
    const itemKey = `${type}-${id}`;
    const existingItem = cart.find(item => item.key === itemKey);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ key: itemKey, type, id, name, price: parseFloat(price), quantity: 1 });
    }
    renderCart();
    updateTotals();
}

function removeFromCart(key) {
    cart = cart.filter(item => item.key !== key);
    renderCart();
    updateTotals();
}

function updateQuantity(key, change) {
    const item = cart.find(item => item.key === key);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) removeFromCart(key);
        else { renderCart(); updateTotals(); }
    }
}

function renderCart() {
    const container = document.getElementById('cart-items');
    if (cart.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cart is empty</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Add items to get started</p>
            </div>`;
        return;
    }
    container.innerHTML = cart.map(item => `
        <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate flex items-center gap-2">
                    ${item.name}
                    ${item.source ? `<span class="text-xs font-medium px-1.5 py-0.5 rounded bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300">
                        ${item.source === 'appointment' ? 'Treatment' : 'Rx'}
                    </span>` : ''}
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">$${item.price.toFixed(2)} each</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="updateQuantity('${item.key}', -1)" class="w-8 h-8 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 active:scale-95 transition-all">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                </button>
                <span class="w-8 text-center text-sm font-semibold text-gray-900 dark:text-white">${item.quantity}</span>
                <button onclick="updateQuantity('${item.key}', 1)" class="w-8 h-8 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 active:scale-95 transition-all">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>
            <div class="text-right min-w-[60px]">
                <p class="text-sm font-bold text-gray-900 dark:text-white">$${(item.price * item.quantity).toFixed(2)}</p>
            </div>
            <button onclick="removeFromCart('${item.key}')" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 active:scale-95 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    `).join('');
}

function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const taxRate = parseFloat(document.getElementById('tax-input').value) || 0;
    const discount = parseFloat(document.getElementById('discount-input').value) || 0;
    const taxAmount = (subtotal * taxRate) / 100;
    const grandTotal = Math.max(0, subtotal + taxAmount - discount);
    document.getElementById('items-count').textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.getElementById('subtotal-amount').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax-amount').textContent = '$' + taxAmount.toFixed(2);
    document.getElementById('grand-total').textContent = '$' + grandTotal.toFixed(2);
}

function openPartialPaymentModal() {
    if (!currentRegister) {
        alert("No cash register is currently open.\nYou must open a register first.");
        showOpenRegisterModal();
        return;
    }
    if (cart.length === 0) return alert("Cart is empty");
    if (!document.getElementById('patient-select').value) return alert("Please select patient");
    const total = parseFloat(document.getElementById('grand-total').textContent.replace(/[^0-9.]/g, '')) || 0;
    document.getElementById('partial-total').textContent = '$' + total.toFixed(2);
    document.getElementById('partial-amount').value = '';
    updatePartialRemaining();
    document.getElementById('partial-modal').classList.remove('hidden');
}

function closePartialModal() {
    document.getElementById('partial-modal').classList.add('hidden');
}

function updatePartialRemaining() {
    const total = parseFloat(document.getElementById('partial-total').textContent.replace(/[^0-9.]/g, '')) || 0;
    const paidNow = parseFloat(document.getElementById('partial-amount').value) || 0;
    const remaining = Math.max(0, total - paidNow);
    document.getElementById('partial-remaining').textContent = '$' + remaining.toFixed(2);
}

document.getElementById('partial-amount')?.addEventListener('input', updatePartialRemaining);

function submitPartialPayment() {
    const amount = parseFloat(document.getElementById('partial-amount').value) || 0;
    if (amount < 0) return alert("Amount cannot be negative");
    const total = parseFloat(document.getElementById('partial-total').textContent.replace(/[^0-9.]/g, '')) || 0;
    if (amount > total) return alert("Cannot pay more than total due");
    const payload = {
        patient_id: document.getElementById('patient-select').value,
        items: cart.map(item => ({ type: item.type, id: item.id, quantity: item.quantity })),
        tax_rate: parseFloat(document.getElementById('tax-input')?.value) || 0,
        discount_amount: parseFloat(document.getElementById('discount-input')?.value) || 0,
        payment_method: document.getElementById('partial-method').value,
        payment_reference: document.getElementById('partial-reference').value.trim(),
        notes: '',
        amount_paid_now: amount,
        _token: document.querySelector('meta[name="csrf-token"]')?.content
    };
    fetch('{{ route("invoices.pos.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': payload._token,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) throw new Error(data.message || 'Payment failed');

        alert(`Success! Invoice #${data.invoice_number} created.`);

         const printUrl = '{{ url("") }}/invoices/' + data.invoice_id + '/print';
        window.open(printUrl, '_blank');

        clearCart();
        closePartialModal();
        loadRegisterStatus();
    })
    .catch(err => alert('Error: ' + err.message));
    }

function openPaymentModal(method) {
    if (!currentRegister) {
        alert("No cash register is currently open.\nYou must open a register first.");
        showOpenRegisterModal();
        return;
    }
    selectedPaymentMethod = method;
    const modal = document.getElementById('payment-modal');
    const grandTotal = parseFloat(document.getElementById('grand-total').textContent.replace('$', '')) || 0;
    document.getElementById('modal-grand-total').textContent = '$' + grandTotal.toFixed(2);
    document.getElementById('cash-section').classList.add('hidden');
    document.getElementById('card-section').classList.add('hidden');
    document.getElementById('other-section').classList.add('hidden');
    if (method === 'cash') {
        document.getElementById('cash-section').classList.remove('hidden');
        document.getElementById('cash-received').value = grandTotal.toFixed(2);
        updateCashChange();
    } else if (method === 'card') {
        document.getElementById('card-section').classList.remove('hidden');
    } else if (method === 'other') {
        document.getElementById('other-section').classList.remove('hidden');
    }
    modal.classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('payment-modal').classList.add('hidden');
}

function addCashDenomination(amount) {
    const current = parseFloat(document.getElementById('cash-received').value) || 0;
    document.getElementById('cash-received').value = (current + amount).toFixed(2);
    updateCashChange();
}

function updateCashChange() {
    const grandTotal = parseFloat(document.getElementById('modal-grand-total').textContent.replace('$', '')) || 0;
    const received = parseFloat(document.getElementById('cash-received').value) || 0;
    const change = Math.max(0, received - grandTotal);
    document.getElementById('cash-change').textContent = '$' + change.toFixed(2);
    const btn = document.getElementById('complete-payment-btn');
    if (received >= grandTotal) {
        btn.classList.remove('opacity-60');
        btn.disabled = false;
    } else {
        btn.classList.add('opacity-60');
        btn.disabled = true;
    }
}

function clearCart() {
    if (cart.length > 0 && !confirm('Are you sure you want to clear the cart?')) return;
    cart = [];
    document.getElementById('patient-select').value = '';
    document.getElementById('discount-input').value = '0';
    renderCart();
    updateTotals();
}

function clearAll() {
    clearCart();
}

function filterCategory(category) {
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-gray-900', 'text-white', 'dark:bg-white', 'dark:text-gray-900');
        btn.classList.add('bg-gray-100', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
    });
    event.target.classList.remove('bg-gray-100', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
    event.target.classList.add('active', 'bg-gray-900', 'text-white', 'dark:bg-white', 'dark:text-gray-900');
    document.querySelectorAll('.product-card').forEach(product => {
        product.style.display = (category === 'all' || product.dataset.category === category) ? 'block' : 'none';
    });
}

function processPayment() {
    if (!currentRegister) {
        alert("No active cash register session.\nPlease open the register.");
        showOpenRegisterModal();
        return;
    }
    const patientId = document.getElementById('patient-select').value;
    if (!patientId) return alert('Please select a patient first');
    if (cart.length === 0) return alert('Cart is empty');
    const grandTotal = parseFloat(document.getElementById('grand-total').textContent.replace('$', ''));
    if (selectedPaymentMethod === 'cash') {
        const amountReceived = parseFloat(document.getElementById('cash-received').value) || 0;
        if (amountReceived < grandTotal) return alert('Insufficient amount received.');
    }
    const formData = {
        patient_id: patientId,
        items: cart.map(item => ({ type: item.type, id: item.id, quantity: item.quantity })),
        tax_rate: parseFloat(document.getElementById('tax-input').value) || 0,
        discount_amount: parseFloat(document.getElementById('discount-input').value) || 0,
        payment_method: selectedPaymentMethod,
        amount_paid_now: selectedPaymentMethod === 'cash' 
            ? parseFloat(document.getElementById('cash-received').value) || 0 
            : grandTotal,
        notes: '',
        _token: '{{ csrf_token() }}'
    };
    fetch('{{ route("invoices.pos.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const change = selectedPaymentMethod === 'cash' ? (formData.amount_paid_now - grandTotal).toFixed(2) : '0.00';
            alert(`Sale completed!\nInvoice: ${data.invoice_number}\nTotal: $${data.total}\nPayment: ${selectedPaymentMethod.toUpperCase()}${selectedPaymentMethod === 'cash' ? '\nChange: $' + change : ''}`);

            const printUrl = '{{ url("") }}/invoices/' + data.invoice_id + '/print';
            window.open(printUrl, '_blank');

            closePaymentModal();
            clearCart();
            loadRegisterStatus();
        } else {
            alert('Error: ' + (data.message || 'Payment failed'));
        }
    })
    .catch(() => alert('Network error. Please try again.'));
}

function saveAsDraft() {
    if (cart.length === 0) return alert('Cart is empty');
    alert('Draft saved successfully');
}

function showSalesStats() {
    document.getElementById('sales-stats-modal').classList.remove('hidden');
}

function closeSalesStats() {
    document.getElementById('sales-stats-modal').classList.add('hidden');
}

function loadRegisterStatus() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '{{ route("cash-registers.current") }}', true);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                var data = JSON.parse(xhr.responseText);
                var label = document.getElementById('register-label');
                var subtitle = document.getElementById('register-subtitle');
                if (label && subtitle) {
                    if (data.open && data.register) {
                        currentRegister = data.register;
                        label.textContent = `Register: ${data.register.id} (Open)`;
                        subtitle.innerHTML = `<span class="text-green-600 dark:text-green-400">Opened at ${data.register.opened_at_formatted} • $${data.register.opening_balance_formatted}</span>`;
                    } else {
                        currentRegister = null;
                        label.textContent = 'Register: Not Open';
                        subtitle.innerHTML = '<span class="text-red-600 dark:text-red-400">Click to open register</span>';
                    }
                }
            } catch (e) {
                console.error("JSON parse error:", e);
            }
        }
    };
    xhr.onerror = function () {
        console.error("Network error");
    };
    xhr.send();
}

document.getElementById('open-register-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Opening...';

    const formData = new FormData(this);

    fetch('{{ route("cash-registers.open") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cash register opened successfully!');
            closeOpenRegisterModal();
            loadRegisterStatus();  // Refresh status bar
        } else {
            alert(data.message || 'Failed to open register');
        }
    })
    .catch(error => {
        alert('Network error: ' + error.message);
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

function showOpenRegisterModal() {
    document.getElementById('open-register-modal').classList.remove('hidden');
}

function closeOpenRegisterModal() {
    document.getElementById('open-register-modal').classList.add('hidden');
}

function showRegisterDetails() {
    if (!currentRegister) {
        showOpenRegisterModal();
        return;
    }
    const content = document.getElementById('register-details-content');
    if (!content) return;
    content.innerHTML = `
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <div class="py-3 flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Register ID</span>
                <span class="text-sm font-semibold">#${currentRegister.id}</span>
            </div>
            <div class="py-3 flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Opened At</span>
                <span class="text-sm font-semibold">${currentRegister.opened_at_formatted}</span>
            </div>
            <div class="py-3 flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Opening Balance</span>
                <span class="text-sm font-semibold text-green-600 dark:text-green-400">$${currentRegister.opening_balance_formatted}</span>
            </div>
            <div class="py-3 flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Cash Sales</span>
                <span class="text-sm font-semibold">$${currentRegister.cash_sales_formatted || '0.00'}</span>
            </div>
            <div class="py-3 flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Card Sales</span>
                <span class="text-sm font-semibold">$${currentRegister.card_sales_formatted || '0.00'}</span>
            </div>
            <div class="py-3 flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Total Transactions</span>
                <span class="text-sm font-semibold">${currentRegister.transaction_count || 0}</span>
            </div>
            <div class="py-3 flex justify-between items-center font-bold">
                <span class="text-base text-gray-900 dark:text-white">Expected Cash</span>
                <span class="text-base text-gray-900 dark:text-white">$${currentRegister.expected_closing_formatted || '0.00'}</span>
            </div>
        </div>
    `;
    document.getElementById('register-details-modal').classList.remove('hidden');
}

function closeRegisterDetailsModal() {
    document.getElementById('register-details-modal').classList.add('hidden');
}

function showCloseRegisterForm() {
    const counted = prompt("Enter counted cash in drawer:", currentRegister.expected_closing_formatted || "0.00");
    if (counted === null) return;
    const amount = parseFloat(counted);
    if (isNaN(amount) || amount < 0) {
        alert("Please enter a valid amount.");
        return;
    }
    if (!confirm(`Close register?\nCounted: $${amount.toFixed(2)}\nExpected: $${currentRegister.expected_closing_formatted || "?"}`)) return;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route("cash-registers.close", ":id") }}'.replace(':id', currentRegister.id), true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                var data = JSON.parse(xhr.responseText);
                if (data.success) {
                    alert(`Register closed successfully.\nDifference: $${data.difference || 0}`);
                    currentRegister = null;
                    loadRegisterStatus();
                    closeRegisterDetailsModal();
                    window.location.href = '{{ route("home") }}';
                } else {
                    alert(data.message || "Failed to close register");
                }
            } catch (e) {
                alert("Response error");
            }
        } else {
            alert("Server error: " + xhr.status);
        }
    };
    xhr.onerror = function () {
        alert("Connection error");
    };
    xhr.send(JSON.stringify({
        actual_closing_balance: amount,
        notes: 'Closed from POS terminal'
    }));
}

function showLastTransaction() {
    document.getElementById('last-transaction-modal').classList.remove('hidden');
}

function closeLastTransaction() {
    document.getElementById('last-transaction-modal').classList.add('hidden');
}

function viewInvoice() {
    alert('Opening invoice view...');
}

function printLastInvoice() {
    alert('Printing last invoice...');
}

document.getElementById('search-products').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        const name = card.querySelector('h3').textContent.toLowerCase();
        card.style.display = name.includes(term) ? 'block' : 'none';
    });
});

function updateTime() {
    const now = new Date();
    const options = { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true };
    const timeElement = document.getElementById('current-time');
    if (timeElement) timeElement.textContent = now.toLocaleString('en-US', options);
}

document.addEventListener('DOMContentLoaded', function () {
    updateTotals();
    updateTime();
    setInterval(updateTime, 60000);
    if (window.preselectedPatientId) {
        const patientSelect = document.getElementById('patient-select');
        if (patientSelect) patientSelect.value = window.preselectedPatientId;
    }
    if (window.preloadedItems && window.preloadedItems.length > 0) {
        window.preloadedItems.forEach(item => {
            const key = `${item.type}-${item.id}`;
            if (cart.some(cartItem => cartItem.key === key)) return;
            cart.push({
                key,
                type: item.type,
                id: item.id,
                name: item.name + (item.source ? ` (${item.source})` : ''),
                price: parseFloat(item.price),
                quantity: parseInt(item.quantity) || 1,
                source: item.source || null
            });
        });
        renderCart();
        updateTotals();
    }
    loadRegisterStatus();
});
</script>

@endsection