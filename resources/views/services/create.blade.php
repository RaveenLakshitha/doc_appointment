{{-- resources/views/services/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add Service')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('services.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Services</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">Add Service</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Add New Service</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a new medical service with equipment and availability</p>
    </div>

    <form method="POST" action="{{ route('services.store') }}" class="space-y-8">
        @csrf

        <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex overflow-x-auto scrollbar-hide" aria-label="Tabs">
                    <button type="button" onclick="switchTab('basic')" id="tab-basic"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-gray-400 bg-gray-50 dark:bg-gray-700/50">
                        Basic Information
                    </button>
                    <button type="button" onclick="switchTab('equipment')" id="tab-equipment"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        Equipment
                    </button>
                    <button type="button" onclick="switchTab('availability')" id="tab-availability"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        Availability Slots
                    </button>
                    <button type="button" onclick="switchTab('details')" id="tab-details"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        Details
                    </button>
                </nav>
            </div>

            <div class="p-6 space-y-8">
                <!-- Basic Information -->
                <div id="content-basic" class="tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Department <span class="text-red-500">*</span></label>
                            <select name="department_id" required class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800">
                                <option value="">Select department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type <span class="text-red-500">*</span></label>
                            <select name="type" required class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800">
                                <option value="">Select type</option>
                                <option value="Diagnostic" {{ old('type') == 'Diagnostic' ? 'selected' : '' }}>Diagnostic</option>
                                <option value="Therapeutic" {{ old('type') == 'Therapeutic' ? 'selected' : '' }}>Therapeutic</option>
                                <option value="Consultation" {{ old('type') == 'Consultation' ? 'selected' : '' }}>Consultation</option>
                                <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (minutes) <span class="text-red-500">*</span></label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" required min="5" step="5" class="w-full px-3 py-2.5 border rounded-lg dark:bg-gray-800">
                            @error('duration_minutes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price ($) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" value="{{ old('price') }}" required min="0" step="0.01" class="w-full px-3 py-2.5 border rounded-lg dark:bg-gray-800">
                            @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Equipment Assignment -->
                <div id="content-equipment" class="tab-content hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Assign Equipment (Optional)</label>
                    <div class="space-y-3">
                        @foreach($equipment as $eq)
                            <label class="flex items-center">
                                <input type="checkbox" name="equipment[]" value="{{ $eq->id }}"
                                       class="h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $eq->name }} 
                                    <span class="text-xs text-gray-500">({{ $eq->status }})</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('equipment') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Availability Slots -->
                <div id="content-availability" class="tab-content hidden">
                    <div class="flex justify-between items-center mb-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Availability Slots</label>
                        <button type="button" id="add-slot" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                            + Add Slot
                        </button>
                    </div>

                    <div id="slots-container" class="space-y-4">
                        <div class="slot-row grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Day</label>
                                <select name="slots[0][day]" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                        <option value="{{ $day }}">{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Start Time</label>
                                <input type="time" name="slots[0][start_time]" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">End Time</label>
                                <input type="time" name="slots[0][end_time]" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div id="content-details" class="tab-content hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea name="description" rows="4" class="w-full px-3 py-2.5 border rounded-lg dark:bg-gray-800">{{ old('description') }}</textarea>
                    </div>
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Patient Preparation</label>
                        <textarea name="patient_preparation" rows="4" class="w-full px-3 py-2.5 border rounded-lg dark:bg-gray-800">{{ old('patient_preparation') }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="requires_insurance" value="1" {{ old('requires_insurance') ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 rounded">
                            <span class="ml-3 text-sm">Requires Insurance</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="requires_referral" value="1" {{ old('requires_referral') ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 rounded">
                            <span class="ml-3 text-sm">Requires Referral</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                Create Service
            </button>
            <a href="{{ route('services.index') }}" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium">
                Cancel
            </a>
        </div>
    </form>
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

let slotIndex = 1;
document.getElementById('add-slot').addEventListener('click', function () {
    const container = document.getElementById('slots-container');
    const template = `
        <div class="slot-row grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Day</label>
                <select name="slots[${slotIndex}][day]" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                        <option value="{{ $day }}">{{ $day }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Start Time</label>
                <input type="time" name="slots[${slotIndex}][start_time]" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
            </div>
            <div class="flex items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">End Time</label>
                    <input type="time" name="slots[${slotIndex}][end_time]" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                </div>
                <button type="button" onclick="this.closest('.slot-row').remove()" class="ml-3 text-red-600 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6"/></svg>
                </button>
            </div>
        </div>`;
    container.insertAdjacentHTML('beforeend', template);
    slotIndex++;
});
</script>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection