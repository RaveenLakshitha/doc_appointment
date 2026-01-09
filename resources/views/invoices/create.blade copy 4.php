@extends('layouts.pos')
@section('title', __('file.point_of_sale'))

@section('content')
<div class="w-full min-h-screen lg:h-screen flex flex-col lg:flex-row bg-gray-50 dark:bg-gray-900">
    
    <!-- Left Panel - Products & Services -->
    <div class="flex-1 flex flex-col lg:overflow-hidden">
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Point of Sale</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select items to add to cart</p>
                </div>

                <!-- Search Bar -->
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" id="search-products" placeholder="Search products or services..."
                           class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-sm">
                </div>
            </div>

            <!-- Category Tabs -->
            <div class="flex gap-2 mt-4 overflow-x-auto pb-2 hide-scrollbar">
                <button onclick="filterCategory('all')" class="category-btn active px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap bg-gray-900 text-white dark:bg-white dark:text-gray-900">
                    All Items
                </button>
                <button onclick="filterCategory('consultation')" class="category-btn px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                    Consultations
                </button>
                <button onclick="filterCategory('treatment')" class="category-btn px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                    Treatments
                </button>
                <button onclick="filterCategory('medication')" class="category-btn px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                    Medications
                </button>
                <button onclick="filterCategory('lab')" class="category-btn px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                    Lab Tests
                </button>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900" style="max-height: calc(100vh - 180px);">
                <div id="products-grid" 
                     class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                <!-- Services -->
                @foreach($services as $service)
                <div class="product-card bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:border-gray-900 dark:hover:border-gray-500 transition-all duration-200 cursor-pointer group"
                     onclick="addToCart('service-{{ $service->id }}', '{{ addslashes($service->name) }}', {{ $service->price }}, 'service')"
                     data-category="{{ strtolower($service->category ?? 'consultation') }}">
                    <div class="aspect-square bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/50 dark:to-blue-800/50 flex items-center justify-center p-4">
                        <svg class="w-12 h-12 sm:w-16 sm:h-16 text-blue-600 dark:text-blue-400 group-hover:text-blue-900 dark:group-hover:text-blue-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <div class="p-3 sm:p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1 truncate">
                            {{ $service->name }}
                        </h3>
                        <div class="flex items-center justify-between">
                            <span class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                                ${{ number_format($service->price, 2) }}
                            </span>
                            <div class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Inventory Items -->
                @foreach($inventoryItems as $item)
                <div class="product-card bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:border-gray-900 dark:hover:border-gray-500 transition-all duration-200 cursor-pointer group"
                     onclick="addToCart('inventory-{{ $item->id }}', '{{ addslashes($item->name) }}', {{ $item->selling_price ?? $item->price ?? 0 }}, 'inventory')"
                     data-category="medication">
                    <div class="aspect-square bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/50 dark:to-green-800/50 flex items-center justify-center p-4">
                        <svg class="w-12 h-12 sm:w-16 sm:h-16 text-green-600 dark:text-green-400 group-hover:text-green-900 dark:group-hover:text-green-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2m-2 0H5m0 0h2"/>
                        </svg>
                    </div>
                    <div class="p-3 sm:p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1 truncate">
                            {{ $item->name }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Stock: {{ $item->current_stock }}</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                                ${{ number_format($item->selling_price ?? $item->price ?? 0, 2) }}
                            </span>
                            <div class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Panel - Cart & Checkout -->
     <div class="w-full lg:w-80 bg-white dark:bg-gray-800 border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-gray-700 flex flex-col lg:h-screen">   
        <!-- Patient Selection -->
        <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700 space-y-4 flex-shrink-0">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Patient <span class="text-red-500">*</span>
                </label>
                <select id="patient-select" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">Select Patient</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }} - {{ $patient->patient_id }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Items</p>
                    <p id="items-count" class="text-xl font-bold text-gray-900 dark:text-white">0</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                    <p id="cart-total-preview" class="text-xl font-bold text-gray-900 dark:text-white">$0.00</p>
                </div>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4" style="max-height: calc(100vh - 320px);">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Cart Items</h3>
                <div id="cart-items" class="space-y-2">
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Cart is empty</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Add items to get started</p>
                </div>
            </div>
        </div>

        <!-- Totals & Checkout -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-4 sm:p-6 space-y-4 flex-shrink-0">
            <!-- Tax & Discount -->
            <div class="space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                    <span id="subtotal-amount" class="font-semibold text-gray-900 dark:text-white">$0.00</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600 dark:text-gray-400">Tax</span>
                        <input type="number" id="tax-input" value="8" min="0" max="100" step="0.1" 
                               class="w-14 px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white text-center"
                               onchange="updateTotals()">
                        <span class="text-gray-600 dark:text-gray-400">%</span>
                    </div>
                    <span id="tax-amount" class="font-semibold text-gray-900 dark:text-white">$0.00</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Discount</span>
                    <input type="number" id="discount-input" value="0" min="0" step="0.01" 
                           class="w-24 px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-700 dark:text-white text-right"
                           placeholder="0.00"
                           onchange="updateTotals()">
                </div>
            </div>

            <!-- Grand Total -->
            <div class="pt-4 border-t border-gray-300 dark:border-gray-600">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">Total</span>
                    <span id="grand-total" class="text-3xl font-bold text-gray-900 dark:text-white">$0.00</span>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2">
                    <button onclick="processPayment()" class="w-full py-3.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-semibold rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Charge $<span id="charge-amount">0.00</span>
                    </button>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="saveAsDraft()" class="py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm">
                            Save Draft
                        </button>
                        <button onclick="clearCart()" class="py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm">
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
</style>

<script>
let cart = [];

function addToCart(id, name, price) {
    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ id, name, price, quantity: 1 });
    }
    
    renderCart();
    updateTotals();
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    renderCart();
    updateTotals();
}

