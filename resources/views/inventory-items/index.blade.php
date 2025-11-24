{{-- resources/views/inventoryitems/index.blade.php --}}
@extends('layouts.app')
@section('title', __('file.inventory_items'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.inventory_items') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.manage_inventory_records') }}</p>
        </div>
        <a href="{{ route('inventoryitems.create') }}"
           class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2.5 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors duration-200 shadow-sm whitespace-nowrap">
            <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="sm:inline">{{ __('file.add_item') }}</span>
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-5 sm:mb-6">
        <form method="GET" id="search-form" class="flex flex-col gap-3">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="{{ __('file.search_placeholder') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow">
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span class="hidden sm:inline">{{ __('file.search') }}</span>
                </button>

                <a href="{{ route('inventoryitems.index') }}"
                   class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                    {{ __('file.clear') }}
                </a>
            </div>
        </form>
    </div>

    <form method="POST" action="{{ route('inventoryitems.bulk-delete') }}" id="bulk-delete-form" class="hidden mb-4">
        @csrf @method('DELETE')
        <input type="hidden" name="ids" id="bulk-ids">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 sm:p-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <span class="text-sm text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> {{ __('file.item_selected') }}
                </span>
                <button type="submit" 
                        onclick="return confirm('{{ __('file.confirm_delete_selected') }}')"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ __('file.delete') }}
                </button>
            </div>
        </div>
    </form>

    <div class="sm:hidden text-sm text-gray-600 dark:text-gray-400 mb-3">
        {{ __('file.showing_results', ['from' => $items->firstItem(), 'to' => $items->lastItem(), 'total' => $items->total()]) }}
    </div>

    <div class="sm:hidden mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">{{ __('file.sort_by') }}</h3>
        <div class="grid grid-cols-2 gap-2 text-sm">
            <x-sort-link field="name" :sort="$sort" :direction="$direction">{{ __('file.name') }}</x-sort-link>
            <x-sort-link field="sku" :sort="$sort" :direction="$direction">SKU</x-sort-link>
            <x-sort-link field="category_id" :sort="$sort" :direction="$direction">{{ __('file.category') }}</x-sort-link>
            <x-sort-link field="current_stock" :sort="$sort" :direction="$direction">{{ __('file.stock') }}</x-sort-link>
        </div>
    </div>

    <div class="space-y-4 sm:hidden">
        @forelse($items as $item)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700 border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $item->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->sku }}</div>
                            </div>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                        {{ $item->current_stock <= $item->reorder_point ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' }}">
                        {{ $item->current_stock }} {{ $item->unit_of_measure }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm mb-4">
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs">{{ __('file.category') }}</div>
                        <div class="truncate">{{ $item->category?->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs">{{ __('file.supplier') }}</div>
                        <div>{{ $item->primarySupplier?->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs">{{ __('file.cost') }}</div>
                        <div>Rs. {{ number_format($item->unit_cost, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs">{{ __('file.price') }}</div>
                        <div>Rs. {{ number_format($item->unit_price, 2) }}</div>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('inventoryitems.show', $item) }}"
                       class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
                       title="{{ __('file.view_details') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    <a href="{{ route('inventoryitems.edit', $item) }}"
                       class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
                       title="{{ __('file.edit') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('inventoryitems.destroy', $item) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('{{ __('file.confirm_delete_item') }}')"
                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors"
                                title="{{ __('file.delete') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_items_found') }}</p>
            </div>
        @endforelse
    </div>

    <div class="hidden sm:block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="select-all" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="name" :sort="$sort" :direction="$direction">{{ __('file.name') }}</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="sku" :sort="$sort" :direction="$direction">SKU</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="category_id" :sort="$sort" :direction="$direction">{{ __('file.category') }}</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="current_stock" :sort="$sort" :direction="$direction">{{ __('file.stock') }}</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="unit_cost" :sort="$sort" :direction="$direction">{{ __('file.cost') }}</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="unit_price" :sort="$sort" :direction="$direction">{{ __('file.price') }}</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="primary_supplier_id" :sort="$sort" :direction="$direction">{{ __('file.supplier') }}</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('file.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors duration-150">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700 border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-600 dark:text-gray-300">{{ $item->sku }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 truncate max-w-28">
                                    {{ Str::limit($item->category?->name, 20) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium
                                    {{ $item->current_stock <= $item->reorder_point ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' }}">
                                    {{ $item->current_stock }} {{ $item->unit_of_measure }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">Rs. {{ number_format($item->unit_cost, 2) }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Rs. {{ number_format($item->unit_price, 2) }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-600 dark:text-gray-300">{{ $item->primarySupplier?->name ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('inventoryitems.show', $item) }}"
                                       class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" 
                                       title="{{ __('file.view_details') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('inventoryitems.edit', $item) }}"
                                       class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" 
                                       title="{{ __('file.edit') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('inventoryitems.destroy', $item) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('{{ __('file.confirm_delete_item') }}')"
                                                class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" 
                                                title="{{ __('file.delete') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_items_found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 sm:hidden">
        {{ $items->appends(request()->query())->links() }}
    </div>

    <div class="hidden sm:block mt-6">
        {{ $items->appends(request()->query())->links() }}
    </div>
</div>

<script>
    document.getElementById('select-all')?.addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkDelete();
    });
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.addEventListener('change', updateBulkDelete));

    function updateBulkDelete() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const count = checked.length;
        const form = document.getElementById('bulk-delete-form');
        const idsInput = document.getElementById('bulk-ids');
        const countSpan = document.getElementById('selected-count');

        if (count > 0) {
            form.classList.remove('hidden');
            idsInput.value = Array.from(checked).map(cb => cb.value).join(',');
            countSpan.textContent = count;
        } else {
            form.classList.add('hidden');
        }
    }
</script>
@endsection