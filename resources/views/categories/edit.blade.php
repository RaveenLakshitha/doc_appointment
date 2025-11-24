{{-- resources/views/categories/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Category')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('categories.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Categories</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">Edit Category</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Edit Category</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update category details</p>
    </div>

    <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-8">
        @csrf @method('PUT')

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                               class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow"
                               placeholder="e.g. Surgical Instruments">
                        @error('name') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Parent Category</label>
                        <select name="parent_id"
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow">
                            <option value="">None (Top Level)</option>
                            @foreach($parents as $parent)
                                @if($parent->id !== $category->id)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('parent_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea name="description" rows="4"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-shadow resize-none"
                                  placeholder="Brief description of this category...">{{ old('description', $category->description) }}</textarea>
                        @error('description') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Update Category
            </button>
            <a href="{{ route('categories.index') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection