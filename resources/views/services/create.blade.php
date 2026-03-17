{{-- resources/views/services/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add Service')

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">

        <div class=" flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('services.index') }}"
                class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Services</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 dark:text-white">Add Service</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Add New Service</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a new medical service</p>
    </div>

    <form method="POST" action="{{ route('services.store') }}" class="space-y-8">
        @csrf

        <div
            class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <!-- Mobile Tab Selector (Visible only on mobile) -->
                <div class="sm:hidden p-4 bg-white dark:bg-gray-800">
                    <label for="mobile-tab-select" class="sr-only">Select a tab</label>
                    <select id="mobile-tab-select" onchange="switchTab(this.value)"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gray-900 dark:focus:ring-gray-500">
                        <option value="basic">Basic Information</option>
                        <option value="equipment">Equipment</option>
                        <option value="details">Details</option>
                    </select>
                </div>

                <!-- Desktop/Tablet Tab Navigation (Hidden on mobile) -->
                <nav class="hidden sm:flex overflow-x-auto no-scrollbar " aria-label="Tabs">
                    <button type="button" onclick="switchTab('basic')" id="tab-basic"
                        class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-gray-400 bg-gray-50 dark:bg-gray-700/50">
                        Basic Information
                    </button>
                    <button type="button" onclick="switchTab('equipment')" id="tab-equipment"
                        class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        Equipment
                    </button>
                    <button type="button" onclick="switchTab('details')" id="tab-details"
                        class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        Details
                    </button>
                </nav>
            </div>

            <div class="p-6 space-y-8">
                <!-- Basic Information -->
                <div id="content-basic" class="tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service Name
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Department
                                <span class="text-red-500">*</span></label>
                            <select name="department_id" required
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                <option value="">Select department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type <span
                                    class="text-red-500">*</span></label>
                            <select name="type" required
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                <option value="">Select type</option>
                                <option value="Diagnostic" {{ old('type') == 'Diagnostic' ? 'selected' : '' }}>Diagnostic
                                </option>
                                <option value="Therapeutic" {{ old('type') == 'Therapeutic' ? 'selected' : '' }}>
                                    Therapeutic</option>
                                <option value="Consultation" {{ old('type') == 'Consultation' ? 'selected' : '' }}>
                                    Consultation</option>
                                <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration
                                (minutes)</label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}"
                                min="5" step="5"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                            @error('duration_minutes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price ($)
                                <span class="text-red-500">*</span></label>
                            <input type="number" name="price" value="{{ old('price') }}" required min="0" step="0.01"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                            @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Equipment Assignment -->
                <div id="content-equipment" class="tab-content hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Assign Equipment
                        (Optional)</label>
                    <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                        @foreach($equipment as $eq)
                            <label class="flex items-center">
                                <input type="checkbox" name="equipment[]" value="{{ $eq->id }}"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $eq->name }}
                                    <span class="text-xs text-gray-500 ml-1">({{ $eq->status ?? 'N/A' }})</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('equipment') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Details -->
                <div id="content-details" class="tab-content hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea name="description" rows="4"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Patient
                            Preparation Instructions</label>
                        <textarea name="patient_preparation" rows="4"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none">{{ old('patient_preparation') }}</textarea>
                        @error('patient_preparation') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="requires_insurance" value="1" {{ old('requires_insurance') ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Requires Insurance Approval</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="requires_referral" value="1" {{ old('requires_referral') ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Requires Doctor Referral</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-4">
            <button type="submit"
                class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Service
            </button>
            <a href="{{ route('services.index') }}"
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
                b.classList.remove('text-gray-900', 'dark:text-white', 'border-b-2', 'border-gray-900', 'dark:border-gray-400', 'bg-gray-50', 'dark:bg-gray-700/50');
                b.classList.add('text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'hover:bg-gray-50', 'dark:hover:bg-gray-700/30');
            });

            // Update mobile select if present
            const mobileSelect = document.getElementById('mobile-tab-select');
            if (mobileSelect) mobileSelect.value = tabName;

            const content = document.getElementById('content-' + tabName);
            const button = document.getElementById('tab-' + tabName);

            if (content && button) {
                content.classList.remove('hidden');
                button.classList.add('text-gray-900', 'dark:text-white', 'border-b-2', 'border-gray-900', 'dark:border-gray-400', 'bg-gray-50', 'dark:bg-gray-700/50');
                button.classList.remove('text-gray-500', 'dark:text-gray-400');

                // Scroll the tab into view on mobile without shifting the entire page
                const nav = button.closest('nav');
                if (nav && nav.classList.contains('flex')) {
                    const navRect = nav.getBoundingClientRect();
                    const btnRect = button.getBoundingClientRect();
                    const offset = (btnRect.left - navRect.left) - (navRect.width / 2) + (btnRect.width / 2);
                    nav.scrollBy({ left: offset, behavior: 'smooth' });
                }
            }
        }

        // Open basic tab by default
        switchTab('basic');
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection