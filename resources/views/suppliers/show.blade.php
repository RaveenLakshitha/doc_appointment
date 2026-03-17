@extends('layouts.app')

@section('title', $supplier->name)

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <!-- Header Section -->
        <div class=" mb-6">
            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-3">
                <a href="{{ route('suppliers.index') }}" class="hover:text-gray-900 dark:hover:text-gray-200">
                    {{ __('file.suppliers') }}
                </a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white">{{ Str::limit($supplier->name, 30) }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-3xl font-semibold text-gray-900 dark:text-white mb-2">{{ $supplier->name }}</h1>
                    <div class="flex flex-wrap items-center gap-2">
                        @if($supplier->category)
                            <span
                                class="px-2.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300">
                                {{ $supplier->category }}
                            </span>
                        @endif

                        <span
                            class="px-2.5 py-1 text-xs border rounded
                                        {{ $supplier->status ? 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300' : 'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400' }}">
                            {{ $supplier->status ? __('file.active') : __('file.inactive') }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-2">
                    @can('suppliers.edit')
                        <a href="{{ route('suppliers.edit', $supplier) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm rounded hover:bg-gray-800 dark:hover:bg-gray-600">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            {{ __('file.edit') }}
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-medium text-gray-900 dark:text-white">
                            {{ __('file.basic_information') }}
                        </h2>
                    </div>
                    <div class="p-5 space-y-5">
                        <!-- Description -->
                        @if($supplier->description)
                            <div>
                                <label
                                    class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">{{ __('file.description') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplier->description }}</p>
                            </div>
                        @endif

                        <!-- Contact Grid -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            @if($supplier->contact_person)
                                <div>
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase block mb-1">
                                        {{ __('file.contact_person') }}
                                    </label>
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $supplier->contact_person }}</div>
                                </div>
                            @endif

                            @if($supplier->email)
                                <div>
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase block mb-1">
                                        {{ __('file.email_address') }}
                                    </label>
                                    <a href="mailto:{{ $supplier->email }}"
                                        class="text-sm text-gray-900 dark:text-white hover:underline">
                                        {{ $supplier->email }}
                                    </a>
                                </div>
                            @endif

                            @if($supplier->phone)
                                <div>
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase block mb-1">
                                        {{ __('file.phone_number') }}
                                    </label>
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $supplier->phone }}</div>
                                </div>
                            @endif
                        </div>

                        <!-- Website & Location -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            @if($supplier->location)
                                <div>
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase block mb-1">
                                        {{ __('file.location') }}
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $supplier->location }}</p>
                                </div>
                            @endif

                            @if($supplier->website)
                                <div>
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase block mb-1">
                                        {{ __('file.website') }}
                                    </label>
                                    <a href="{{ $supplier->website }}" target="_blank"
                                        class="text-sm text-gray-900 dark:text-white hover:underline">
                                        {{ $supplier->website }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Primary Supplied Items -->
                @if($supplier->inventoryItems->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-base font-medium text-gray-900 dark:text-white">
                                {{ __('file.primary_supplier_for') }}
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">
                                            {{ __('file.item') }}
                                        </th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">
                                            {{ __('file.sku') }}
                                        </th>
                                        <th
                                            class="px-5 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">
                                            {{ __('file.current_stock') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($supplier->inventoryItems as $item)
                                        <tr>
                                            <td class="px-5 py-3">
                                                <a href="{{ route('inventory.show', $item) }}"
                                                    class="text-sm text-gray-900 dark:text-white hover:underline">
                                                    {{ $item->name }}
                                                </a>
                                            </td>
                                            <td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $item->sku ?? '—' }}
                                            </td>
                                            <td class="px-5 py-3 text-right text-sm text-gray-900 dark:text-white">
                                                {{ number_format($item->current_stock ?? 0) }} {{ $item->unit_of_measure ?? '' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Secondary Supplied Items -->
                @if($supplier->secondaryItems->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-base font-medium text-gray-900 dark:text-white">
                                {{ __('file.secondary_supplier_for') }}
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">
                                            {{ __('file.item') }}
                                        </th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">
                                            {{ __('file.supplier_item_code') }}
                                        </th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">
                                            {{ __('file.price') }}
                                        </th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">
                                            {{ __('file.moq') }}
                                        </th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase">
                                            {{ __('file.lead_time') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($supplier->secondaryItems as $item)
                                        <tr>
                                            <td class="px-5 py-3">
                                                <a href="{{ route('inventory.show', $item) }}"
                                                    class="text-sm text-gray-900 dark:text-white hover:underline">
                                                    {{ $item->name }}
                                                </a>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->sku ?? '—' }}</div>
                                            </td>
                                            <td class="px-5 py-3 text-sm text-gray-900 dark:text-white">
                                                {{ $item->pivot->supplier_item_code ?? '—' }}
                                            </td>
                                            <td class="px-5 py-3 text-sm text-gray-900 dark:text-white">
                                                {{ $currency_code }} {{ number_format($item->pivot->supplier_price ?? 0, 2) }}
                                            </td>
                                            <td class="px-5 py-3 text-sm text-gray-900 dark:text-white">
                                                {{ $item->pivot->minimum_order_quantity ?? '—' }}
                                            </td>
                                            <td class="px-5 py-3 text-sm text-gray-900 dark:text-white">
                                                {{ $item->pivot->lead_time_days ? $item->pivot->lead_time_days . ' ' . __('file.days') : '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-medium text-gray-900 dark:text-white">
                            {{ __('file.status') }}
                        </h2>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.supplier_status') }}</span>
                            @if($supplier->status)
                                <span
                                    class="px-2.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300">
                                    {{ __('file.active') }}
                                </span>
                            @else
                                <span
                                    class="px-2.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded text-gray-500 dark:text-gray-400">
                                    {{ __('file.inactive') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-medium text-gray-900 dark:text-white">
                            {{ __('file.statistics') }}
                        </h2>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.primary_items') }}</span>
                            <span
                                class="text-lg font-medium text-gray-900 dark:text-white">{{ $supplier->inventoryItems->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('file.secondary_items') }}</span>
                            <span
                                class="text-lg font-medium text-gray-900 dark:text-white">{{ $supplier->secondaryItems->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-medium text-gray-900 dark:text-white">
                            {{ __('file.quick_actions') }}
                        </h2>
                    </div>
                    <div class="p-5 space-y-2">
                        <a href="{{ route('suppliers.edit', $supplier) }}"
                            class="w-full flex items-center justify-center px-4 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            {{ __('file.edit_supplier') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection