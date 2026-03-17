@extends('layouts.app')

@section('title', __('file.add_supplier'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">

        <div class=" flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('suppliers.index') }}"
                class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">{{ __('file.suppliers') }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 dark:text-white">{{ __('file.add_supplier') }}</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.add_new_supplier') }}</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.create_supplier_record') }}</p>
    </div>

    <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-8">
        @csrf

        <div
            class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <!-- Mobile Tab Selector (Visible only on mobile) -->
                <div class="sm:hidden p-4 bg-white dark:bg-gray-800">
                    <label for="mobile-tab-select" class="sr-only">Select a tab</label>
                    <select id="mobile-tab-select" onchange="switchTab(this.value)"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gray-900 dark:focus:ring-gray-500">
                        <option value="basic">{{ __('file.basic_information') }}</option>
                        <option value="contact">{{ __('file.contact_details') }}</option>
                        <option value="status">{{ __('file.status_and_notes') }}</option>
                    </select>
                </div>

                <!-- Desktop/Tablet Tab Navigation (Hidden on mobile) -->
                <nav class="hidden sm:flex overflow-x-auto no-scrollbar " aria-label="Tabs">
                    <button type="button" onclick="switchTab('basic')" id="tab-basic"
                        class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-gray-400 bg-gray-50 dark:bg-gray-700/50 transition-all">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h-4m-6 0H5" />
                            </svg>
                            <span class="hidden sm:inline">{{ __('file.basic_information') }}</span>
                            <span class="sm:hidden">{{ __('file.basic') }}</span>
                        </div>
                    </button>

                    <button type="button" onclick="switchTab('contact')" id="tab-contact"
                        class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="hidden sm:inline">{{ __('file.contact_details') }}</span>
                            <span class="sm:hidden">{{ __('file.contact') }}</span>
                        </div>
                    </button>

                    <button type="button" onclick="switchTab('status')" id="tab-status"
                        class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="hidden sm:inline">{{ __('file.status_and_notes') }}</span>
                            <span class="sm:hidden">{{ __('file.status') }}</span>
                        </div>
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <div id="content-basic" class="tab-content">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.supplier_name') }}
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="{{ __('file.enter_supplier_name') }}">
                                @error('name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                </p> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.category') }}</label>
                                <input type="text" name="category" value="{{ old('category') }}"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="{{ __('file.example_raw_materials_packaging') }}">
                                @error('category') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.description') }}</label>
                            <textarea name="description" rows="4"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                placeholder="{{ __('file.brief_description_about_supplier') }}">{{ old('description') }}</textarea>
                            @error('description') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p> @enderror
                        </div>
                    </div>
                </div>

                <div id="content-contact" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.contact_person') }}</label>
                                <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="{{ __('file.full_name_of_contact_person') }}">
                                @error('contact_person') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.email_address') }}</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="{{ __('file.supplier_example_com') }}">
                                @error('email') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.phone_number') }}</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" minlength="7" maxlength="15"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="{{ __('file.phone_placeholder_mx') }}">
                                @error('phone') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                </p> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.city_or_location') }}</label>
                                <input type="text" name="location" value="{{ old('location') }}"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                    placeholder="{{ __('file.example_mexico_city') }}">
                                @error('location') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.website') }}</label>
                            <input type="url" name="website" value="{{ old('website') }}"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                                placeholder="{{ __('file.website_placeholder') }}">
                            @error('website') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}
                            </p> @enderror
                        </div>
                    </div>
                </div>

                <div id="content-status" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="status" id="status" value="1"
                                class="h-5 w-5 text-gray-900 focus:ring-gray-900 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                                {{ old('status', 1) ? 'checked' : '' }}>
                            <label for="status"
                                class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('file.active_supplier') }}</label>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.additional_notes') }}</label>
                            <textarea name="notes" rows="4"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                placeholder="{{ __('file.notes_payment_terms_etc') }}">{{ old('notes') }}</textarea>
                            @error('notes') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
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
                {{ __('file.create_supplier') }}
            </button>
            <a href="{{ route('suppliers.index') }}"
                class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 dark:bg-transparent dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ __('file.cancel') }}
            </a>
        </div>
    </form>
    </div>

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('text-gray-900', 'dark:text-white', 'border-b-2', 'border-gray-900', 'dark:border-gray-400', 'bg-gray-50', 'dark:bg-gray-700/50');
                btn.classList.add('text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'hover:bg-gray-50', 'dark:hover:bg-gray-700/30');
            });

            // Update mobile select if present
            const mobileSelect = document.getElementById('mobile-tab-select');
            if (mobileSelect) mobileSelect.value = tabName;

            const content = document.getElementById('content-' + tabName);
            if (content) content.classList.remove('hidden');

            const activeTab = document.getElementById('tab-' + tabName);
            if (activeTab) {
                activeTab.classList.remove('text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'hover:bg-gray-50', 'dark:hover:bg-gray-700/30');
                activeTab.classList.add('text-gray-900', 'dark:text-white', 'border-b-2', 'border-gray-900', 'dark:border-gray-400', 'bg-gray-50', 'dark:bg-gray-700/50');

                // Scroll the tab into view on mobile without shifting the entire page
                const nav = activeTab.closest('nav');
                if (nav && nav.classList.contains('flex')) {
                    const navRect = nav.getBoundingClientRect();
                    const btnRect = activeTab.getBoundingClientRect();
                    const offset = (btnRect.left - navRect.left) - (navRect.width / 2) + (btnRect.width / 2);
                    nav.scrollBy({ left: offset, behavior: 'smooth' });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            switchTab('basic');
        });
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