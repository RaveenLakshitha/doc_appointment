{{-- resources/views/medicine_templates/show.blade.php --}}
@extends('layouts.app')

@section('title', $medicineTemplate->name)

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('medicine-templates.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Medicine Templates</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">{{ $medicineTemplate->name }}</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $medicineTemplate->name }}</h1>
        @if($medicineTemplate->category)
            <span class="inline-block mt-2 px-3 py-1 text-sm font-medium text-indigo-800 dark:text-indigo-200 bg-indigo-100 dark:bg-indigo-900/30 rounded-full">
                {{ $medicineTemplate->category }}
            </span>
        @endif
        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 max-w-4xl">
            {{ $medicineTemplate->description ?? 'No description provided.' }}
        </p>
    </div>

    <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex overflow-x-auto scrollbar-hide" aria-label="Tabs">
                <button type="button" onclick="switchTab('medications')" id="tab-medications"
                        class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-gray-400 bg-gray-50 dark:bg-gray-700/50">
                    Medications ({{ $medicineTemplate->medications->count() }})
                </button>
                <button type="button" onclick="switchTab('details')" id="tab-details"
                        class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                    Template Details
                </button>
            </nav>
        </div>

        <div class="p-6 space-y-8">
            <!-- Medications Tab -->
            <div id="content-medications" class="tab-content">
                @if($medicineTemplate->medications->count() > 0)
                    <div class="grid grid-cols-1 gap-6">
                        @foreach($medicineTemplate->medications as $medication)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 bg-gray-50 dark:bg-gray-800/50">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $medication->name }}
                                        </h4>
                                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                            <div>
                                                <span class="font-medium text-gray-600 dark:text-gray-400">Dosage:</span>
                                                <span class="ml-2 text-gray-900 dark:text-gray-200">{{ $medication->dosage }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-600 dark:text-gray-400">Route:</span>
                                                <span class="ml-2 text-gray-900 dark:text-gray-200">{{ $medication->route }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-600 dark:text-gray-400">Frequency:</span>
                                                <span class="ml-2 text-gray-900 dark:text-gray-200">{{ $medication->frequency }}</span>
                                            </div>
                                            @if($medication->instructions)
                                                <div class="sm:col-span-2 lg:col-span-4 mt-3">
                                                    <span class="font-medium text-gray-600 dark:text-gray-400">Instructions:</span>
                                                    <p class="mt-1 text-gray-900 dark:text-gray-200">{{ $medication->instructions }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">No medications added to this template yet.</p>
                    </div>
                @endif
            </div>

            <!-- Template Details Tab -->
            <div id="content-details" class="tab-content hidden">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Template Name</label>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $medicineTemplate->name }}</p>
                    </div>

                    @if($medicineTemplate->category)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $medicineTemplate->category }}</p>
                        </div>
                    @endif

                    @if($medicineTemplate->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <p class="mt-1 text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $medicineTemplate->description }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Medications</label>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $medicineTemplate->medications->count() }}</p>
                    </div>

                    <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created At</label>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        {{ $medicineTemplate->created_at ? $medicineTemplate->created_at->format('M d, Y \a\t h:i A') : 'N/A' }}
                    </p>
                </div>

                @if($medicineTemplate->updated_at && $medicineTemplate->updated_at->notEqualTo($medicineTemplate->created_at))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Updated</label>
                        <p class="mt-1 text-gray-600 dark:text-gray-400">
                            {{ $medicineTemplate->updated_at->format('M d, Y \a\t h:i A') }}
                        </p>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <a href="{{ route('medicine-templates.edit', $medicineTemplate) }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Template
        </a>
        <a href="{{ route('medicine-templates.index') }}"
           class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium">
            Back to List
        </a>
    </div>
</div>

<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(b => {
        b.classList.remove('text-gray-900','dark:text-white','border-b-2','border-gray-900','dark:border-gray-400','bg-gray-50','dark:bg-gray-700/50');
        b.classList.add('text-gray-500','dark:text-gray-400','hover:text-gray-700','dark:hover:text-gray-300','hover:bg-gray-50','dark:hover:bg-gray-700/30');
    });
    document.getElementById('content-' + tabName).classList.remove('hidden');
    document.getElementById('tab-' + tabName).classList.add('text-gray-900','dark:text-white','border-b-2','border-gray-900','dark:border-gray-400','bg-gray-50','dark:bg-gray-700/50');
    document.getElementById('tab-' + tabName).classList.remove('text-gray-500','dark:text-gray-400');
}
</script>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection