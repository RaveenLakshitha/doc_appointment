@extends('layouts.app')
@section('title', 'Add Inventory Item')

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">

        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('inventory.index') }}"
                class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Inventory Items</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 dark:text-white">Add Item</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Add New Inventory Item</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a new inventory item record</p>
    </div>

    <form method="POST" action="{{ route('inventory.store') }}" class="space-y-8" enctype="multipart/form-data">
        @csrf

        <div
            class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Mobile Tab Selector (Visible only on mobile) -->
            <div class="sm:hidden border-b border-gray-200 dark:border-gray-700 p-4">
                <label for="mobile-tab-select" class="sr-only">Select a tab</label>
                <select id="mobile-tab-select" onchange="switchTab(this.value)"
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-gray-900 dark:focus:ring-gray-500">
                    <option value="basic">Basic Information</option>
                    <option value="stock">Stock & Pricing</option>
                    <option value="supplier">Supplier Details</option>
                    <option value="medicine">Medicine & Images</option>
                    <option value="advanced">Advanced Settings</option>
                </select>
            </div>

            <!-- Desktop/Tablet Tab Navigation (Visible on SM and up, Scrollable on larger phones) -->
            <div
                class="hidden sm:block border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 relative ">
                <nav class="flex overflow-x-auto whitespace-nowrap snap-x snap-mandatory scroll-smooth no-scrollbar"
                    style="-webkit-overflow-scrolling: touch; scroll-behavior: smooth;" aria-label="Tabs">
                    <button type="button" onclick="switchTab('basic')" id="tab-basic"
                        class="tab-button flex-shrink-0 min-w-max px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-gray-400 bg-white dark:bg-gray-800 snap-start">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <span class="hidden sm:inline">Basic Information</span>
                            <span class="sm:hidden">Basic</span>
                        </div>
                    </button>
                    <button type="button" onclick="switchTab('stock')" id="tab-stock"
                        class="tab-button flex-shrink-0 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors snap-start">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span class="hidden sm:inline">Stock & Pricing</span>
                            <span class="sm:hidden">Stock</span>
                        </div>
                    </button>
                    <button type="button" onclick="switchTab('supplier')" id="tab-supplier"
                        class="tab-button flex-shrink-0 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors snap-start">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h-4m-4 0H5m14 0h-4m-4 0H5" />
                            </svg>
                            <span class="hidden sm:inline">Supplier Details</span>
                            <span class="sm:hidden">Supplier</span>
                        </div>
                    </button>
                    <button type="button" onclick="switchTab('medicine')" id="tab-medicine"
                        class="tab-button flex-shrink-0 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors snap-start">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            <span class="hidden sm:inline">Medicine & Images</span>
                            <span class="sm:hidden">Medicine</span>
                        </div>
                    </button>
                    <button type="button" onclick="switchTab('advanced')" id="tab-advanced"
                        class="tab-button flex-shrink-0 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors snap-start">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="hidden sm:inline">Advanced Settings</span>
                            <span class="sm:hidden">Advanced</span>
                        </div>
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <div id="content-basic" class="tab-content">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Item Name
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="e.g. Band-Aid Flexible Fabric">
                                @error('name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                </p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SKU <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="sku" value="{{ old('sku') }}" required
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="BAND-FAB-100">
                                @error('sku') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category
                                <span class="text-red-500">*</span></label>
                            <select name="category_id" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                <option value="">Select category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->full_path }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p> @enderror
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                placeholder="Brief description of the item...">{{ old('description') }}</textarea>
                            @error('description') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit of
                                    Measure <span class="text-red-500">*</span></label>
                                <select name="unit_of_measure" required
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                    <option value="">Select unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->name }}" {{ old('unit_of_measure') == $unit->name ? 'selected' : '' }}>{{ $unit->display_name }}</option>
                                    @endforeach
                                </select>
                                @error('unit_of_measure') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Units per
                                    Package <span class="text-red-500">*</span></label>
                                <input type="number" name="unit_quantity" value="{{ old('unit_quantity', 1) }}" required
                                    min="1"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="100">
                                @error('unit_quantity') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Manufacturer</label>
                                <input type="text" name="manufacturer" value="{{ old('manufacturer') }}"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="Johnson & Johnson">
                                @error('manufacturer') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Brand</label>
                                <input type="text" name="brand" value="{{ old('brand') }}"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="Band-Aid">
                                @error('brand') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                </p> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Model/Version</label>
                                <input type="text" name="model_version" value="{{ old('model_version') }}"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="2024 Edition">
                                @error('model_version') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Storage
                                Location</label>
                            <input type="text" name="storage_location" value="{{ old('storage_location') }}"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                placeholder="Warehouse A, Shelf 3">
                            @error('storage_location') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p> @enderror
                        </div>
                    </div>
                </div>

                <div id="content-stock" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current
                                    Stock <span class="text-red-500">*</span></label>
                                <input type="number" name="current_stock" value="{{ old('current_stock', 0) }}" required
                                    min="0"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="45">
                                @error('current_stock') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum
                                    Stock Level</label>
                                <input type="number" name="minimum_stock_level" value="{{ old('minimum_stock_level', 10) }}"
                                    min="0"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="10">
                                @error('minimum_stock_level') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit Cost
                                    ({{ $currency_code }}) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" name="unit_cost" value="{{ old('unit_cost') }}" required
                                    min="0"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="850.00">
                                @error('unit_cost') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit
                                    Price ({{ $currency_code }}) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" name="unit_price" value="{{ old('unit_price') }}" required
                                    min="0"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="1200.00">
                                @error('unit_price') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div id="content-supplier" class="tab-content hidden">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Primary
                                Supplier</label>
                            <select name="primary_supplier_id"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                <option value="">None</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('primary_supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('primary_supplier_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier
                                    Item Code</label>
                                <input type="text" name="supplier_item_code" value="{{ old('supplier_item_code') }}"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="MED-BAND100">
                                @error('supplier_item_code') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier
                                    Price ({{ $currency_code }})</label>
                                <input type="number" step="0.01" name="supplier_price" value="{{ old('supplier_price') }}"
                                    min="0"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="820.00">
                                @error('supplier_price') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lead Time
                                    (Days)</label>
                                <input type="number" name="lead_time_days" value="{{ old('lead_time_days') }}" min="0"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="3">
                                @error('lead_time_days') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum
                                    Order Qty</label>
                                <input type="number" name="minimum_order_quantity"
                                    value="{{ old('minimum_order_quantity') }}" min="0"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="50">
                                @error('minimum_order_quantity') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div id="content-medicine" class="tab-content hidden">
                    <div class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Generic
                                    Name</label>
                                <input type="text" name="generic_name" value="{{ old('generic_name') }}"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="Paracetamol / Acetaminophen">
                                @error('generic_name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Medicine
                                    Type</label>
                                <select name="medicine_type"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                    <option value="">Select type</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Capsule">Capsule</option>
                                    <option value="Syrup">Syrup / Suspension</option>
                                    <option value="Injection">Injection / Ampoule</option>
                                    <option value="Cream">Cream / Ointment</option>
                                    <option value="Other">Other</option>
                                </select>
                                @error('medicine_type') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dosage /
                                    Strength</label>
                                <input type="text" name="dosage" value="{{ old('dosage') }}"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="500 mg, 250 mg/5 ml">
                                @error('dosage') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                </p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tax Rate
                                    (%)</label>
                                <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate') }}" min="0"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="5.00">
                                @error('tax_rate') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Side
                                Effects</label>
                            <textarea name="side_effects" rows="3"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                placeholder="Nausea, dizziness, rash...">{{ old('side_effects') }}</textarea>
                            @error('side_effects') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Precautions &
                                Warnings</label>
                            <textarea name="precautions_warnings" rows="3"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                placeholder="Not for patients with liver disease...">{{ old('precautions_warnings') }}</textarea>
                            @error('precautions_warnings') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Storage
                                Conditions (select all that apply)</label>
                            <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @php
                                    $conditions = ['Room temperature', 'Cool place', 'Refrigerate (2-8°C)', 'Protect from light', 'Protect from moisture', 'Do not freeze'];
                                @endphp
                                @foreach($conditions as $condition)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="storage_conditions[]" value="{{ $condition }}"
                                            class="h-4 w-4 text-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $condition }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('storage_conditions.*') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" checked
                                    class="h-4 w-4 text-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active / Available</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="expiry_tracking" id="expiry_tracking" value="1" {{ old('expiry_tracking') ? 'checked' : '' }}
                                    class="h-4 w-4 text-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Track Expiry</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="requires_refrigeration" value="1" {{ old('requires_refrigeration') ? 'checked' : '' }}
                                    class="h-4 w-4 text-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Requires
                                    Refrigeration</span>
                            </label>
                        </div>

                        <div id="initial-batch-section" class="border-t dark:border-gray-700 pt-6 mt-6 hidden">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Initial Batch / Opening
                                Stock</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Batch
                                        Number</label>
                                    <input type="text" name="batch_number" value="{{ old('batch_number') }}"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white"
                                        placeholder="LOT-2025-001">
                                    @error('batch_number') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry
                                        Date <span class="text-red-500">*</span></label>
                                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white">
                                    @error('expiry_date') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Initial
                                        Quantity <span class="text-red-500">*</span></label>
                                    <input type="number" name="initial_quantity" value="{{ old('initial_quantity') }}"
                                        min="0"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white"
                                        placeholder="100">
                                    @error('initial_quantity') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t dark:border-gray-700">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Medicine
                                    Image</label>
                                <div
                                    class="flex justify-center rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 px-6 pt-5 pb-6">
                                    <div class="space-y-1 text-center">
                                        <div id="medicine_preview_container" class="hidden mb-4">
                                            <img id="medicine_preview" src="#" alt="Preview"
                                                class="mx-auto h-32 w-auto object-contain rounded-md border border-gray-200 dark:border-gray-700">
                                        </div>
                                        <svg id="medicine_icon" class="mx-auto h-12 w-12 text-gray-400"
                                            stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                            <label
                                                class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                                <span>Upload medicine image</span>
                                                <input type="file" name="medicine_image" class="sr-only" accept="image/*"
                                                    onchange="previewImage(this, 'medicine_preview')">
                                            </label>
                                        </div>
                                        <p id="medicine_filename" class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            PNG, JPG up to 4MB</p>
                                    </div>
                                </div>
                                @error('medicine_image') <p class="mt-2 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Package /
                                    Box Image</label>
                                <div
                                    class="flex justify-center rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 px-6 pt-5 pb-6">
                                    <div class="space-y-1 text-center">
                                        <div id="package_preview_container" class="hidden mb-4">
                                            <img id="package_preview" src="#" alt="Preview"
                                                class="mx-auto h-32 w-auto object-contain rounded-md border border-gray-200 dark:border-gray-700">
                                        </div>
                                        <svg id="package_icon" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                            fill="none" viewBox="0 0 48 48">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                            <label
                                                class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                                <span>Upload package image</span>
                                                <input type="file" name="package_image" class="sr-only" accept="image/*"
                                                    onchange="previewImage(this, 'package_preview')">
                                            </label>
                                        </div>
                                        <p id="package_filename" class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            PNG, JPG up to 4MB</p>
                                    </div>
                                </div>
                                @error('package_image') <p class="mt-2 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div id="content-advanced" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="controlled_substance" value="1" {{ old('controlled_substance') ? 'checked' : '' }}
                                    class="h-4 w-4 text-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600 focus:ring-gray-900 dark:focus:ring-gray-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Controlled Substance</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="hazardous_material" value="1" {{ old('hazardous_material') ? 'checked' : '' }}
                                    class="h-4 w-4 text-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600 focus:ring-gray-900 dark:focus:ring-gray-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Hazardous Material</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="sterile" value="1" {{ old('sterile') ? 'checked' : '' }}
                                    class="h-4 w-4 text-gray-900 border-gray-300 rounded dark:bg-transparent dark:border-gray-600 focus:ring-gray-900 dark:focus:ring-gray-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Sterile Item</span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Additional
                                Information</label>
                            <textarea name="additional_info" rows="4"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                placeholder="Safety notes, usage instructions, etc.">{{ old('additional_info') }}</textarea>
                            @error('additional_info') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button type="submit"
                class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Item
            </button>
            <a href="{{ route('inventory.index') }}"
                class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 dark:bg-transparent dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
            </a>
        </div>
    </form>
    </div>

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.querySelectorAll('.tab-button').forEach(b => {
                b.classList.remove('text-gray-900', 'dark:text-white', 'border-b-2', 'border-gray-900', 'dark:border-gray-400', 'bg-white', 'dark:bg-gray-800');
                b.classList.add('text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'hover:bg-gray-50', 'dark:hover:bg-gray-700/30');
            });

            // Update mobile select if present
            const mobileSelect = document.getElementById('mobile-tab-select');
            if (mobileSelect) mobileSelect.value = tabName;

            const content = document.getElementById('content-' + tabName);
            if (content) content.classList.remove('hidden');

            const btn = document.getElementById('tab-' + tabName);
            if (btn) {
                btn.classList.remove('text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'hover:bg-gray-50', 'dark:hover:bg-gray-700/30');
                btn.classList.add('text-gray-900', 'dark:text-white', 'border-b-2', 'border-gray-900', 'dark:border-gray-400', 'bg-white', 'dark:bg-gray-800');

                // Scroll the tab into view on mobile without shifting the entire page
                const nav = btn.closest('nav');
                if (nav) {
                    const navRect = nav.getBoundingClientRect();
                    const btnRect = btn.getBoundingClientRect();
                    const offset = (btnRect.left - navRect.left) - (navRect.width / 2) + (btnRect.width / 2);
                    nav.scrollBy({ left: offset, behavior: 'smooth' });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const expiryCheckbox = document.getElementById('expiry_tracking');
            const batchSection = document.getElementById('initial-batch-section');

            const toggleBatch = () => {
                if (expiryCheckbox.checked) {
                    batchSection.classList.remove('hidden');
                    document.querySelector('[name="expiry_date"]').setAttribute('required', '');
                    document.querySelector('[name="initial_quantity"]').setAttribute('required', '');
                } else {
                    batchSection.classList.add('hidden');
                    document.querySelector('[name="expiry_date"]').removeAttribute('required');
                    document.querySelector('[name="initial_quantity"]').removeAttribute('required');
                }
            };

            expiryCheckbox.addEventListener('change', toggleBatch);
            toggleBatch();

            // Check for validation errors and switch to the tab with errors
            const firstError = document.querySelector('.text-red-600');
            if (firstError) {
                const tabContent = firstError.closest('.tab-content');
                if (tabContent) {
                    const tabId = tabContent.id.replace('content-', '');
                    switchTab(tabId);
                    return;
                }
            }

            switchTab('basic');
        });

        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById(previewId + '_container').classList.remove('hidden');
                    document.getElementById(previewId).src = e.target.result;

                    // Hide the generic icon if present
                    var iconId = previewId.replace('_preview', '_icon');
                    var icon = document.getElementById(iconId);
                    if (icon) icon.classList.add('hidden');

                    // Update filename text
                    var filenameId = previewId.replace('_preview', '_filename');
                    var text = document.getElementById(filenameId);
                    if (text) text.textContent = input.files[0].name;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    @push('styles')
        <style>
            .tab-button {
                z-index: 10;
            }
        </style>
    @endpush
@endsection