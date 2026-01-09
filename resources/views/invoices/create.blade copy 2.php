@extends('layouts.app')
@section('title', __('file.pos_system'))

@section('content')
<div class="w-full h-screen flex flex-col bg-gray-50 dark:bg-gray-900">
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('invoices.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 dark:text-gray-400" id="current-time"></span>
                <button type="button" onclick="clearAll()" class="px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                    Clear All
                </button>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('invoices.store') }}" class="flex-1 flex overflow-hidden">
        @csrf
        <input type="hidden" name="invoice_type" value="standard">
        <input type="hidden" name="invoice_date" value="{{ date('Y-m-d') }}">
        <input type="hidden" name="due_date" value="{{ date('Y-m-d') }}">
        <input type="hidden" name="invoice_number" value="POS-{{ date('YmdHis') }}">

        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <div class="flex-1 relative">
                        <input type="text" id="product-search" placeholder="Search services or medicines..." 
                               class="w-full px-4 py-2.5 pl-10 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-900 dark:text-white"
                               oninput="filterProducts()">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <select id="type-filter" onchange="filterProducts()" 
                            class="px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-900 dark:text-white">
                        <option value="">All Items</option>
                        <option value="service">Services</option>
                        <option value="inventory">Medicines / Products</option>
                    </select>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900" style="max-height: calc(100vh - 180px);">
                <div id="products-grid" 
                     class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                    
                    @foreach($services as $service)
                    <button type="button" 
                            class="product-card p-4 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-900 dark:hover:border-gray-500 transition-all text-left group"
                            data-item-type="service"
                            data-item-id="{{ $service->id }}"
                            data-item-name="{{ $service->name }}"
                            data-item-price="{{ $service->price }}"
                            onclick="addToCart('service', {{ $service->id }}, '{{ addslashes($service->name) }}', {{ $service->price }})">
                        <div class="flex flex-col h-full">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded-full">Service</span>
                                </div>
                                <h3 class="font-semibold text-sm text-gray-900 dark:text-white mb-1 line-clamp-2">
                                    {{ $service->name }}
                                </h3>
                            </div>
                            <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-100 dark:border-gray-700">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                    ${{ number_format($service->price, 2) }}
                                </span>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                        </div>
                    </button>
                    @endforeach

                    @if(isset($inventoryItems))
                        @foreach($inventoryItems as $item)
                        <button type="button" 
                                class="product-card p-4 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-900 dark:hover:border-gray-500 transition-all text-left group {{ $item->current_stock <= 0 ? 'opacity-60' : '' }}"
                                data-item-type="inventory"
                                data-item-id="{{ $item->id }}"
                                data-item-name="{{ $item->name }} @if($item->generic_name) ({{ $item->generic_name }}) @endif"
                                data-item-price="{{ $item->unit_price }}"
                                data-stock="{{ $item->current_stock }}"
                                onclick="{{ $item->current_stock > 0 ? "addToCart('inventory', {$item->id}, '" . addslashes($item->name . ($item->generic_name ? ' (' . $item->generic_name . ')' : '')) . "', {$item->unit_price})" : "alert('Out of stock!')" }}"
                                {{ $item->current_stock <= 0 ? 'disabled' : '' }}>
                            <div class="flex flex-col h-full">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded-full">Medicine</span>
                                        @if($item->current_stock < $item->reorder_point)
                                            <span class="text-xs px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 rounded-full">Low Stock</span>
                                        @endif
                                    </div>
                                    <h3 class="font-semibold text-sm text-gray-900 dark:text-white mb-1 line-clamp-2">
                                        {{ $item->name }}
                                        @if($item->generic_name)<br><span class="text-xs text-gray-500">({{ $item->generic_name }})</span>@endif
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Stock: <strong>{{ $item->current_stock }}</strong>
                                    </p>
                                </div>
                                <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($item->unit_price, 2) }}
                                    </span>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        <div class="w-full md:w-96 lg:w-[450px] bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex flex-col">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Patient <span class="text-red-500">*</span>
                </label>
                <select name="patient_id" id="patient-select" required 
                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-900 dark:text-white">
                    <option value="">Select Patient</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">
                            {{ $patient->first_name }} {{ $patient->last_name }} - {{ $patient->patient_id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 overflow-y-auto p-4" style="max-height: calc(100vh - 320px);">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Cart Items</h3>
                <div id="cart-items" class="space-y-2">
                    <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <p class="text-sm">Cart is empty</p>
                        <p class="text-xs mt-1">Add items to get started</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 p-4 space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs text-gray-600 dark:text-gray-400">Tax %</label>
                        <input type="number" name="tax_percentage" id="tax-input" value="8" min="0" max="100" step="0.01" 
                               class="w-20 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-900 dark:text-white text-right"
                               oninput="updateTotals()">
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="text-xs text-gray-600 dark:text-gray-400">Discount $</label>
                        <input type="number" name="discount" id="discount-input" value="0.00" min="0" step="0.01" 
                               class="w-20 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 dark:bg-gray-900 dark:text-white text-right"
                               oninput="updateTotals()">
                    </div>
                </div>

                <div class="space-y-2 py-3 border-y border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                        <span id="subtotal" class="font-medium text-gray-900 dark:text-white">$0.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Tax:</span>
                        <span id="tax-amount" class="font-medium text-gray-900 dark:text-white">$0.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                        <span id="discount-amount" class="font-medium text-gray-900 dark:text-white">$0.00</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-gray-900 dark:text-white">Total:</span>
                        <span id="grand-total" class="text-gray-900 dark:text-white">$0.00</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <button type="submit" id="checkout-btn"
                            class="w-full px-6 py-3.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-semibold rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Complete Sale
                    </button>
                    <button type="button" onclick="holdOrder()"
                            class="w-full px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Hold Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let cart = [];
let itemIndex = 0;

function updateTime() {
    const now = new Date();
    document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
    });
}
setInterval(updateTime, 1000);
updateTime();

