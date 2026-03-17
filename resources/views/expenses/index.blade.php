@extends('layouts.app')

@section('title', __('file.expenses'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="mdi mdi-cash-multiple text-indigo-600"></i>
                    {{ __('file.expenses') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_expenses_system') }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" id="filter-toggle"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-gray-600 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    {{ __('file.filters') }}
                    <span id="filter-count"
                        class="hidden ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200"></span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                @can('expenses.create')
                    <button type="button" onclick="openAddDrawer()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('file.add_expense') }}
                    </button>
                @endcan
            </div>
        </div>

        <div id="main-backdrop"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300 opacity-0"></div>

        <div id="filter-drawer"
            class="fixed z-50 bg-white dark:bg-gray-800 shadow-2xl transition-transform duration-300 ease-in-out
                           bottom-0 left-0 right-0 max-h-[85vh] rounded-t-2xl translate-y-full
                           md:top-0 md:right-0 md:bottom-auto md:left-auto md:h-full md:w-96 md:max-h-none md:rounded-none md:rounded-l-lg md:translate-y-0 md:translate-x-full">

            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('file.filters') }}
                </h3>
                <button type="button" id="close-filter-drawer"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 p-1.5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(85vh-140px)] md:max-h-[calc(100vh-140px)]">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.category') }}
                        </label>
                        <select id="filter-category"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.all_categories') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div
                class="bottom-0 left-0 right-0 flex gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                <button type="button" id="clear-filters"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    {{ __('file.clear') }}
                </button>
                <button type="button" id="apply-filters"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm">
                    {{ __('file.apply') }}
                </button>
            </div>
        </div>

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('expenses.bulkDelete') }}" id="bulk-delete-form-el"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
                <div id="bulk-ids-container"></div>
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.expenses_selected') }}
                </span>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                    {{ __('file.delete_selected') }}
                </button>
            </form>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="docapp-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-right all pr-6" style="width: 80px; min-width: 80px;">
                                <input type="checkbox" id="select-all"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.reference_no') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.category') }}
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.amount') }}
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.date') }}
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="profile-drawer"
        class="fixed z-50 bg-white dark:bg-gray-800 shadow-2xl transition-transform duration-300 ease-in-out
                       bottom-0 left-0 right-0 max-h-[85vh] rounded-t-2xl translate-y-full
                       md:top-0 md:right-0 md:bottom-auto md:left-auto md:h-full md:w-96 md:max-h-none md:rounded-none md:rounded-l-2xl md:translate-y-0 md:translate-x-full">

        <div class="flex flex-col h-full">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="drawer-reference"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.expense_details') }}</p>
                </div>
                <button onclick="closeProfileDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-6 space-y-8">
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">
                        {{ __('file.information') }}
                    </h4>
                    <div class="space-y-4">
                        <div class="flex justify-between items-start pb-3 border-b border-gray-100 dark:border-gray-700/50">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.reference_no') }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" id="drawer-reference-no"></span>
                        </div>
                        <div class="flex justify-between items-start pb-3 border-b border-gray-100 dark:border-gray-700/50">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.category') }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" id="drawer-category"></span>
                        </div>
                        <div class="flex justify-between items-start pb-3 border-b border-gray-100 dark:border-gray-700/50">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.amount') }}</span>
                            <span class="text-sm font-bold text-red-600 dark:text-red-400" id="drawer-amount"></span>
                        </div>
                        <div class="flex justify-between items-start pb-3 border-b border-gray-100 dark:border-gray-700/50">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.cash_register') }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white"
                                id="drawer-cash-register"></span>
                        </div>
                        <div class="flex justify-between items-start pb-3 border-b border-gray-100 dark:border-gray-700/50">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.added_by') }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" id="drawer-user"></span>
                        </div>
                        <div class="flex justify-between items-start pb-3 border-b border-gray-100 dark:border-gray-700/50">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.date') }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" id="drawer-date"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">
                        {{ __('file.note') }}
                    </h4>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-300 italic"
                        id="drawer-note">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/80 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeProfileDrawer()"
                    class="w-full px-4 py-3 bg-gray-900 dark:bg-gray-700 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 dark:hover:bg-gray-600 transition shadow-sm">
                    {{ __('file.close') }}
                </button>
            </div>
        </div>
    </div>

    <div id="add-drawer"
        class="fixed z-50 bg-white dark:bg-gray-800 shadow-2xl transition-transform duration-300 ease-in-out
                       bottom-0 left-0 right-0 max-h-[85vh] rounded-t-2xl translate-y-full
                       md:top-0 md:right-0 md:bottom-auto md:left-auto md:h-full md:w-96 md:max-h-none md:rounded-none md:rounded-l-2xl md:translate-y-0 md:translate-x-full">

        <div class="flex flex-col h-full">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.add_expense') }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.create_new_expense') }}</p>
                </div>
                <button onclick="closeAddDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6">
                <form id="add-form" class="space-y-5 text-sm">
                    @csrf
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.reference_no') }}
                            *</label>
                        <input type="text" name="reference_no" id="add-reference-no" required
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all shadow-sm" />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.category') }}
                            *</label>
                        <select name="expense_category_id" required
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all shadow-sm">
                            <option value="">{{ __('file.select_category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.amount') }}
                            *</label>
                        <input type="number" step="0.01" name="amount" required
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all shadow-sm" />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.cash_register') }}</label>
                        <select name="cash_register_id"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all shadow-sm">
                            <option value="">{{ __('file.select_register_optional') }}</option>
                            @foreach($cashRegisters as $register)
                                <option value="{{ $register->id }}">
                                    CR-{{ str_pad($register->id, 4, '0', STR_PAD_LEFT) }}
                                    ({{ $register->user ? $register->user->name : __('file.unknown') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.note') }}</label>
                        <textarea name="note" rows="4"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all shadow-sm resize-none"></textarea>
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/80 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3 text-sm">
                    <button onclick="closeAddDrawer()"
                        class="flex-1 px-4 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-semibold rounded-xl border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        {{ __('file.cancel') }}
                    </button>
                    <button type="submit" form="add-form"
                        class="flex-1 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition shadow-sm">
                        {{ __('file.add') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="edit-drawer"
        class="fixed z-50 bg-white dark:bg-gray-800 shadow-2xl transition-transform duration-300 ease-in-out
                       bottom-0 left-0 right-0 max-h-[85vh] rounded-t-2xl translate-y-full
                       md:top-0 md:right-0 md:bottom-auto md:left-auto md:h-full md:w-96 md:max-h-none md:rounded-none md:rounded-l-2xl md:translate-y-0 md:translate-x-full">

        <div class="flex flex-col h-full">
            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="edit-drawer-title"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.edit_expense') }}</p>
                </div>
                <button onclick="closeEditDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6">
                <form id="edit-form" class="space-y-5 text-sm">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="id" id="edit-id">

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.reference_no') }}
                            *</label>
                        <input type="text" name="reference_no" id="edit-reference-no" required
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all shadow-sm" />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.category') }}
                            *</label>
                        <select name="expense_category_id" id="edit-category-id" required
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all shadow-sm">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.amount') }}
                            *</label>
                        <input type="number" step="0.01" name="amount" id="edit-amount" required
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all shadow-sm" />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.cash_register') }}</label>
                        <select name="cash_register_id" id="edit-cash-register-id"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all shadow-sm">
                            <option value="">{{ __('file.select_register_optional') }}</option>
                            @foreach($cashRegisters as $register)
                                <option value="{{ $register->id }}">
                                    CR-{{ str_pad($register->id, 4, '0', STR_PAD_LEFT) }}
                                    ({{ $register->user ? $register->user->name : __('file.unknown') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.note') }}</label>
                        <textarea name="note" id="edit-note" rows="4"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-all shadow-sm resize-none"></textarea>
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/80 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3 text-sm">
                    <button onclick="closeEditDrawer()"
                        class="flex-1 px-4 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-semibold rounded-xl border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        {{ __('file.cancel') }}
                    </button>
                    <button type="submit" form="edit-form"
                        class="flex-1 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition shadow-sm">
                        {{ __('file.save_changes') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const filterToggle = document.getElementById('filter-toggle');
                const filterDrawer = document.getElementById('filter-drawer');
                const mainBackdrop = document.getElementById('main-backdrop');
                const closeFilterDrawer = document.getElementById('close-filter-drawer');
                const filterCount = document.getElementById('filter-count');
                const filterCategory = document.getElementById('filter-category');
                const profileDrawer = document.getElementById('profile-drawer');
                const addDrawer = document.getElementById('add-drawer');
                const editDrawer = document.getElementById('edit-drawer');

                function openDrawer(drawer) {
                    mainBackdrop.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => {
                        mainBackdrop.classList.add('opacity-100');
                        mainBackdrop.classList.remove('opacity-0');
                        if (window.innerWidth >= 768) {
                            drawer.classList.remove('md:translate-x-full');
                        } else {
                            drawer.classList.remove('translate-y-full');
                        }
                    }, 10);
                }

                function closeDrawer(drawer) {
                    mainBackdrop.classList.remove('opacity-100');
                    mainBackdrop.classList.add('opacity-0');
                    if (window.innerWidth >= 768) {
                        drawer.classList.add('md:translate-x-full');
                    } else {
                        drawer.classList.add('translate-y-full');
                    }
                    setTimeout(() => {
                        mainBackdrop.classList.add('hidden');
                        document.body.style.overflow = '';
                    }, 300);
                }

                filterToggle.addEventListener('click', e => { e.stopPropagation(); openDrawer(filterDrawer); });
                closeFilterDrawer.addEventListener('click', () => closeDrawer(filterDrawer));
                mainBackdrop.addEventListener('click', () => {
                    closeDrawer(filterDrawer);
                    closeDrawer(profileDrawer);
                    closeDrawer(addDrawer);
                    closeDrawer(editDrawer);
                });
                [filterDrawer, profileDrawer, addDrawer, editDrawer].forEach(d => {
                    d.addEventListener('click', e => e.stopPropagation());
                });

                function updateFilterCount() {
                    const count = [filterCategory.value].filter(Boolean).length;
                    filterCount.textContent = count;
                    filterCount.classList.toggle('hidden', count === 0);
                }

                fetch('{{ route('expenses.filters') }}?column=category')
                    .then(r => r.json())
                    .then(data => {
                        Object.entries(data).forEach(([id, name]) => {
                            const opt = document.createElement('option');
                            opt.value = id;
                            opt.textContent = name;
                            filterCategory.appendChild(opt);
                        });
                    })
                    .catch(err => console.error('Failed to load categories', err));

                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: {
                        url: '{{ route('expenses.datatable') }}',
                        data: function (d) {
                            d.category = document.getElementById('filter-category').value;
                        }
                    },
                    order: [[4, 'desc']],
                    columnDefs: [
                        { orderable: false, targets: [0, 5] },
                        { searchable: false, targets: [0, 3, 5] }
                    ],
                    columns: [
                        {
                            data: 'id',
                            render: d => `<input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="${d}">`,
                            className: 'text-center'
                        },
                        { data: 'reference_no', render: d => d || '-' },
                        { data: 'category_name', render: d => d || '-' },
                        {
                            data: 'amount',
                            className: 'text-right font-medium text-red-600 dark:text-red-400',
                            render: d => parseFloat(d).toFixed(2)
                        },
                        { data: 'created_at', className: 'text-center' },
                        {
                            data: null,
                            render: (data, type, row) => `
                                                <div class="flex justify-end gap-1">
                                                    <button onclick="openProfileDrawer(${row.id})"
                                                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 transition" title="{{ __('file.view') }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </button>
                                                    <button onclick="openEditDrawer(${row.id})"
                                                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 transition" title="{{ __('file.edit') }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </button>
                                                    <form method="POST" action="${row.delete_url}" class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" onclick="return confirm('{{ __('file.confirm_delete_expense') }}')"
                                                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 transition" title="{{ __('file.delete') }}">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>`,
                            className: 'text-right whitespace-nowrap'
                        }
                    ],
                    layout: {
                        topStart: {
                            buttons: [
                                { extend: 'pageLength', className: 'px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300' },
                                {
                                    extend: 'collection',
                                    text: "{{ __('file.Export') }}",
                                    className: 'inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 hover:bg-gray-700 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm',
                                    buttons: [
                                        { extend: 'copy', exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] } },
                                        { extend: 'excel', filename: 'Expenses_{{ date("Y-m-d") }}', exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] } },
                                        { extend: 'csv', filename: 'Expenses_{{ date("Y-m-d") }}', exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] } },
                                        { extend: 'pdf', filename: 'Expenses_{{ date("Y-m-d") }}', title: 'Expenses List', exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] } },
                                        { extend: 'print', exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] } }
                                    ]
                                }
                            ]
                        },
                        topEnd: 'search',
                        bottomStart: 'info',
                        bottomEnd: 'paging'
                    },
                    language: {
                        search: "",
                        searchPlaceholder: "{{ __('file.search_expenses') }}",
                        lengthMenu: "_MENU_",
                        info: "{{ __('file.showing_entries') }}",
                        paginate: {
                            next: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>',
                            previous: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>'
                        }
                    }
                });

                document.getElementById('apply-filters').addEventListener('click', () => {
                    table.draw();
                    updateFilterCount();
                    closeDrawer(filterDrawer);
                });

                document.getElementById('clear-filters').addEventListener('click', () => {
                    filterCategory.value = '';
                    table.draw();
                    updateFilterCount();
                });

                filterCategory.addEventListener('change', updateFilterCount);

                $('#select-all').on('change', function () {
                    $('.row-checkbox').prop('checked', this.checked);
                    updateBulkDelete();
                });
                $(document).on('change', '.row-checkbox', updateBulkDelete);

                function updateBulkDelete() {
                    const checked = $('.row-checkbox:checked');
                    const count = checked.length;
                    document.getElementById('bulk-delete-form').classList.toggle('hidden', count === 0);
                    document.getElementById('selected-count').textContent = count;

                    const container = document.getElementById('bulk-ids-container');
                    container.innerHTML = '';
                    checked.each(function () {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = this.value;
                        container.appendChild(input);
                    });
                }

                $('#bulk-delete-form-el').on('submit', function (e) {
                    e.preventDefault();
                    if (!confirm('{{ __("file.confirm_delete_selected") }}')) return;

                    $.ajax({
                        url: this.action,
                        method: 'POST',
                        data: $(this).serialize(),
                        success: res => {
                            table.draw(false);
                            $('.row-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            updateBulkDelete();
                            if (res.success) {
                                if (typeof showNotification === 'function') showNotification('Success', res.message, 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', res.message, 'error');
                                else alert(res.message);
                            }
                        },
                        error: (xhr) => {
                            const msg = xhr.responseJSON?.message || 'Error deleting expenses';
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                            else alert(msg);
                        }
                    });
                });

                let scrollPos = 0;

                window.openProfileDrawer = id => {
                    const url = `{{ route('expenses.show', ':id') }}`.replace(':id', id);
                    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => r.json())
                        .then(row => {
                            document.getElementById('drawer-reference').textContent = row.reference_no || '—';
                            document.getElementById('drawer-reference-no').textContent = row.reference_no || '—';
                            document.getElementById('drawer-category').textContent = row.category_name || '—';
                            document.getElementById('drawer-amount').textContent = parseFloat(row.amount).toFixed(2);
                            document.getElementById('drawer-cash-register').textContent = row.cash_register_name || '—';
                            document.getElementById('drawer-user').textContent = row.user_name || '—';
                            document.getElementById('drawer-date').textContent = row.created_at || '—';
                            document.getElementById('drawer-note').textContent = row.note || '—';
                            openDrawer(profileDrawer);
                        })
                        .catch(err => {
                            const msg = 'Failed to fetch details: ' + err.message;
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                            else alert(msg);
                        });
                };

                window.closeProfileDrawer = () => {
                    closeDrawer(profileDrawer);
                };

                window.openAddDrawer = () => {
                    document.getElementById('add-form').reset();
                    document.getElementById('add-reference-no').value = 'EXP-' + Date.now();
                    openDrawer(addDrawer);
                };

                window.closeAddDrawer = () => {
                    closeDrawer(addDrawer);
                };

                window.openEditDrawer = id => {
                    const url = `{{ route('expenses.show', ':id') }}`.replace(':id', id);
                    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => r.json())
                        .then(row => {
                            document.getElementById('edit-id').value = row.id;
                            document.getElementById('edit-drawer-title').textContent = row.reference_no || 'Edit Expense';
                            document.getElementById('edit-reference-no').value = row.reference_no || '';
                            document.getElementById('edit-category-id').value = row.expense_category_id || '';
                            document.getElementById('edit-amount').value = row.amount || '';
                            document.getElementById('edit-cash-register-id').value = row.cash_register_id || '';
                            document.getElementById('edit-note').value = row.note || '';
                            openDrawer(editDrawer);
                        })
                        .catch(err => {
                            const msg = 'Failed to fetch details: ' + err.message;
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                            else alert(msg);
                        });
                };

                window.closeEditDrawer = () => {
                    closeDrawer(editDrawer);
                };

                document.getElementById('add-form')?.addEventListener('submit', e => {
                    e.preventDefault();
                    const formData = new FormData(e.target);

                    fetch('{{ route('expenses.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                table.draw(false);
                                closeAddDrawer();
                                if (typeof showNotification === 'function') showNotification('Success', data.message, 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', data.message, 'error');
                                else alert(data.message);
                            }
                        })
                        .catch(err => {
                            if (typeof showNotification === 'function') showNotification('Error', err.message, 'error');
                            else alert('Error: ' + err.message);
                        });
                });

                document.getElementById('edit-form')?.addEventListener('submit', e => {
                    e.preventDefault();
                    const formData = new FormData(e.target);
                    const id = formData.get('id');
                    const url = `{{ route('expenses.update', ':id') }}`.replace(':id', id);

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                table.draw(false);
                                closeEditDrawer();
                                if (typeof showNotification === 'function') showNotification('Success', data.message, 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', data.message, 'error');
                                else alert(data.message);
                            }
                        })
                        .catch(err => {
                            if (typeof showNotification === 'function') showNotification('Error', err.message, 'error');
                            else alert('Error: ' + err.message);
                        });
                });

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') {
                        closeDrawer(filterDrawer);
                        closeDrawer(profileDrawer);
                        closeDrawer(addDrawer);
                        closeDrawer(editDrawer);
                    }
                });
            });
        </script>
    @endpush
@endsection