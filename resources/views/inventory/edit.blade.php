@extends('layouts.app')

@section('title', 'Edit: ' . Str::limit($inventoryitem->name, 30))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Edit Inventory Item: {{ $inventoryitem->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                SKU: {{ $inventoryitem->sku ?? '—' }}
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('inventory.update', $inventoryitem) }}" class="p-6 space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $inventoryitem->name) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300">SKU</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku', $inventoryitem->sku) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                    @error('sku') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Category & Supplier -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                    <select name="category_id" id="category_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        <option value="">— Select Category —</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $inventoryitem->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->full_path ?? $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="primary_supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Primary Supplier</label>
                    <select name="primary_supplier_id" id="primary_supplier_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                        <option value="">— None —</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('primary_supplier_id', $inventoryitem->primary_supplier_id) == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('primary_supplier_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea name="description" id="description" rows="4"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">{{ old('description', $inventoryitem->description) }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Unit & Quantity -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="unit_of_measure" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit of Measure</label>
                    <input type="text" name="unit_of_measure" id="unit_of_measure" value="{{ old('unit_of_measure', $inventoryitem->unit_of_measure) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>

                <div>
                    <label for="unit_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Quantity</label>
                    <input type="number" name="unit_quantity" id="unit_quantity" min="1" value="{{ old('unit_quantity', $inventoryitem->unit_quantity ?? 1) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>

                <div>
                    <label for="storage_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Storage Location</label>
                    <input type="text" name="storage_location" id="storage_location" value="{{ old('storage_location', $inventoryitem->storage_location) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>
            </div>

            <!-- Manufacturer & Brand -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="manufacturer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Manufacturer</label>
                    <input type="text" name="manufacturer" id="manufacturer" value="{{ old('manufacturer', $inventoryitem->manufacturer) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>

                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Brand</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand', $inventoryitem->brand) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>
            </div>

            <!-- Stock Levels -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-t pt-6">
                <div>
                    <label for="minimum_stock_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Stock Level</label>
                    <input type="number" name="minimum_stock_level" id="minimum_stock_level" min="0" value="{{ old('minimum_stock_level', $inventoryitem->minimum_stock_level ?? 0) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>

                <div>
                    <label for="reorder_point" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reorder Point</label>
                    <input type="number" name="reorder_point" id="reorder_point" min="0" value="{{ old('reorder_point', $inventoryitem->reorder_point ?? 0) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>

                <div>
                    <label for="reorder_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reorder Quantity</label>
                    <input type="number" name="reorder_quantity" id="reorder_quantity" min="1" value="{{ old('reorder_quantity', $inventoryitem->reorder_quantity ?? 1) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>
            </div>

            <!-- Pricing -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-6">
                <div>
                    <label for="unit_cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Cost (Rs.)</label>
                    <input type="number" name="unit_cost" id="unit_cost" step="0.01" min="0" value="{{ old('unit_cost', $inventoryitem->unit_cost ?? 0) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>

                <div>
                    <label for="unit_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price (Rs.)</label>
                    <input type="number" name="unit_price" id="unit_price" step="0.01" min="0" value="{{ old('unit_price', $inventoryitem->unit_price ?? 0) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t">
                <a href="{{ route('inventory.show', $inventoryitem) }}"
                   class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-400 transition">
                    Cancel
                </a>

                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition shadow-md">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection