@extends('layouts.app')

@section('title', 'Add New Medicine')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 pb-12 sm:py-6">
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('medicines.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                Medicines
            </a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white font-medium">Add New Medicine</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Medicine</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Fill in the details below to add a new medicine to the system. Initial stock/batch is optional — you can add it later.</p>
    </div>

    <form method="POST" action="{{ route('medicines.store') }}" class="space-y-10" enctype="multipart/form-data">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <!-- Mobile Tab Selector (Visible only on mobile) -->
                <div class="sm:hidden p-4 bg-white dark:bg-gray-800">
                    <label for="mobile-tab-select" class="sr-only">Select a tab</label>
                    <select id="mobile-tab-select" onchange="switchTab(this.value)"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500">
                        <option value="basic">Basic Information</option>
                        <option value="details">Medicine Details</option>
                        <option value="stock">Stock & Batch</option>
                        <option value="supplier">Supplier & Pricing</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>

                <!-- Desktop/Tablet Tab Navigation (Hidden on mobile) -->
                <nav class="hidden sm:flex overflow-x-auto no-scrollbar " aria-label="Tabs">
                    <button type="button" onclick="switchTab('basic')" id="tab-basic"
                            class="tab-button flex-1 min-w-[120px] px-5 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 dark:hover:border-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Basic Information
                    </button>
                    <button type="button" onclick="switchTab('details')" id="tab-details"
                            class="tab-button flex-1 min-w-[120px] px-5 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 dark:hover:border-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Medicine Details
                    </button>
                    <button type="button" onclick="switchTab('stock')" id="tab-stock"
                            class="tab-button flex-1 min-w-[120px] px-5 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 dark:hover:border-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Stock & Batch
                    </button>
                    <button type="button" onclick="switchTab('supplier')" id="tab-supplier"
                            class="tab-button flex-1 min-w-[120px] px-5 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 dark:hover:border-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Supplier & Pricing
                    </button>
                    <button type="button" onclick="switchTab('advanced')" id="tab-advanced"
                            class="tab-button flex-1 min-w-[120px] px-5 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 dark:hover:border-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Advanced
                    </button>
                </nav>
            </div>

            <div class="p-6 md:p-8">
                <!-- Basic Information Tab -->
                <div id="content-basic" class="tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Trade/Brand Name <span class="text-red-600">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Generic Name <span class="text-red-600">*</span></label>
                            <input type="text" name="generic_name" value="{{ old('generic_name') }}" required
                                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('generic_name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Category <span class="text-red-600">*</span></label>
                        <select name="category_id" required class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">— Select Category —</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                        <textarea name="description" rows="3" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Medicine Details Tab -->
                <div id="content-details" class="tab-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Medicine Type <span class="text-red-600">*</span></label>
                            <select name="medicine_type" required class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">— Select Type —</option>
                                <option value="Tablet" {{ old('medicine_type') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                                <option value="Capsule" {{ old('medicine_type') == 'Capsule' ? 'selected' : '' }}>Capsule</option>
                                <option value="Syrup" {{ old('medicine_type') == 'Syrup' ? 'selected' : '' }}>Syrup</option>
                                <option value="Injection" {{ old('medicine_type') == 'Injection' ? 'selected' : '' }}>Injection</option>
                                <option value="Cream/Ointment" {{ old('medicine_type') == 'Cream/Ointment' ? 'selected' : '' }}>Cream/Ointment</option>
                                <option value="Drops" {{ old('medicine_type') == 'Drops' ? 'selected' : '' }}>Drops</option>
                                <option value="Other" {{ old('medicine_type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('medicine_type') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Dosage / Strength</label>
                            <input type="text" name="dosage" value="{{ old('dosage') }}"
                                   placeholder="e.g. 500 mg, 5 ml, 10 mg/ml" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('dosage') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Side Effects</label>
                        <textarea name="side_effects" rows="3" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('side_effects') }}</textarea>
                        @error('side_effects') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Precautions & Warnings</label>
                        <textarea name="precautions_warnings" rows="3" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('precautions_warnings') }}</textarea>
                        @error('precautions_warnings') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Stock & Batch Tab (Optional) -->
                <div id="content-stock" class="tab-content hidden">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 italic">
                        Optional — You can add batches/stock later via Purchases or Receive Goods.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Initial Quantity</label>
                            <input type="number" name="initial_quantity" min="0" value="{{ old('initial_quantity') }}"
                                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('initial_quantity') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Batch Number</label>
                            <input type="text" name="batch_number" value="{{ old('batch_number') }}"
                                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('batch_number') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Expiry Date</label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('expiry_date') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Manufacturing Date (optional)</label>
                        <input type="date" name="manufacturing_date" value="{{ old('manufacturing_date') }}"
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('manufacturing_date') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Supplier & Pricing Tab -->
                <div id="content-supplier" class="tab-content hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Primary Supplier</label>
                        <select name="primary_supplier_id" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">— Select Supplier —</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('primary_supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('primary_supplier_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Unit Cost ({{ $currency_code }}) <span class="text-red-600">*</span></label>
                            <input type="number" step="0.01" name="unit_cost" value="{{ old('unit_cost') }}" min="0" required
                                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('unit_cost') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Unit Price ({{ $currency_code }}) <span class="text-red-600">*</span></label>
                            <input type="number" step="0.01" name="unit_price" value="{{ old('unit_price') }}" min="0" required
                                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('unit_price') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tax Rate (%)</label>
                        <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', 0) }}" min="0" max="100"
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('tax_rate') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Advanced Tab -->
                <div id="content-advanced" class="tab-content hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Storage Conditions</label>
                            <select name="storage_conditions[]" multiple class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-32">
                                <option value="Room Temperature" {{ in_array('Room Temperature', old('storage_conditions', [])) ? 'selected' : '' }}>Room Temperature</option>
                                <option value="Refrigerated" {{ in_array('Refrigerated', old('storage_conditions', [])) ? 'selected' : '' }}>Refrigerated</option>
                                <option value="Frozen" {{ in_array('Frozen', old('storage_conditions', [])) ? 'selected' : '' }}>Frozen</option>
                                <option value="Protect from Light" {{ in_array('Protect from Light', old('storage_conditions', [])) ? 'selected' : '' }}>Protect from Light</option>
                            </select>
                            @error('storage_conditions.*') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-4">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Active / Available</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Medicine Image</label>
                            <input type="file" name="medicine_image" accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-200">
                            @error('medicine_image') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Package Image</label>
                            <input type="file" name="package_image" accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-200">
                            @error('package_image') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-8">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Manufacturer</label>
                        <input type="text" name="manufacturer" value="{{ old('manufacturer') }}"
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('manufacturer') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6">
            <a href="{{ route('medicines.index') }}"
               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                Cancel
            </a>

            <button type="submit"
                    class="inline-flex items-center justify-center px-8 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Medicine
            </button>
        </div>
    </form>
</div>

<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-indigo-600', 'text-indigo-600', 'dark:border-indigo-500', 'dark:text-indigo-400');
        btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-300');
    });

    // Update mobile select if present
    const mobileSelect = document.getElementById('mobile-tab-select');
    if (mobileSelect) mobileSelect.value = tabName;

    document.getElementById(`content-${tabName}`).classList.remove('hidden');
    const selectedBtn = document.getElementById(`tab-${tabName}`);
    if (selectedBtn) {
        selectedBtn.classList.add('border-indigo-600', 'text-indigo-600', 'dark:border-indigo-500', 'dark:text-indigo-400');
        selectedBtn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');

        // Scroll the tab into view on mobile without shifting the entire page
        const nav = selectedBtn.closest('nav');
        if (nav && nav.classList.contains('flex')) {
            const navRect = nav.getBoundingClientRect();
            const btnRect = selectedBtn.getBoundingClientRect();
            const offset = (btnRect.left - navRect.left) - (navRect.width / 2) + (btnRect.width / 2);
            nav.scrollBy({ left: offset, behavior: 'smooth' });
        }
    }
}

switchTab('basic');
</script>

<style>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection