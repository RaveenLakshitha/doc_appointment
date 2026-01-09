{{-- resources/views/medicine_templates/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Medicine Template')

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('medicine-templates.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Medicine Templates</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">Create Template</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Create New Medicine Template</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Define a reusable medication template for common conditions</p>
    </div>

    <form method="POST" action="{{ route('medicine-templates.store') }}" class="space-y-8">
        @csrf

        <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex overflow-x-auto scrollbar-hide" aria-label="Tabs">
                    <button type="button" onclick="switchTab('details')" id="tab-details"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-gray-400 bg-gray-50 dark:bg-gray-700/50">
                        Template Details
                    </button>
                    <button type="button" onclick="switchTab('medications')" id="tab-medications"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        Medications
                    </button>
                </nav>
            </div>

            <div class="p-6 space-y-8">
                <!-- Template Details -->
                <div id="content-details" class="tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Template Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Category
                            </label>
                            <input type="text" name="category" value="{{ old('category') }}" placeholder="e.g., Cardiology, Endocrinology"
                                   class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800">
                            @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea name="description" rows="4" class="w-full px-3 py-2.5 border rounded-lg dark:bg-gray-800"
                                  placeholder="Brief description of when to use this template...">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Medications -->
                <div id="content-medications" class="tab-content hidden">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Medications</h3>
                        <button type="button" id="add-medication" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                            + Add Medication
                        </button>
                    </div>

                    <div id="medications-container" class="space-y-4">
                        <!-- Default row -->
                        <div class="medication-row grid grid-cols-1 md:grid-cols-12 gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Medication Name</label>
                                <input type="text" name="medications[0][name]" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Dosage</label>
                                <input type="text" name="medications[0][dosage]" required placeholder="e.g. 500mg" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Route</label>
                                <select name="medications[0][route]" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                                    <option value="Oral">Oral</option>
                                    <option value="IV">IV</option>
                                    <option value="IM">IM</option>
                                    <option value="Topical">Topical</option>
                                    <option value="Sublingual">Sublingual</option>
                                    <option value="Inhalation">Inhalation</option>
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Frequency</label>
                                <input type="text" name="medications[0][frequency]" required placeholder="e.g. Twice daily" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                            </div>
                            <div class="md:col-span-1 flex items-end">
                                <button type="button" onclick="this.closest('.medication-row').remove()" class="text-red-600 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="md:col-span-12">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Instructions (Optional)</label>
                                <textarea name="medications[0][instructions]" rows="2" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700"
                                          placeholder="e.g. Take with food, Avoid alcohol..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                Create Template
            </button>
            <a href="{{ route('medicine-templates.index') }}" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
let medicationIndex = 1;

document.getElementById('add-medication').addEventListener('click', function () {
    const container = document.getElementById('medications-container');
    const template = `
        <div class="medication-row grid grid-cols-1 md:grid-cols-12 gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
            <div class="md:col-span-3">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Medication Name</label>
                <input type="text" name="medications[${medicationIndex}][name]" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Dosage</label>
                <input type="text" name="medications[${medicationIndex}][dosage]" required placeholder="e.g. 500mg" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Route</label>
                <select name="medications[${medicationIndex}][route]" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                    <option value="Oral">Oral</option>
                    <option value="IV">IV</option>
                    <option value="IM">IM</option>
                    <option value="Topical">Topical</option>
                    <option value="Sublingual">Sublingual</option>
                    <option value="Inhalation">Inhalation</option>
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Frequency</label>
                <input type="text" name="medications[${medicationIndex}][frequency]" required placeholder="e.g. Twice daily" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
            </div>
            <div class="md:col-span-1 flex items-end">
                <button type="button" onclick="this.closest('.medication-row').remove()" class="text-red-600 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6"/>
                    </svg>
                </button>
            </div>
            <div class="md:col-span-12">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Instructions (Optional)</label>
                <textarea name="medications[${medicationIndex}][instructions]" rows="2" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700"
                          placeholder="e.g. Take with food, Avoid alcohol..."></textarea>
            </div>
        </div>`;
    container.insertAdjacentHTML('beforeend', template);
    medicationIndex++;
});

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