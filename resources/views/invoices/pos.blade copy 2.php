@extends('layouts.pos')
@section('title', __('file.point_of_sale'))

@section('content')
<div class="w-full min-h-screen lg:h-screen flex flex-col lg:flex-row bg-gray-50 dark:bg-gray-900">
    
    <!-- Left Panel: Product Grid -->
     <div class="flex-1 flex flex-col overflow-hidden lg:w-1/2 bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 ">
        <div class="p-3 border-b border-gray-200 dark:border-gray-700 space-y-2 flex-shrink-0">
            

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Patient <span class="text-red-500">*</span>
                </label>
                <select id="patient-select" class="w-full px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">Select Patient</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }} - {{ $patient->medical_record_number }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Cart Items: <span id="items-count" class="font-bold">0</span></span>
                <button type="button" onclick="clearAll()" class="px-3 py-1 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                    Clear All
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-3" style="max-height: calc(100vh - 520px);">
            <div id="cart-items" class="space-y-1.5">
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Cart is empty</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Add items to get started</p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 p-3 space-y-2 flex-shrink-0">
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                    <span id="subtotal-amount" class="font-semibold text-gray-900 dark:text-white">$0.00</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="text-gray-600 dark:text-gray-400">Tax</span>
                        <input type="number" id="tax-input" value="8" min="0" max="100" step="0.1" 
                               class="w-12 px-1.5 py-0.5 text-xs border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white text-center"
                               onchange="updateTotals()">
                        <span class="text-gray-600 dark:text-gray-400">%</span>
                    </div>
                    <span id="tax-amount" class="font-semibold text-gray-900 dark:text-white">$0.00</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600 dark:text-gray-400">Discount</span>
                    <input type="number" id="discount-input" value="0" min="0" step="0.01" 
                           class="w-20 px-1.5 py-0.5 text-xs border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white text-right"
                           placeholder="0.00"
                           onchange="updateTotals()">
                </div>
            </div>

            <div class="pt-2 border-t border-gray-300 dark:border-gray-600">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Total Amount</span>
                    <span id="grand-total" class="text-2xl font-bold text-gray-900 dark:text-white">$0.00</span>
                </div>

                <div class="space-y-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-1.5">
                            <button onclick="selectPaymentMethod('cash')" class="payment-method-btn active px-3 py-2 text-xs font-medium rounded-lg border-2 border-gray-900 dark:border-white bg-gray-900 dark:bg-white text-white dark:text-gray-900 transition-all">
                                <svg class="w-4 h-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Cash
                            </button>
                            <button onclick="selectPaymentMethod('card')" class="payment-method-btn px-3 py-2 text-xs font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-gray-400 dark:hover:border-gray-500 transition-all">
                                <svg class="w-4 h-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Card
                            </button>
                            <button onclick="selectPaymentMethod('other')" class="payment-method-btn px-3 py-2 text-xs font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-gray-400 dark:hover:border-gray-500 transition-all">
                                <svg class="w-4 h-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Other
                            </button>
                        </div>
                    </div>

                    <div id="cash-payment-section" class="space-y-1.5">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Amount Received
                            </label>
                            <input type="number" id="amount-received" value="0" min="0" step="0.01" 
                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white text-right font-semibold"
                                   placeholder="0.00"
                                   oninput="calculateChange()">
                        </div>
                        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Change</span>
                            <span id="change-amount" class="text-lg font-bold text-green-600 dark:text-green-400">$0.00</span>
                        </div>
                        <div class="grid grid-cols-4 gap-1">
                            <button onclick="quickAmount(10)" class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600">$10</button>
                            <button onclick="quickAmount(20)" class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600">$20</button>
                            <button onclick="quickAmount(50)" class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600">$50</button>
                            <button onclick="quickAmount(100)" class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600">$100</button>
                        </div>
                    </div>

                    <button onclick="processPayment()" class="w-full py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-semibold rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center gap-2 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Complete Payment $<span id="charge-amount">0.00</span>
                    </button>
                    <div class="grid grid-cols-3 gap-1.5">
                        <button onclick="saveAsDraft()" class="py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-xs">
                            <svg class="w-4 h-4 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                            Draft
                        </button>
                        <button onclick="holdSale()" class="py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-xs">
                            <svg class="w-4 h-4 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Hold
                        </button>
                        <button onclick="clearCart()" class="py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-xs">
                            <svg class="w-4 h-4 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Panel: Cart & Checkout -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-3 py-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Register: Main Counter</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" id="current-time"></p>
                    </div>
                </div>
                <div class="flex gap-1">
                    <button onclick="showRegisterDetails()" class="p-1.5 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Register Details">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                    <button onclick="showLastTransaction()" class="p-1.5 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Last Transaction">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-2">
                    <p class="text-xs text-blue-600 dark:text-blue-400">Today's Sales</p>
                    <p class="text-sm font-bold text-blue-900 dark:text-blue-300">$2,450.00</p>
                    <p class="text-xs text-blue-500 dark:text-blue-400">12 Transactions</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-2">
                    <p class="text-xs text-green-600 dark:text-green-400">Cash in Hand</p>
                    <p class="text-sm font-bold text-green-900 dark:text-green-300">$1,850.00</p>
                    <p class="text-xs text-green-500 dark:text-green-400">Opening: $200</p>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-2">
                    <p class="text-xs text-purple-600 dark:text-purple-400">Card Sales</p>
                    <p class="text-sm font-bold text-purple-900 dark:text-purple-300">$600.00</p>
                    <p class="text-xs text-purple-500 dark:text-purple-400">8 Payments</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="grid grid-cols-4 gap-2 text-xs">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Invoice #:</span>
                        <p class="font-semibold text-gray-900 dark:text-white">POS-{{ date('YmdHis') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Type:</span>
                        <p class="font-semibold text-gray-900 dark:text-white">Standard</p>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Date:</span>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ date('M d, Y') }}</p>
                    </div>
                </div>

                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" id="search-products" placeholder="Search products or services..."
                           class="block w-full pl-8 pr-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-sm">
                </div>
            </div>

            <div class="flex gap-1.5 mt-2 overflow-x-auto pb-1 hide-scrollbar">
                <button onclick="filterCategory('all')" class="category-btn active px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap bg-gray-900 text-white dark:bg-white dark:text-gray-900">
                    All Items
                </button>
                <button onclick="filterCategory('consultation')" class="category-btn px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                    Consultations
                </button>
                <button onclick="filterCategory('treatment')" class="category-btn px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                    Treatments
                </button>
                <button onclick="filterCategory('medication')" class="category-btn px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                    Medications
                </button>
                <button onclick="filterCategory('lab')" class="category-btn px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                    Lab Tests
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-2 bg-gray-50 dark:bg-gray-900" style="max-height: calc(100vh - 140px);">
            <div id="products-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2">
                
                @foreach($services as $service)
                <button type="button" 
                        class="product-card p-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-900 dark:hover:border-gray-500 hover:shadow-md transition-all text-left group"
                        data-item-type="service"
                        data-item-id="{{ $service->id }}"
                        data-item-name="{{ $service->name }}"
                        data-item-price="{{ $service->price }}"
                        data-category="consultation"
                        onclick="addToCart('service', {{ $service->id }}, '{{ addslashes($service->name) }}', {{ $service->price }})">
                    <div class="flex flex-col h-full">
                        <div class="w-full h-24 bg-gray-100 dark:bg-gray-700 rounded-md mb-2 flex items-center justify-center overflow-hidden">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        
                        <div class="flex-1">
                            <div class="flex items-center gap-1 mb-1">
                                <span class="text-xs px-1.5 py-0.5 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded">Service</span>
                            </div>
                            <h3 class="font-semibold text-xs text-gray-900 dark:text-white mb-1 line-clamp-2">
                                {{ $service->name }}
                            </h3>
                        </div>
                        <div class="flex items-center justify-between mt-auto pt-1.5 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                ${{ number_format($service->price, 2) }}
                            </span>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </div>
                </button>
                @endforeach

                @foreach($inventoryItems as $item)
                <button type="button" 
                        class="product-card p-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-900 dark:hover:border-gray-500 hover:shadow-md transition-all text-left group {{ $item->current_stock <= 0 ? 'opacity-60' : '' }}"
                        data-item-type="inventory"
                        data-item-id="{{ $item->id }}"
                        data-item-name="{{ $item->name }} @if($item->generic_name) ({{ $item->generic_name }}) @endif"
                        data-item-price="{{ $item->unit_price }}"
                        data-stock="{{ $item->current_stock }}"
                        data-category="medication"
                        onclick="{{ $item->current_stock > 0 ? "addToCart('inventory', {$item->id}, '" . addslashes($item->name . ($item->generic_name ? ' (' . $item->generic_name . ')' : '')) . "', {$item->unit_price})" : "alert('Out of stock!')" }}"
                        {{ $item->current_stock <= 0 ? 'disabled' : '' }}>
                    <div class="flex flex-col h-full">
                        <div class="w-full h-24 bg-gray-100 dark:bg-gray-700 rounded-md mb-2 flex items-center justify-center overflow-hidden">
                            @if($item->medicine_image)
                                <img src="{{ $item->medicine_image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <div class="flex items-center gap-1 mb-1 flex-wrap">
                                <span class="text-xs px-1.5 py-0.5 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded">Medicine</span>
                                @if($item->current_stock < $item->reorder_point && $item->current_stock > 0)
                                    <span class="text-xs px-1.5 py-0.5 bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300 rounded">Low</span>
                                @elseif($item->current_stock <= 0)
                                    <span class="text-xs px-1.5 py-0.5 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 rounded">Out</span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-xs text-gray-900 dark:text-white mb-0.5 line-clamp-2">
                                {{ $item->name }}
                            </h3>
                            @if($item->generic_name)
                                <p class="text-xs text-gray-500 dark:text-gray-400">({{ $item->generic_name }})</p>
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Stock: <strong>{{ $item->current_stock }}</strong>
                            </p>
                        </div>
                        <div class="flex items-center justify-between mt-auto pt-1.5 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                ${{ number_format($item->unit_price, 2) }}
                            </span>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </div>
                </button>
                @endforeach
            </div>
        </div>
    </div>

</div>

<!-- Register Details Modal -->
<div id="register-details-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cash Register Details</h3>
            <button onclick="closeRegisterDetails()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-4 space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Register Name</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">Main Counter</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Opened At</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">08:30 AM</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Opening Balance</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">$200.00</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Cash Sales</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">$1,650.00</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Card Sales</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">$600.00</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Total Transactions</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">12</span>
            </div>
            <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <span class="text-base font-semibold text-gray-900 dark:text-white">Expected Cash</span>
                    <span class="text-base font-bold text-gray-900 dark:text-white">$1,850.00</span>
                </div>
            </div>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-b-lg">
            <button onclick="closeRegister()" class="w-full py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors text-sm">
                Close Register
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

<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .line-clamp-2 { 
        display: -webkit-box; 
        -webkit-line-clamp: 2; 
        -webkit-box-orient: vertical; 
        overflow: hidden; 
    }
</style>

<script>
let cart = [];
let selectedPaymentMethod = 'cash';

function addToCart(type, id, name, price) {
    const itemKey = `${type}-${id}`;
    const existingItem = cart.find(item => item.key === itemKey);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ key: itemKey, type: type, id: id, name: name, price: parseFloat(price), quantity: 1 });
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
            <div class="text-center py-8">
                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-xs text-gray-500 dark:text-gray-400">Cart is empty</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Add items to get started</p>
            </div>`;
        return;
    }
    
    container.innerHTML = cart.map(item => `
        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <div class="flex-1 min-w-0">
                <h4 class="text-xs font-semibold text-gray-900 dark:text-white truncate">${item.name}</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">$${item.price.toFixed(2)} each</p>
            </div>
            <div class="flex items-center gap-1">
                <button onclick="updateQuantity('${item.key}', -1)" class="w-6 h-6 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-3 h-3 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                </button>
                <span class="w-6 text-center text-xs font-semibold text-gray-900 dark:text-white">${item.quantity}</span>
                <button onclick="updateQuantity('${item.key}', 1)" class="w-6 h-6 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-3 h-3 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>
            <div class="text-right min-w-[55px]">
                <p class="text-xs font-bold text-gray-900 dark:text-white">$${(item.price * item.quantity).toFixed(2)}</p>
            </div>
            <button onclick="removeFromCart('${item.key}')" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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
    document.getElementById('charge-amount').textContent = grandTotal.toFixed(2);
    
    calculateChange();
}

