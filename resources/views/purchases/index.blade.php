@extends('layouts.app')

@section('title', __('file.purchases'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="mdi mdi-cart-outline text-blue-500"></i>
                    {{ __('file.purchases') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_purchases_system') }}
                </p>
            </div>
            <button onclick="openAddDrawer()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('file.add_purchase') }}
            </button>
        </div>

        <div id="bulk-delete-form" class="hidden mb-6">
            <form method="POST" action="{{ route('purchases.bulkDelete') }}" id="bulk-delete-form-el"
                class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800 rounded-lg p-4 flex justify-between items-center">
                @csrf
                <div id="bulk-ids-container"></div>
                <span class="text-sm font-medium text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.purchase_selected') }}
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
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.supplier') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.inventory_item') }}
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.quantity') }}
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.grand_total') }}
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.status') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider desktop">
                                {{ __('file.cash_register') }}
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

    <div id="profile-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div id="drawer-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm"
            onclick="closeProfileDrawer()"></div>

        <div id="drawer-panel"
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto
                                                                                        w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col
                                                                                        h-[90vh] md:h-full rounded-t-3xl md:rounded-none">

            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="drawer-reference"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.purchase_details') }}</p>
                </div>
                <button onclick="closeProfileDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 text-sm">
                <div class="space-y-5">
                    <div>
                        <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.supplier_and_item') }}
                        </h4>
                        <div class="grid grid-cols-1 gap-3">
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.supplier') }}</label>
                                <div class="text-gray-900 dark:text-white" id="drawer-supplier"></div>
                            </div>
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.inventory_item') }}</label>
                                <div class="text-gray-900 dark:text-white" id="drawer-item"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.financials') }}
                        </h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.quantity') }}</label>
                                <div class="text-gray-900 dark:text-white" id="drawer-qty"></div>
                            </div>
                            <div><label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.grand_total') }}</label>
                                <div class="text-gray-900 dark:text-white" id="drawer-total"></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('file.status') }}
                            </h4>
                            <div id="drawer-status"></div>
                        </div>
                        <div>
                            <h4 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('file.payment_status') }}
                            </h4>
                            <div id="drawer-payment"></div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            {{ __('file.note') }}
                        </h4>
                        <div class="text-gray-900 dark:text-white" id="drawer-note"></div>
                    </div>
                </div>
            </div>

            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeProfileDrawer()"
                    class="w-full px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                    {{ __('file.close') }}
                </button>
            </div>
        </div>
    </div>

    <div id="add-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm" onclick="closeAddDrawer()"></div>

        <div
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto
                                                                                        w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col
                                                                                        h-[90vh] md:h-full rounded-t-3xl md:rounded-none">

            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.add_purchase') }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.new_purchase') }}</p>
                </div>
                <button onclick="closeAddDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="add-form" class="flex-1 flex flex-col overflow-hidden">
                @csrf

                <div class="flex-1 overflow-y-auto px-5 py-5 text-sm space-y-5">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.reference_no') }}</label>
                        <input type="text" name="reference_no" id="add-reference" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.supplier') }}</label>
                        <select name="supplier_id" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.select_supplier') }}</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.inventory_item') }}</label>
                        <select name="item" id="add-item" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.select_item') }}</option>
                            @foreach($inventoryItems as $item)
                                <option value="{{ $item->id }}" data-unit-cost="{{ $item->unit_cost }}">
                                    {{ $item->name }} ({{ $item->sku ?? '—' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.quantity') }}</label>
                            <input type="number" name="total_qty" id="add-qty" min="1" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow"
                                oninput="updateGrandTotal('add')">
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.unit_cost') }}</label>
                            <input type="number" step="0.01" name="total_cost" id="add-cost" min="0" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow"
                                oninput="updateGrandTotal('add')">
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.grand_total') }}</label>
                        <input type="number" step="0.01" name="grand_total" id="add-total" readonly
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 cursor-not-allowed focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:text-white transition-shadow">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                            <select name="status" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="1">{{ __('file.received') }}</option>
                                <option value="2">{{ __('file.partial') }}</option>
                                <option value="3">{{ __('file.pending') }}</option>
                                <option value="4">{{ __('file.ordered') }}</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.payment_status') }}</label>
                            <select name="payment_status" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="1">{{ __('file.due') }}</option>
                                <option value="2">{{ __('file.paid') }}</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.cash_register') }}</label>
                        <select name="cash_register_id"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.none') }}</option>
                            @foreach($cashRegisters as $cr)
                                <option value="{{ $cr->id }}">CR-{{ str_pad($cr->id, 4, '0', STR_PAD_LEFT) }} ({{ $cr->opened_at->format('M d, Y') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.note') }}</label>
                        <textarea name="note" rows="5"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow"></textarea>
                    </div>
                </div>

                <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex gap-3">
                        <button type="button" onclick="closeAddDrawer()"
                            class="flex-1 px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                            {{ __('file.cancel') }}
                        </button>
                        <button type="submit" form="add-form"
                            class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                            {{ __('file.save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="edit-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm" onclick="closeEditDrawer()"></div>

        <div
            class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto
                                                                                        w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col
                                                                                        h-[90vh] md:h-full rounded-t-3xl md:rounded-none">

            <div class="md:hidden flex justify-center pt-4 pb-2">
                <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="edit-drawer-title"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('file.edit_purchase') }}</p>
                </div>
                <button onclick="closeEditDrawer()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="edit-form" class="flex-1 flex flex-col overflow-hidden">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="id" id="edit-id">

                <div class="flex-1 overflow-y-auto px-5 py-5 text-sm space-y-5">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.reference_no') }}</label>
                        <input type="text" name="reference_no" id="edit-reference" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.supplier') }}</label>
                        <select name="supplier_id" id="edit-supplier" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.inventory_item') }}</label>
                        <select name="item" id="edit-item" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            @foreach($inventoryItems as $item)
                                <option value="{{ $item->id }}" data-unit-cost="{{ $item->unit_cost }}">
                                    {{ $item->name }} ({{ $item->sku ?? '—' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.quantity') }}</label>
                            <input type="number" name="total_qty" id="edit-qty" min="1" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow"
                                oninput="updateGrandTotal('edit')">
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.unit_cost') }}</label>
                            <input type="number" step="0.01" name="total_cost" id="edit-cost" min="0" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow"
                                oninput="updateGrandTotal('edit')">
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.grand_total') }}</label>
                        <input type="number" step="0.01" name="grand_total" id="edit-total" readonly
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 cursor-not-allowed focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:text-white transition-shadow">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.status') }}</label>
                            <select name="status" id="edit-status" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="1">{{ __('file.received') }}</option>
                                <option value="2">{{ __('file.partial') }}</option>
                                <option value="3">{{ __('file.pending') }}</option>
                                <option value="4">{{ __('file.ordered') }}</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.payment_status') }}</label>
                            <select name="payment_status" id="edit-payment" required
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                <option value="1">{{ __('file.due') }}</option>
                                <option value="2">{{ __('file.paid') }}</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.cash_register') }}</label>
                        <select name="cash_register_id" id="edit-register"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="">{{ __('file.none') }}</option>
                            @foreach($cashRegisters as $cr)
                                <option value="{{ $cr->id }}">CR-{{ str_pad($cr->id, 4, '0', STR_PAD_LEFT) }} ({{ $cr->opened_at->format('M d, Y') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.note') }}</label>
                        <textarea name="note" id="edit-note" rows="5"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow"></textarea>
                    </div>
                </div>

                <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex gap-3">
                        <button type="button" onclick="closeEditDrawer()"
                            class="flex-1 px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition">
                            {{ __('file.cancel') }}
                        </button>
                        <button type="submit" form="edit-form"
                            class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                            {{ __('file.save_changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const table = $('#docapp-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: '{{ route('purchases.datatable') }}',
                    order: [[1, 'desc']],
                    columnDefs: [
                        { orderable: false, targets: [0, -1] },
                        { searchable: false, targets: [0, 4, 5, 6, 7, -1] }
                    ],
                    columns: [
                        {
                            data: 'id',
                            render: data => `<input type="checkbox" name="ids[]" value="${data}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">`,
                            className: 'text-center'
                        },
                        { data: 'reference_no', render: data => data || '-' },
                        { data: 'supplier_name', render: data => data || '—' },
                        { data: 'item_name', render: data => data || '—' },
                        { data: 'total_qty', className: 'text-center font-medium' },
                        {
                            data: 'grand_total',
                            className: 'text-right font-medium',
                            render: data => data ? '{{ $currency_code }}' + parseFloat(data).toFixed(2) : '—'
                        },
                        {
                            data: 'status',
                            className: 'text-center',
                            render: function (data) {
                                let label, bg;
                                switch (parseInt(data)) {
                                    case 4: label = '{{ __('file.ordered') }}'; bg = 'gray'; break;
                                    case 3: label = '{{ __('file.pending') }}'; bg = 'yellow'; break;
                                    case 2: label = '{{ __('file.partial') }}'; bg = 'blue'; break;
                                    case 1: label = '{{ __('file.received') }}'; bg = 'green'; break;
                                    default: label = '—'; bg = 'gray';
                                }
                                return `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-${bg}-100 dark:bg-${bg}-900/30 text-${bg}-800 dark:text-${bg}-300">${label}</span>`;
                            }
                        },
                        { data: 'cash_register_name', className: 'text-left' },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-right whitespace-nowrap',
                            render: (data, type, row) => `
                                                                                                                                    <div class="flex items-center justify-end gap-1">
                                                                                                                                        <button onclick='openProfileDrawer(${JSON.stringify(row).replace(/'/g, "\\'")})'
                                                                                                                                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="{{ __('file.view') }}">
                                                                                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                                                                                            </svg>
                                                                                                                                        </button>
                                                                                                                                        <button onclick='openEditDrawer(${JSON.stringify(row).replace(/'/g, "\\'")})'
                                                                                                                                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="{{ __('file.edit') }}">
                                                                                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                                                                                            </svg>
                                                                                                                                        </button>
                                                                                                                                        <button type="button" onclick="confirmDelete('${row.delete_url}')"
                                                                                                                                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="{{ __('file.delete') }}">
                                                                                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                                                                                            </svg>
                                                                                                                                        </button>
                                                                                                                                    </div>`
                        }
                    ],
                    layout: {
                        topStart: {
                            buttons: [
                                { extend: 'pageLength', className: 'inline-flex items-center gap-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium transition shadow-sm' },
                                {
                                    extend: 'collection',
                                    text: "{{ __('file.Export') }}",
                                    className: 'bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium',
                                    buttons: [
                                        { extend: 'copy', text: "{{ __('file.copy') }}", exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
                                        { extend: 'excel', text: 'Excel', filename: 'Purchases_{{ date("Y-m-d") }}', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
                                        { extend: 'csv', text: 'CSV', filename: 'Purchases_{{ date("Y-m-d") }}', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
                                        { extend: 'pdf', text: 'PDF', filename: 'Purchases_{{ date("Y-m-d") }}', title: 'Purchase List', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
                                        { extend: 'print', text: "{{ __('file.print') }}", exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
                                    ]
                                }
                            ]
                        },
                        topEnd: 'search',
                        bottomStart: 'info',
                        bottomEnd: 'paging'
                    },
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    language: {
                        search: "",
                        searchPlaceholder: "{{ __('file.search_purchases') }}",
                        lengthMenu: "{{ __('file.show_entries') }}",
                        info: "{{ __('file.showing_entries') }}",
                        emptyTable: "{{ __('file.no_purchases_found') }}",
                        processing: "{{ __('file.processing') }}"
                    }
                });

                $('#select-all').on('change', function () {
                    $('.row-checkbox').prop('checked', this.checked);
                    updateBulkDelete();
                });
                $(document).on('change', '.row-checkbox', updateBulkDelete);

                function updateBulkDelete() {
                    const checked = $('.row-checkbox:checked');
                    const count = checked.length;
                    $('#bulk-delete-form').toggleClass('hidden', count === 0);
                    $('#selected-count').text(count);
                    
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
                        success: function (response) {
                            table.draw(false);
                            $('.row-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            updateBulkDelete();
                            if (response.success) {
                                if (typeof showNotification === 'function') showNotification('Success', response.message, 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', response.message, 'error');
                                else alert(response.message || 'Error deleting purchases');
                            }
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON?.message || 'Something went wrong';
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                            else alert('Error: ' + msg);
                        }
                    });
                });

                let bodyScrollPos = 0;

                window.openProfileDrawer = function (purchase) {
                    document.getElementById('drawer-reference').textContent = purchase.reference_no || '—';
                    document.getElementById('drawer-supplier').textContent = purchase.supplier_name || '—';
                    document.getElementById('drawer-item').textContent = purchase.item_name || '—';
                    document.getElementById('drawer-qty').textContent = purchase.total_qty || '—';
                    document.getElementById('drawer-total').textContent = purchase.grand_total ? '{{ $currency_code }}' + parseFloat(purchase.grand_total).toFixed(2) : '—';
                    document.getElementById('drawer-note').textContent = purchase.note || '—';

                    let statusHtml;
                    switch (parseInt(purchase.status)) {
                        case 4: statusHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">{{ __('file.ordered') }}</span>`; break;
                        case 3: statusHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">{{ __('file.pending') }}</span>`; break;
                        case 2: statusHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">{{ __('file.partial') }}</span>`; break;
                        case 1: statusHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">{{ __('file.received') }}</span>`; break;
                        default: statusHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">—</span>`;
                    }
                    document.getElementById('drawer-status').innerHTML = statusHtml;

                    let paymentHtml = purchase.payment_status == 2
                        ? `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">{{ __('file.paid') }}</span>`
                        : `<span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">{{ __('file.due') }}</span>`;
                    document.getElementById('drawer-payment').innerHTML = paymentHtml;

                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${bodyScrollPos}px`;
                    document.body.style.width = '100%';

                    document.getElementById('profile-drawer').classList.remove('hidden');
                };

                window.closeProfileDrawer = function () {
                    document.getElementById('profile-drawer').classList.add('hidden');
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    window.scrollTo(0, bodyScrollPos);
                };

                window.openAddDrawer = function () {
                    document.getElementById('add-form').reset();
                    document.getElementById('add-reference').value = 'PO-' + Date.now().toString().slice(-6);
                    document.getElementById('add-qty').value = 1;
                    document.getElementById('add-cost').value = '0.00';
                    updateGrandTotal('add');

                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${bodyScrollPos}px`;
                    document.body.style.width = '100%';

                    document.getElementById('add-drawer').classList.remove('hidden');
                };

                window.closeAddDrawer = function () {
                    document.getElementById('add-drawer').classList.add('hidden');
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    window.scrollTo(0, bodyScrollPos);
                };

                window.openEditDrawer = function (purchase) {
                    document.getElementById('edit-id').value = purchase.id;
                    document.getElementById('edit-drawer-title').textContent = purchase.reference_no || '';
                    document.getElementById('edit-reference').value = purchase.reference_no || '';
                    document.getElementById('edit-supplier').value = purchase.supplier_id || '';
                    document.getElementById('edit-item').value = purchase.item || '';
                    document.getElementById('edit-qty').value = purchase.total_qty || 1;
                    document.getElementById('edit-cost').value = purchase.total_cost || '0.00';
                    document.getElementById('edit-total').value = purchase.grand_total || '0.00';
                    document.getElementById('edit-status').value = purchase.status || 1;
                    document.getElementById('edit-payment').value = purchase.payment_status || 1;
                    document.getElementById('edit-register').value = purchase.cash_register_id || '';
                    document.getElementById('edit-note').value = purchase.note || '';
                    updateGrandTotal('edit');

                    bodyScrollPos = window.pageYOffset;
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${bodyScrollPos}px`;
                    document.body.style.width = '100%';

                    document.getElementById('edit-drawer').classList.remove('hidden');
                };

                window.closeEditDrawer = function () {
                    document.getElementById('edit-drawer').classList.add('hidden');
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    window.scrollTo(0, bodyScrollPos);
                };

                window.updateGrandTotal = function (prefix) {
                    const qty = parseFloat(document.getElementById(prefix + '-qty').value) || 0;
                    const cost = parseFloat(document.getElementById(prefix + '-cost').value) || 0;
                    document.getElementById(prefix + '-total').value = (qty * cost).toFixed(2);
                };

                window.confirmDelete = function (url) {
                    if (!confirm('{{ __("file.confirm_delete_purchase") }}')) return;
                    
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            table.draw(false);
                            if (response.success) {
                                if (typeof showNotification === 'function') showNotification('Success', response.message, 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', response.message, 'error');
                            }
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON?.message || 'Delete failed.';
                            if (typeof showNotification === 'function') showNotification('Error', msg, 'error');
                        }
                    });
                };

                document.getElementById('add-item').addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    const cost = selected.getAttribute('data-unit-cost') || '0.00';
                    document.getElementById('add-cost').value = parseFloat(cost).toFixed(2);
                    updateGrandTotal('add');
                });

                document.getElementById('edit-item').addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    const cost = selected.getAttribute('data-unit-cost') || '0.00';
                    document.getElementById('edit-cost').value = parseFloat(cost).toFixed(2);
                    updateGrandTotal('edit');
                });

                document.getElementById('add-form').addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch('{{ route("purchases.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(async response => {
                            if (!response.ok) {
                                const err = await response.json();
                                throw new Error(err.message || 'Save failed');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                table.draw(false);
                                closeAddDrawer();
                                if (typeof showNotification === 'function') showNotification('Success', data.message, 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', data.message, 'error');
                                else alert(data.message || 'Failed to create purchase');
                            }
                        })
                        .catch(error => {
                            if (typeof showNotification === 'function') showNotification('Error', error.message, 'error');
                            else alert(error.message || 'Error creating purchase');
                        });
                });

                document.getElementById('edit-form').addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const id = formData.get('id');

                    const url = `{{ route("purchases.update", ":id") }}`.replace(':id', id);

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(async response => {
                            if (!response.ok) {
                                const err = await response.json();
                                throw new Error(err.message || 'Update failed');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                table.draw(false);
                                closeEditDrawer();
                                if (typeof showNotification === 'function') showNotification('Success', data.message, 'success');
                            } else {
                                if (typeof showNotification === 'function') showNotification('Error', data.message, 'error');
                                else alert(data.message || 'Failed to update purchase');
                            }
                        })
                        .catch(error => {
                            if (typeof showNotification === 'function') showNotification('Error', error.message, 'error');
                            else alert(error.message || 'Error updating purchase');
                        });
                });

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') {
                        if (!document.getElementById('profile-drawer').classList.contains('hidden')) closeProfileDrawer();
                        if (!document.getElementById('add-drawer').classList.contains('hidden')) closeAddDrawer();
                        if (!document.getElementById('edit-drawer').classList.contains('hidden')) closeEditDrawer();
                    }
                });
            });
        </script>
    @endpush
@endsection