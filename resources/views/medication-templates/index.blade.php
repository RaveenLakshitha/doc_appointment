{{-- resources/views/medication-templates/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Medication Templates')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                Medication Templates
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Manage reusable prescription templates
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <form method="GET" class="flex gap-1 flex-1 sm:flex-initial">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search templates..."
                       class="w-full px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded 
                              focus:ring-1 focus:ring-gray-900 dark:focus:ring-gray-500 
                              focus:border-transparent dark:bg-transparent dark:text-white pr-10">

                <button type="submit"
                        class="px-2.5 py-1.5 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded 
                               hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                @if(request('search'))
                    <a href="{{ route('medication-templates.index') }}"
                       class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded 
                              hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </form>

            <a href="{{ route('medication-templates.create') }}"
               class="px-4 py-1.5 bg-gray-900 dark:bg-gray-700 dark:bg-transparent border border-gray-300 dark:border-gray-200 
                      text-white text-sm font-medium rounded hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors 
                      flex items-center justify-center gap-1 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">New Template</span>
                <span class="sm:hidden">Add</span>
            </a>
        </div>
    </div>

    <!-- Bulk Delete Bar -->
    <form method="POST" action="{{ route('medication-templates.bulkDelete') }}" id="bulk-delete-form" class="hidden mb-4">
        @csrf @method('DELETE')
        <input type="hidden" name="ids" id="bulk-ids">
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded p-3">
            <div class="flex items-center justify-between gap-3">
                <span class="text-sm text-red-800 dark:text-red-300">
                    <span id="selected-count">0</span> template(s) selected
                </span>
                <button type="submit" onclick="return confirm('Move selected templates to trash?')"
                        class="px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Move to Trash
                </button>
            </div>
        </div>
    </form>

    <!-- Mobile Cards -->
    <div class="space-y-3 sm:hidden">
        @forelse($templates as $template)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-4">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" value="{{ $template->id }}" class="row-checkbox w-4 h-4 rounded border-gray-300">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $template->name }}</div>
                            @if($template->description)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($template->description, 60) }}</div>
                            @endif
                        </div>
                    </div>

                    @if($template->category)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              style="background-color: {{ $template->category->color ?? '#6b7280' }}20; color: {{ $template->category->color ?? '#374151' }}">
                            {{ $template->category->name }}
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs">Medications</div>
                        <div class="font-medium">{{ $template->medications_count ?? 0 }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs">Last Used</div>
                        <div class="font-medium">{{ $template->last_used_at?->diffForHumans() ?? 'Never' }}</div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('medication-templates.show', $template) }}" class="text-gray-600 hover:text-gray-900 dark:hover:text-white" title="View">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    <a href="{{ route('medication-templates.edit', $template) }}" class="text-gray-600 hover:text-gray-900 dark:hover:text-white" title="Edit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('medication-templates.destroy', $template) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Move to trash?')"
                                class="text-gray-600 hover:text-red-600 dark:hover:text-red-500" title="Delete">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="mt-4 text-gray-500 dark:text-gray-400">No templates found.</p>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table -->
    <div class="hidden sm:block bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left"><input type="checkbox" id="select-all" class="w-4 h-4 rounded"></th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Medications</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Last Used</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($templates as $template)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                        <td class="px-4 py-3">
                            <input type="checkbox" value="{{ $template->id }}" class="row-checkbox w-4 h-4 rounded">
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                            {{ $template->name }}
                            @if($template->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($template->description, 60) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($template->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      style="background-color: {{ $template->category->color ?? '#6b7280' }}20; color: {{ $template->category->color ?? '#374151' }}">
                                    {{ $template->category->name }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $template->medications_count ?? 0 }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $template->last_used_at?->diffForHumans() ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('medication-templates.show', $template) }}" class="text-gray-600 hover:text-gray-900 dark:hover:text-white" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('medication-templates.edit', $template) }}" class="text-gray-600 hover:text-gray-900 dark:hover:text-white" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('medication-templates.destroy', $template) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Move to trash?')"
                                            class="text-gray-600 hover:text-red-600 dark:hover:text-red-500" title="Delete">
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
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            No templates found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $templates->appends(request()->query())->links() }}
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