function updateQuantity(id, change) {
    const item = cart.find(item => item.id === id);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(id);
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
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-sm text-gray-500 dark:text-gray-400">Cart is empty</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Add items to get started</p>
            </div>
        `;
        return;
    }
    
    cartContainer.innerHTML = cart.map(item => `
        <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate">${item.name}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">$${item.price.toFixed(2)} each</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="updateQuantity(${item.id}, -1)" class="w-7 h-7 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-3 h-3 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/>
                    </svg>
                </button>
                <span class="w-8 text-center text-sm font-semibold text-gray-900 dark:text-white">${item.quantity}</span>
                <button onclick="updateQuantity(${item.id}, 1)" class="w-7 h-7 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-3 h-3 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            </div>
            <div class="text-right min-w-[60px]">
                <p class="text-sm font-bold text-gray-900 dark:text-white">$${(item.price * item.quantity).toFixed(2)}</p>
            </div>
            <button onclick="removeFromCart(${item.id})" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    const grandTotal = subtotal + taxAmount - discount;
    
    document.getElementById('items-count').textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.getElementById('subtotal-amount').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax-amount').textContent = '$' + taxAmount.toFixed(2);
    document.getElementById('grand-total').textContent = '$' + grandTotal.toFixed(2);
    document.getElementById('cart-total-preview').textContent = '$' + grandTotal.toFixed(2);
    document.getElementById('charge-amount').textContent = grandTotal.toFixed(2);
}

function clearCart() {
    if (cart.length > 0 && !confirm('Are you sure you want to clear the cart?')) return;
    cart = [];
    renderCart();
    updateTotals();
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
    alert('Processing payment for $' + document.getElementById('charge-amount').textContent);
    // After successful payment:
    // clearCart();
}

function saveAsDraft() {
    if (cart.length === 0) {
        alert('Cart is empty');
        return;
    }
    alert('Draft saved successfully');
}

// Search functionality
document.getElementById('search-products').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const name = product.querySelector('h3').textContent.toLowerCase();
        if (name.includes(searchTerm)) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateTotals();
});
</script>

@endsection