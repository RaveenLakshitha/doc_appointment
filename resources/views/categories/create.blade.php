{{-- resources/views/categories/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add Category')

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" mb-8">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                <a href="{{ route('categories.index') }}"
                    class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Categories</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white">Add Category</span>
            </div>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Add New Category</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a new inventory category</p>
        </div>

        <form method="POST" action="{{ route('categories.store') }}" class="space-y-8">
            @csrf

            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <!-- Mobile Tab Selector (Visible only on mobile) -->
                    <div class="sm:hidden p-4 bg-white dark:bg-gray-800">
                        <label for="mobile-tab-select" class="sr-only">Select a tab</label>
                        <select id="mobile-tab-select" onchange="switchTab(this.value)"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gray-900">
                            <option value="basic">Basic Information</option>
                        </select>
                    </div>

                    <!-- Desktop/Tablet Tab Navigation (Hidden on mobile) -->
                    <nav class="hidden sm:flex overflow-x-auto no-scrollbar "
                        aria-label="Tabs">
                        <button type="button" onclick="switchTab('basic')" id="tab-basic"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-gray-400 bg-gray-50 dark:bg-gray-700/50">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <span class="hidden sm:inline">Basic Information</span>
                                <span class="sm:hidden">Basic</span>
                            </div>
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <div id="content-basic" class="tab-content">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category Name
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow"
                                    placeholder="e.g. Surgical Instruments">
                                @error('name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Parent
                                    Category</label>
                                <select name="parent_id"
                                    class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow">
                                    <option value="">None (Top Level)</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                </p> @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                                <textarea name="description" rows="4"
                                    class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow resize-none"
                                    placeholder="Brief description of this category...">{{ old('description') }}</textarea>
                                @error('description') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
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
                    Create Category
                </button>
                <a href="{{ route('categories.index') }}"
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
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('text-gray-900', 'dark:text-white', 'border-gray-900', 'dark:border-gray-400', 'bg-gray-50', 'dark:bg-gray-700/50');
                button.classList.add('text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'hover:bg-gray-50', 'dark:hover:bg-gray-700/30');
            });
            document.getElementById('content-' + tabName).classList.remove('hidden');
            const activeButton = document.getElementById('tab-' + tabName);
            if (activeButton) {
                activeButton.classList.remove('text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'hover:bg-gray-50', 'dark:hover:bg-gray-700/30');
                activeButton.classList.add('text-gray-900', 'dark:text-white', 'border-b-2', 'border-gray-900', 'dark:border-gray-400', 'bg-gray-50', 'dark:bg-gray-700/50');

                // Update mobile select if present
                const mobileSelect = document.getElementById('mobile-tab-select');
                if (mobileSelect) mobileSelect.value = tabName;

                // Scroll the tab into view on mobile without shifting the entire page
                const nav = activeButton.closest('nav');
                if (nav && nav.classList.contains('flex')) {
                    const navRect = nav.getBoundingClientRect();
                    const btnRect = activeButton.getBoundingClientRect();
                    const offset = (btnRect.left - navRect.left) - (navRect.width / 2) + (btnRect.width / 2);
                    nav.scrollBy({ left: offset, behavior: 'smooth' });
                }
            }
        }
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