function addToCart(type, id, name, price) {
    const key = `${type}-${id}`;
    const existingItem = cart.find(item => item.key === key);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            type: type,
            id: id,
            name: name,
            price: parseFloat(price),
            quantity: 1,
            key: key,
            index: itemIndex++
        });
    }
    
    renderCart();
    updateTotals();
}

function removeFromCart(index) {
    cart = cart.filter(item => item.index !== index);
    renderCart();
    updateTotals();
}

function updateQuantity(index, delta) {
    const item = cart.find(item => item.index === index);
    if (item) {
        item.quantity = Math.max(1, item.quantity + delta);
        renderCart();
        updateTotals();
    }
}

function renderCart() {
    const container = document.getElementById('cart-items');
    
    if (cart.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-sm">Cart is empty</p>
                <p class="text-xs mt-1">Add items to get started</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = cart.map(item => `
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
            <div class="flex items-start justify-between mb-2">
                <div class="flex-1 min-w-0 pr-2">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">${item.name}</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        ${item.type === 'service' ? 'Service' : 'Medicine'} • $${item.price.toFixed(2)} each
                    </p>
                </div>
                <button type="button" onclick="removeFromCart(${item.index})" 
                        class="p-1 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="updateQuantity(${item.index}, -1)" 
                            class="p-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-3 h-3 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </button>
                    <span class="text-sm font-medium text-gray-900 dark:text-white w-8 text-center">${item.quantity}</span>
                    <button type="button" onclick="updateQuantity(${item.index}, 1)" 
                            class="p-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-3 h-3 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white">$${(item.price * item.quantity).toFixed(2)}</span>
            </div>

            <input type="hidden" name="items[${item.index}][type]" value="${item.type}">
            <input type="hidden" name="items[${item.index}][item_id]" value="${item.type}-${item.id}">
            <input type="hidden" name="items[${item.index}][quantity]" value="${item.quantity}">
            <input type="hidden" name="items[${item.index}][unit_price]" value="${item.price}">
        </div>
    `).join('');
}

function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const taxPercentage = parseFloat(document.getElementById('tax-input').value) || 0;
    const discount = parseFloat(document.getElementById('discount-input').value) || 0;
    
    const taxAmount = (subtotal * taxPercentage) / 100;
    const grandTotal = subtotal + taxAmount - discount;
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax-amount').textContent = '$' + taxAmount.toFixed(2);
    document.getElementById('discount-amount').textContent = '$' + discount.toFixed(2);
    document.getElementById('grand-total').textContent = '$' + grandTotal.toFixed(2);
    
    const checkoutBtn = document.getElementById('checkout-btn');
    const patientSelected = document.getElementById('patient-select').value;
    checkoutBtn.disabled = cart.length === 0 || !patientSelected;
}

function filterProducts() {
    const searchTerm = document.getElementById('product-search').value.toLowerCase();
    const typeFilter = document.getElementById('type-filter').value;
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const name = product.dataset.itemName.toLowerCase();
        const type = product.dataset.itemType;
        
        const matchesSearch = name.includes(searchTerm);
        const matchesType = !typeFilter || type === typeFilter;
        
        product.style.display = matchesSearch && matchesType ? 'block' : 'none';
    });
}

function clearAll() {
    if (cart.length > 0 && confirm('Are you sure you want to clear the cart?')) {
        cart = [];
        renderCart();
        updateTotals();
    }
}

function holdOrder() {
    if (cart.length > 0) {
        const orderData = {
            cart: cart,
            patient: document.getElementById('patient-select').value,
            timestamp: new Date().toISOString()
        };
        localStorage.setItem('held_pos_order_' + Date.now(), JSON.stringify(orderData));
        alert('Order held successfully!');
        clearAll();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateTotals();
    document.getElementById('patient-select').addEventListener('change', updateTotals);
});
</script>

@endsection