function selectPaymentMethod(method) {
    selectedPaymentMethod = method;
    
    document.querySelectorAll('.payment-method-btn').forEach(btn => {
        btn.classList.remove('active', 'border-gray-900', 'dark:border-white', 'bg-gray-900', 'dark:bg-white', 'text-white', 'dark:text-gray-900');
        btn.classList.add('border-gray-300', 'dark:border-gray-600', 'bg-white', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300');
    });
    
    event.target.classList.remove('border-gray-300', 'dark:border-gray-600', 'bg-white', 'dark:bg-gray-800', 'text-gray-700', 'dark:text-gray-300');
    event.target.classList.add('active', 'border-gray-900', 'dark:border-white', 'bg-gray-900', 'dark:bg-white', 'text-white', 'dark:text-gray-900');
    
    if (method === 'cash') {
        document.getElementById('cash-payment-section').classList.remove('hidden');
    } else {
        document.getElementById('cash-payment-section').classList.add('hidden');
    }
}

function calculateChange() {
    if (selectedPaymentMethod !== 'cash') return;
    
    const grandTotal = parseFloat(document.getElementById('grand-total').textContent.replace('$', ''));
    const amountReceived = parseFloat(document.getElementById('amount-received').value) || 0;
    const change = Math.max(0, amountReceived - grandTotal);
    
    document.getElementById('change-amount').textContent = '$' + change.toFixed(2);
}

function quickAmount(amount) {
    const grandTotal = parseFloat(document.getElementById('grand-total').textContent.replace('$', ''));
    const currentAmount = parseFloat(document.getElementById('amount-received').value) || 0;
    document.getElementById('amount-received').value = (currentAmount + amount).toFixed(2);
    calculateChange();
}

function clearCart() {
    if (cart.length > 0 && !confirm('Are you sure you want to clear the cart?')) return;
    cart = [];
    document.getElementById('patient-select').value = '';
    document.getElementById('discount-input').value = '0';
    document.getElementById('amount-received').value = '0';
    selectPaymentMethod('cash');
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
    const patientId = document.getElementById('patient-select').value;
    if (!patientId) return alert('Please select a patient first');
    if (cart.length === 0) return alert('Cart is empty');
    
    const grandTotal = parseFloat(document.getElementById('grand-total').textContent.replace('$', ''));
    
    if (selectedPaymentMethod === 'cash') {
        const amountReceived = parseFloat(document.getElementById('amount-received').value) || 0;
        if (amountReceived < grandTotal) {
            return alert('Insufficient amount received. Please enter the correct amount.');
        }
    }
    
    const formData = {
        patient_id: patientId,
        items: cart.map(item => ({ type: item.type, id: item.id, quantity: item.quantity })),
        tax_rate: parseFloat(document.getElementById('tax-input').value) || 0,
        discount_amount: parseFloat(document.getElementById('discount-input').value) || 0,
        payment_method: selectedPaymentMethod,
        amount_received: selectedPaymentMethod === 'cash' ? parseFloat(document.getElementById('amount-received').value) || 0 : grandTotal,
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
            const change = selectedPaymentMethod === 'cash' ? (formData.amount_received - grandTotal).toFixed(2) : '0.00';
            alert(`Sale completed!\nInvoice: ${data.invoice_number}\nTotal: $${data.total}\nPayment: ${selectedPaymentMethod.toUpperCase()}${selectedPaymentMethod === 'cash' ? '\nChange: $' + change : ''}`);
            
            const printWindow = window.open(
                '{{ url("invoices") }}/' + data.invoice_id + '/print',
                '_blank'
            );
            
            if (printWindow) {
                printWindow.onload = function() {
                    printWindow.focus();
                    printWindow.print();
                };
            }
            
            clearCart();
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

function holdSale() {
    if (cart.length === 0) return alert('Cart is empty');
    alert('Sale held successfully');
}

function showRegisterDetails() {
    document.getElementById('register-details-modal').classList.remove('hidden');
}

function closeRegisterDetails() {
    document.getElementById('register-details-modal').classList.add('hidden');
}

function closeRegister() {
    if (confirm('Are you sure you want to close the register? This will end your session.')) {
        alert('Register closed successfully');
        closeRegisterDetails();
    }
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
        card.style.display = card.dataset.itemName.toLowerCase().includes(term) ? 'block' : 'none';
    });
});

function updateTime() {
    const now = new Date();
    document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
}

document.addEventListener('DOMContentLoaded', () => {
    updateTotals();
    updateTime();
    setInterval(updateTime, 60000);
});
</script>

@endsection