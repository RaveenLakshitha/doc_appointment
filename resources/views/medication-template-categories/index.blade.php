{{-- resources/views/medication-template-categories/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Medication Template Categories')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                Medication Template Categories
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Manage categories for prescription templates
            </p>
        </div>
        <a href="{{ route('medication-template-categories.create') }}"
           class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Category
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..."
                   class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('medication-template-categories.index') }}" class="px-4 py-2.5 text-gray-600 hover:text-gray-900">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Bulk Delete Form -->
    <form method="POST" action="{{ route('medication-template-categories.bulkDelete') }}" id="bulk-delete-form" class="hidden mb-4">
        @csrf @method('DELETE')
        <input type="hidden" name="ids" id="bulk-ids">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <span class="text-sm text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> categor(y|ies) selected
                </span>
                <button type="submit" onclick="return confirm('Delete selected categories?')"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                    Delete Selected
                </button>
            </div>
        </div>
    </form>

    <!-- Desktop Table -->
    <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" id="select-all" class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                        <x-sort-link field="name" :sort="$sort" :direction="$direction">Name</x-sort-link>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                        Color
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                        <x-sort-link field="order" :sort="$sort" :direction="$direction">Order</x-sort-link>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                        <x-sort-link field="is_active" :sort="$sort" :direction="$direction">Status</x-sort-link>
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                        <td class="px-6 py-4">
                            <input type="checkbox" name="ids[]" value="{{ $category->id }}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600">
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            {{ $category->name }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                  style="background-color: {{ $category->color ? "rgb(var(--tw-" . $category->color . "))" : "#e5e7eb" }}20; color: {{ $category->color ?? "#6b7280" }}">
                                {{ $category->color ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $category->order }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="{{ $category->is_active ? 'text-green-600 bg-green-100 dark:bg-green-900/30' : 'text-gray-500 bg-gray-100 dark:bg-gray-700' }} px-2.5 py-1 rounded text-xs font-medium">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('medication-template-categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('medication-template-categories.destroy', $category) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this category?')" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No categories found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="lg:hidden space-y-4">
        @forelse($categories as $category)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $category->name }}</h3>
                    <input type="checkbox" name="ids[]" value="{{ $category->id }}" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600">
                </div>
                <div class="text-sm space-y-2 text-gray-600 dark:text-gray-300">
                    <div><strong>Color:</strong> <span class="font-mono">{{ $category->color ?? '—' }}</span></div>
                    <div><strong>Order:</strong> {{ $category->order }}</div>
                    <div><strong>Status:</strong> {{ $category->is_active ? 'Active' : 'Inactive' }}</div>
                </div>
                <div class="mt-4 flex gap-3">
                    <a href="{{ route('medication-template-categories.edit', $category) }}" class="flex-1 text-center py-2 bg-indigo-600 text-white rounded-lg text-sm">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('medication-template-categories.destroy', $category) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete?')" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-500">No categories found.</div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $categories->appends(request()->query())->links() }}
    </div>
</div>

<script>
    document.getElementById('select-all')?.addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulk();
    });
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.addEventListener('change', updateBulk));

    function updateBulk() {
        const checked = document.querySelectorAll('.row-checkbox:checked').length;
        const form = document.getElementById('bulk-delete-form');
        const ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value).join(',');
        document.getElementById('bulk-ids').value = ids;
        document.getElementById('selected-count').textContent = checked;
        form.classList.toggle('hidden', checked === 0);
    }
</script>
@endsection