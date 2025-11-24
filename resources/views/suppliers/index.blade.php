{{-- resources/views/suppliers/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Suppliers')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">Suppliers</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your inventory suppliers</p>
        </div>
        <a href="{{ route('suppliers.create') }}"
           class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2.5 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors duration-200 shadow-sm whitespace-nowrap">
            <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="sm:inline">Add Supplier</span>
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-5 sm:mb-6">
        <form method="GET" class="flex flex-col gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name, email, phone, contact..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span class="hidden sm:inline">Search</span>
                </button>
                <a href="{{ route('suppliers.index') }}"
                   class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Bulk Delete -->
    <form method="POST" action="{{ route('suppliers.bulkDelete') }}" id="bulk-delete-form" class="hidden mb-4">
        @csrf @method('DELETE')
        <input type="hidden" name="ids" id="bulk-ids">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 sm:p-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <span class="text-sm text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> supplier(s) selected
                </span>
                <button type="submit" onclick="return confirm('Delete selected suppliers?')"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                </button>
            </div>
        </div>
    </form>

    <!-- Mobile Cards -->
    <div class="space-y-4 sm:hidden">
        @forelse($suppliers as $supplier)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="ids[]" value="{{ $supplier->id }}" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $supplier->name }}</div>
                            @if($supplier->contact_person)
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $supplier->contact_person }}</div>
                            @endif
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $supplier->status ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                        {{ $supplier->status ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm mb-3">
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs">Email</div>
                        <div class="truncate">{{ $supplier->email ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs">Phone</div>
                        <div>{{ $supplier->phone ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs">Location</div>
                        <div class="truncate">{{ $supplier->location ?? '—' }}</div>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('suppliers.edit', $supplier) }}"
                       class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this supplier?')"
                                class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5"/>
                </svg>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">No suppliers found</p>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table -->
    <div class="hidden sm:block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="select-all" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <x-sort-link field="name" :sort="$sort" :direction="$direction">Name</x-sort-link>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Contact Person</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Location</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors duration-150">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input type="checkbox" name="ids[]" value="{{ $supplier->id }}" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $supplier->name }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-600 dark:text-gray-300">{{ $supplier->contact_person ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-600 dark:text-gray-300 truncate max-w-32">{{ $supplier->email ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-600 dark:text-gray-300">{{ $supplier->phone ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-600 dark:text-gray-300">{{ $supplier->location ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium {{ $supplier->status ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                    {{ $supplier->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('suppliers.edit', $supplier) }}"
                                       class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this supplier?')"
                                                class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition-colors" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5"/>
                                </svg>
                                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">No suppliers found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $suppliers->appends(request()->query())->links() }}
    </div>
</div>

<script>
    document.getElementById('select-all')?.addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkDelete();
    });
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.addEventListener('change', updateBulkDelete));

    function updateBulkDelete() {
        const checked = document.querySelectorAll('.row-checkbox:checked').length;
        const form = document.getElementById('bulk-delete-form');
        const idsInput = document.getElementById('bulk-ids');
        const countSpan = document.getElementById('selected-count');
        if (checked > 0) {
            form.classList.remove('hidden');
            idsInput.value = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value).join(',');
            countSpan.textContent = checked;
        } else {
            form.classList.add('hidden');
        }
    }
</script>
@endsection