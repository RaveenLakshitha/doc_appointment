@extends('layouts.app')

@section('title', 'Create New Treatment')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('treatments.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Treatments
            </a>
            <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">Create New Treatment</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Create a reusable treatment template. Doctors can later assign it to themselves and set their own price.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form method="POST" action="{{ route('treatments.store') }}">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Treatment Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name') }}"
                               placeholder="e.g. Root Canal, Dental Cleaning, X-Ray"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Code (auto-generated)
                        </label>
                        <input type="text" id="code" value="{{ old('code', $next_code ?? 'TRT-001') }}"
                               disabled class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm sm:text-sm cursor-not-allowed">
                        <input type="hidden" name="code" value="{{ old('code', $next_code ?? 'TRT-001') }}">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            System will assign a unique code (e.g. TRT-001, TRT-002)
                        </p>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="active" id="active" value="1" checked
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                        <label for="active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            Active (visible for doctors to assign in their profiles)
                        </label>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('treatments.index') }}"
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                        Create Treatment
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6 text-sm text-gray-500 dark:text-gray-400">
            <p>Note: After creation, doctors can assign this treatment to themselves and set their own custom price in their profile settings.</p>
        </div>
    </div>
</div>
@endsection