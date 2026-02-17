{{-- resources/views/prescriptions/create.blade.php --}}
@extends('layouts.app')

@section('title', __('file.create_prescription_title'))

@section('content')
<div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('prescriptions.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">{{ __('file.prescriptions') }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 dark:text-white">{{ __('file.create_prescription') }}</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.create_new_prescription') }}</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('file.create_prescription_description') }}</p>
    </div>

    <form method="POST" action="{{ route('prescriptions.store') }}" class="space-y-8">
        @csrf

        <div class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex overflow-x-auto scrollbar-hide" aria-label="Tabs">
                    <button type="button" onclick="switchTab('patient')" id="tab-patient"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-gray-400 bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="hidden sm:inline">{{ __('file.patient_details') }}</span>
                            <span class="sm:hidden">{{ __('file.details') }}</span>
                        </div>
                    </button>
                    <button type="button" onclick="switchTab('medications')" id="tab-medications"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-8 0h6"/>
                            </svg>
                            <span class="hidden sm:inline">{{ __('file.medications') }}</span>
                            <span class="sm:hidden">{{ __('file.meds') }}</span>
                        </div>
                    </button>
                    <button type="button" onclick="switchTab('notes')" id="tab-notes"
                            class="tab-button flex-1 min-w-max px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="hidden sm:inline">{{ __('file.additional_notes') }}</span>
                            <span class="sm:hidden">{{ __('file.notes') }}</span>
                        </div>
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <div id="content-patient" class="tab-content">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.patient') }} <span class="text-red-500">*</span></label>
                                <select name="patient_id" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                    <option value="">{{ __('file.select_patient') }}</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->getFullNameAttribute() }} (MRN: {{ $patient->medical_record_number ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('patient_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.prescription_date') }} <span class="text-red-500">*</span></label>
                                <input type="date" name="prescription_date" value="{{ old('prescription_date', today()->format('Y-m-d')) }}" required
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white [color-scheme:light] dark:[color-scheme:dark] transition-shadow">
                                @error('prescription_date') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.prescription_type') }} <span class="text-red-500">*</span></label>
                                <select name="type" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                    <option value="">{{ __('file.select_type') }}</option>
                                    <option value="Standard" {{ old('type') == 'Standard' ? 'selected' : '' }}>{{ __('file.standard') }}</option>
                                    <option value="Emergency" {{ old('type') == 'Emergency' ? 'selected' : '' }}>{{ __('file.emergency') }}</option>
                                    <option value="Chronic" {{ old('type') == 'Chronic' ? 'selected' : '' }}>{{ __('file.chronic') }}</option>
                                    <option value="Follow-up" {{ old('type') == 'Follow-up' ? 'selected' : '' }}>{{ __('file.follow_up') }}</option>
                                </select>
                                @error('type') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.use_template') }}</label>
                                <select id="template-select" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                    <option value="">{{ __('file.none') }}</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }} ({{ $template->category ?? __('file.general') }})</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="medicine_template_id" id="selected-template-id">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.diagnosis_reason') }}</label>
                            <textarea name="diagnosis" rows="3" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none">{{ old('diagnosis') }}</textarea>
                            @error('diagnosis') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div id="content-medications" class="tab-content hidden">
                    <div class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('file.medications') }}</h3>
                            <button type="button" id="add-medication" class="text-sm font-medium text-gray-900 dark:text-white hover:underline">
                                {{ __('file.add_medication') }}
                            </button>
                        </div>

                        <div id="medications-container" class="space-y-4">
                            <div class="medication-row grid grid-cols-1 md:grid-cols-12 gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.medication_name') }}</label>
                                    <input type="text" name="medications[0][name]" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.dosage') }}</label>
                                    <input type="text" name="medications[0][dosage]" required placeholder="{{ __('file.dosage_ph') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.route') }}</label>
                                    <select name="medications[0][route]" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                        <option value="Oral">{{ __('file.oral') }}</option>
                                        <option value="IV">{{ __('file.iv') }}</option>
                                        <option value="IM">{{ __('file.im') }}</option>
                                        <option value="Topical">{{ __('file.topical') }}</option>
                                        <option value="Sublingual">{{ __('file.sublingual') }}</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.frequency') }}</label>
                                    <input type="text" name="medications[0][frequency]" required placeholder="{{ __('file.frequency_ph') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.duration_days') }}</label>
                                    <input type="number" name="medications[0][duration_days]" min="1" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                </div>
                                <div class="md:col-span-1 flex justify-center">
                                    <button type="button" onclick="this.closest('.medication-row').remove()" class="text-red-600 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="content-notes" class="tab-content hidden">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('file.additional_instructions') }}</label>
                            <textarea name="notes" rows="5" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                      placeholder="{{ __('file.notes_ph') }}">{{ old('notes') }}</textarea>
                            @error('notes') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 border border-gray-300 dark:border-gray-600 dark:bg-white dark:text-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('file.create_prescription') }}
            </button>
            <a href="{{ route('prescriptions.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ __('file.cancel') }}
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
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.medication_name') }}</label>
                <input type="text" name="medications[${medicationIndex}][name]" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.dosage') }}</label>
                <input type="text" name="medications[${medicationIndex}][dosage]" required placeholder="{{ __('file.dosage_ph') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.route') }}</label>
                <select name="medications[${medicationIndex}][route]" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                    <option value="Oral">{{ __('file.oral') }}</option>
                    <option value="IV">{{ __('file.iv') }}</option>
                    <option value="IM">{{ __('file.im') }}</option>
                    <option value="Topical">{{ __('file.topical') }}</option>
                    <option value="Sublingual">{{ __('file.sublingual') }}</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.frequency') }}</label>
                <input type="text" name="medications[${medicationIndex}][frequency]" required placeholder="{{ __('file.frequency_ph') }}" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.duration_days') }}</label>
                <input type="number" name="medications[${medicationIndex}][duration_days]" min="1" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
            </div>
            <div class="md:col-span-1 flex items-end justify-center">
                <button type="button" onclick="this.closest('.medication-row').remove()" class="text-red-600 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6"/>
                    </svg>
                </button>
            </div>
        </div>`;
    container.insertAdjacentHTML('beforeend', template);
    medicationIndex++;
});

document.getElementById('template-select').addEventListener('change', function () {
    const templateId = this.value;
    document.getElementById('selected-template-id').value = templateId || '';
    const container = document.getElementById('medications-container');

    if (!templateId) {
        container.innerHTML = `<div class="medication-row grid grid-cols-1 md:grid-cols-12 gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">...</div>`;
        return;
    }

    fetch(`{{ route('medicine-templates.medications', ':id') }}`.replace(':id', templateId))
        .then(response => response.ok ? response.json() : Promise.reject())
        .then(meds => {
            container.innerHTML = '';
            meds.forEach((med, index) => {
                const row = `
                    <div class="medication-row grid grid-cols-1 md:grid-cols-12 gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.medication_name') }}</label>
                            <input type="text" name="medications[${index}][name]" value="${med.name}" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.dosage') }}</label>
                            <input type="text" name="medications[${index}][dosage]" value="${med.dosage || ''}" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.route') }}</label>
                            <select name="medications[${index}][route]" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                                <option value="Oral" ${med.route === 'Oral' ? 'selected' : ''}>{{ __('file.oral') }}</option>
                                <option value="IV" ${med.route === 'IV' ? 'selected' : ''}>{{ __('file.iv') }}</option>
                                <option value="IM" ${med.route === 'IM' ? 'selected' : ''}>{{ __('file.im') }}</option>
                                <option value="Topical" ${med.route === 'Topical' ? 'selected' : ''}>{{ __('file.topical') }}</option>
                                <option value="Sublingual" ${med.route === 'Sublingual' ? 'selected' : ''}>{{ __('file.sublingual') }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.frequency') }}</label>
                            <input type="text" name="medications[${index}][frequency]" value="${med.frequency || ''}" required class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.duration_days') }}</label>
                            <input type="number" name="medications[${index}][duration_days]" value="${med.duration_days || ''}" min="1" class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow">
                        </div>
                        <div class="md:col-span-1 flex items-end justify-center">
                            <button type="button" onclick="this.closest('.medication-row').remove()" class="text-red-600 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6"/>
                                </svg>
                            </button>
                        </div>
                    </div>`;
                container.insertAdjacentHTML('beforeend', row);
            });
            medicationIndex = meds.length;
        })
        .catch(() => {
            alert('{{ __('file.template_load_error') }}');
        });
});

function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(b => {
        b.classList.remove('text-gray-900','dark:text-white','border-b-2','border-gray-900','dark:border-gray-400','bg-gray-50','dark:bg-gray-700/50');
        b.classList.add('text-gray-500','dark:text-gray-400','hover:text-gray-700','dark:hover:text-gray-300','hover:bg-gray-50','dark:hover:bg-gray-700/30');
    });
    document.getElementById('content-' + tabName).classList.remove('hidden');
    const btn = document.getElementById('tab-' + tabName);
    btn.classList.add('text-gray-900','dark:text-white','border-b-2','border-gray-900','dark:border-gray-400','bg-gray-50','dark:bg-gray-700/50');
    btn.classList.remove('text-gray-500','dark:text-gray-400');
}
</script>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection