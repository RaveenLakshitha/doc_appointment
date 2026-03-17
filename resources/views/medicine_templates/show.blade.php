@extends('layouts.app')

@section('title', $medicineTemplate->name)

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" mb-8">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                <a href="{{ route('medicine-templates.index') }}"
                    class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    {{ __('file.medicine_templates') }}
                </a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white">{{ $medicineTemplate->name }}</span>
            </div>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $medicineTemplate->name }}</h1>
        </div>

        <div
            class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <!-- Mobile Tab Selector (Visible only on mobile) -->
                <div class="sm:hidden p-4 bg-white dark:bg-gray-800">
                    <label for="mobile-tab-select" class="sr-only">Select a tab</label>
                    <select id="mobile-tab-select" onchange="switchTab(this.value)"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500">
                        <option value="medications">{{ __('file.medications') }}</option>
                        <option value="details">{{ __('file.template_details') }}</option>
                    </select>
                </div>

                <!-- Desktop/Tablet Tab Navigation (Hidden on mobile) -->
                <nav class="hidden sm:flex overflow-x-auto no-scrollbar " aria-label="Tabs">
                    <button type="button" onclick="switchTab('medications')" id="tab-medications"
                        class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-gray-400 bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-8 0h6" />
                            </svg>
                            {{ __('file.medications') }} ({{ $medicineTemplate->medications->count() }})
                        </div>
                    </button>
                    <button type="button" onclick="switchTab('details')" id="tab-details"
                        class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ __('file.template_details') }}
                        </div>
                    </button>
                </nav>
            </div>

            <div class="p-6 space-y-8">
                <!-- Medications Tab -->
                <div id="content-medications" class="tab-content">
                    @if($medicineTemplate->medications->count() > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($medicineTemplate->medications as $medication)
                                <div
                                    class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 bg-gray-50 dark:bg-gray-800/50">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                        {{ $medication->inventoryItem ? $medication->inventoryItem->name : $medication->name }}
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span
                                                class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.dosage') }}:</span>
                                            <span
                                                class="ml-2 text-gray-900 dark:text-gray-200">{{ $medication->dosage ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span
                                                class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.route') }}:</span>
                                            <span
                                                class="ml-2 text-gray-900 dark:text-gray-200">{{ $medication->route ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span
                                                class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.frequency') }}:</span>
                                            <span
                                                class="ml-2 text-gray-900 dark:text-gray-200">{{ $medication->frequency ?? '—' }}</span>
                                        </div>
                                        @if($medication->instructions)
                                            <div class="sm:col-span-2 mt-3">
                                                <span
                                                    class="font-medium text-gray-600 dark:text-gray-400">{{ __('file.instructions') }}:</span>
                                                <p class="mt-1 text-gray-900 dark:text-gray-200 whitespace-pre-wrap">
                                                    {{ $medication->instructions }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            {{ __('file.no_medications_in_template') }}
                        </div>
                    @endif
                </div>

                <!-- Template Details Tab -->
                <div id="content-details" class="tab-content hidden">
                    <div class="space-y-6 max-w-3xl">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('file.template_name') }}</label>
                            <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">{{ $medicineTemplate->name }}
                            </p>
                        </div>

                        @if($medicineTemplate->category)
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('file.category') }}</label>
                                <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $medicineTemplate->category }}</p>
                            </div>
                        @endif

                        @if($medicineTemplate->description)
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('file.description') }}</label>
                                <p class="mt-1 text-gray-600 dark:text-gray-400 whitespace-pre-wrap">
                                    {{ $medicineTemplate->description }}
                                </p>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('file.total_medications') }}</label>
                                <p class="mt-1 text-lg text-gray-900 dark:text-white">
                                    {{ $medicineTemplate->medications->count() }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('file.created_at') }}</label>
                                <p class="mt-1 text-gray-600 dark:text-gray-400">
                                    {{ $medicineTemplate->created_at ? $medicineTemplate->created_at->format('M d, Y \a\t h:i A') : 'N/A' }}
                                </p>
                            </div>

                            @if($medicineTemplate->updated_at && $medicineTemplate->updated_at->notEqualTo($medicineTemplate->created_at))
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('file.last_updated') }}</label>
                                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                                        {{ $medicineTemplate->updated_at->format('M d, Y \a\t h:i A') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-col sm:flex-row justify-end gap-4">
            <a href="{{ route('medicine-templates.edit', $medicineTemplate) }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                {{ __('file.edit_template') }}
            </a>

            <a href="{{ route('medicine-templates.index') }}"
                class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                {{ __('file.back_to_list') }}
            </a>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.querySelectorAll('.tab-button').forEach(b => {
                b.classList.remove('text-gray-900', 'dark:text-white', 'border-b-2', 'border-gray-900', 'dark:border-gray-400', 'bg-gray-50', 'dark:bg-gray-700/50');
                b.classList.add('text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'hover:bg-gray-50', 'dark:hover:bg-gray-700/30');
            });
            document.getElementById('content-' + tabName).classList.remove('hidden');
            const btn = document.getElementById('tab-' + tabName);
            if (btn) {
                btn.classList.add('text-gray-900', 'dark:text-white', 'border-b-2', 'border-gray-900', 'dark:border-gray-400', 'bg-gray-50', 'dark:bg-gray-700/50');
                btn.classList.remove('text-gray-500', 'dark:text-gray-400');

                // Update mobile select if present
                const mobileSelect = document.getElementById('mobile-tab-select');
                if (mobileSelect) mobileSelect.value = tabName;

                // Scroll the tab into view on mobile without shifting the entire page
                const nav = btn.closest('nav');
                if (nav && nav.classList.contains('flex')) {
                    const navRect = nav.getBoundingClientRect();
                    const btnRect = btn.getBoundingClientRect();
                    const offset = (btnRect.left - navRect.left) - (navRect.width / 2) + (btnRect.width / 2);
                    nav.scrollBy({ left: offset, behavior: 'smooth' });
                }
            }
        }

        // Open medications tab by default
        switchTab('medications');
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