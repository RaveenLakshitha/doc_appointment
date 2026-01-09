@extends('layouts.pos')
@section('title', __('file.point_of_sale'))

@section('content')
<div class="w-full min-h-screen lg:h-screen flex flex-col lg:flex-row bg-gray-50 dark:bg-gray-900">
    
    <!-- Left Panel - Products & Services -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-3 py-2">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <!-- Invoice Info -->
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
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Due:</span>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ date('M d, Y') }}</p>
                    </div>
                </div>

                <!-- Search Bar -->
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

            <!-- Category Tabs -->
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

        <!-- Products Grid -->
        <div class="flex-1 overflow-y-auto p-2 bg-gray-50 dark:bg-gray-900" style="max-height: calc(100vh - 140px);">
            <div id="products-grid" 
                 class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2">
                
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
                        <!-- Item Image -->
                        <div class="w-full h-24 bg-gray-100 dark:bg-gray-700 rounded-md mb-2 flex items-center justify-center overflow-hidden">
                            @if(isset($service->image) && $service->image)
                                <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            @endif
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

                @if(isset($inventoryItems))
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
                            <!-- Item Image -->
                            <div class="w-full h-24 bg-gray-100 dark:bg-gray-700 rounded-md mb-2 flex items-center justify-center overflow-hidden">
                                @if(isset($item->image) && $item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
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
                @endif
            </div>
        </div>
    </div>

    <!-- Right Panel - Cart & Checkout -->
    <div class="w-full md:w-96 lg:w-[420px] bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex flex-col">
        <!-- Patient Selection & Actions -->
        <div class="p-3 border-b border-gray-200 dark:border-gray-700 space-y-2 flex-shrink-0">
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Patient <span class="text-red-500">*</span>
                </label>
                <select id="patient-select" class="w-full px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">Select Patient</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }} - {{ $patient->patient_id }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 dark:text-gray-400" id="current-time"></span>
                <button type="button" onclick="clearAll()" class="px-3 py-1 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                    Clear All
                </button>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-3" style="max-height: calc(100vh - 380px);">
            <h3 class="text-xs font-semibold text-gray-900 dark:text-white mb-2">Cart Items (<span id="items-count">0</span>)</h3>
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

        <!-- Totals & Checkout -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-3 space-y-2 flex-shrink-0">
            <!-- Tax & Discount -->
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

            <!-- Grand Total -->
            <div class="pt-2 border-t border-gray-300 dark:border-gray-600">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Total</span>
                    <span id="grand-total" class="text-2xl font-bold text-gray-900 dark:text-white">$0.00</span>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-1.5">
                    <button onclick="processPayment()" class="w-full py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-semibold rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Charge $<span id="charge-amount">0.00</span>
                    </button>
                    <div class="grid grid-cols-2 gap-1.5">
                        <button onclick="saveAsDraft()" class="py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-xs">
                            Save Draft
                        </button>
                        <button onclick="clearCart()" class="py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-xs">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
let cart = [];

// Fixed addToCart function with proper parameters
function addToCart(type, id, name, price) {
    const itemKey = `${type}-${id}`;
    const existingItem = cart.find(item => item.key === itemKey);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ 
            key: itemKey,
            type: type,
            id: id, 
            name: name, 
            price: parseFloat(price), 
            quantity: 1 
        });
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
        if (item.quantity <= 0) {
            removeFromCart(key);
        } else {
            renderCart();
            updateTotals();
        }
    }
}

function renderCart() {
    const cartContainer = document.getElementById('cart-items');
    
    if (cart.length === 0) {
        cartContainer.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-xs text-gray-500 dark:text-gray-400">Cart is empty</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Add items to get started</p>
            </div>
        `;
        return;
    }
    
    cartContainer.innerHTML = cart.map(item => `
        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <div class="flex-1 min-w-0">
                <h4 class="text-xs font-semibold text-gray-900 dark:text-white truncate">${item.name}</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">$${item.price.toFixed(2)} each</p>
            </div>
            <div class="flex items-center gap-1">
                <button onclick="updateQuantity('${item.key}', -1)" class="w-6 h-6 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-3 h-3 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/>
                    </svg>
                </button>
                <span class="w-6 text-center text-xs font-semibold text-gray-900 dark:text-white">${item.quantity}</span>
                <button onclick="updateQuantity('${item.key}', 1)" class="w-6 h-6 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-3 h-3 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            </div>
            <div class="text-right min-w-[55px]">
                <p class="text-xs font-bold text-gray-900 dark:text-white">$${(item.price * item.quantity).toFixed(2)}</p>
            </div>
            <button onclick="removeFromCart('${item.key}')" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
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
    const buttons = document.querySelectorAll('.category-btn');
    buttons.forEach(btn => {
        btn.classList.remove('active', 'bg-gray-900', 'text-white', 'dark:bg-white', 'dark:text-gray-900');
        btn.classList.add('bg-gray-100', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
    });
    
    event.target.classList.remove('bg-gray-100', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
    event.target.classList.add('active', 'bg-gray-900', 'text-white', 'dark:bg-white', 'dark:text-gray-900');
    
    const products = document.querySelectorAll('.product-card');
    products.forEach(product => {
        if (category === 'all' || product.dataset.category === category) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

function processPayment() {
    const patientId = document.getElementById('patient-select').value;
    if (!patientId) {
        alert('Please select a patient first');
        return;
    }
    
    if (cart.length === 0) {
        alert('Cart is empty');
        return;
    }
    
    // Here you would submit the form or make an API call
    const total = document.getElementById('charge-amount').textContent;
    alert('Processing payment for $' + total);
    
    // After successful payment, you can:
    // clearCart();
}

function saveAsDraft() {
    const patientId = document.getElementById('patient-select').value;
    if (!patientId) {
        alert('Please select a patient first');
        return;
    }
    
    if (cart.length === 0) {
        alert('Cart is empty');
        return;
    }
    
    // Here you would save the draft via API
    alert('Draft saved successfully');
}

// Search functionality
document.getElementById('search-products').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const name = product.dataset.itemName.toLowerCase();
        if (name.includes(searchTerm)) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
});

// Update current time
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
    });
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateTotals();
    updateTime();
    setInterval(updateTime, 60000); // Update every minute
});
</script>

@endsection