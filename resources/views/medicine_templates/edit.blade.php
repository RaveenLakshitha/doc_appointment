@extends('layouts.app')

@section('title', __('file.edit_medicine_template'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" mb-8">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                <a href="{{ route('medicine-templates.index') }}"
                    class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    {{ __('file.medicine_templates') }}
                </a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white">{{ __('file.edit_template') }}</span>
            </div>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('file.edit_medicine_template') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('file.update_medicine_template_description') }}
            </p>
        </div>

        <form method="POST" action="{{ route('medicine-templates.update', $medicineTemplate) }}" class="space-y-8">
            @csrf
            @method('PUT')

            <div
                class="bg-white dark:bg-transparent rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 space-y-10">

                    <div class="space-y-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white pb-2 border-b border-gray-200 dark:border-gray-700">
                            {{ __('file.template_details') }}
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.template_name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $medicineTemplate->name) }}" required
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                @error('name')
                                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('file.category') }}
                                </label>
                                <input type="text" name="category"
                                    value="{{ old('category', $medicineTemplate->category) }}"
                                    placeholder="{{ __('file.category_placeholder') }}"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                                @error('category')
                                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('file.description_purpose') }}
                            </label>
                            <textarea name="description" rows="3"
                                class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow resize-none"
                                placeholder="{{ __('file.description_placeholder') }}">{{ old('description', $medicineTemplate->description) }}</textarea>
                            @error('description')
                                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('file.medications') }}
                            </h3>
                            <button type="button" id="add-medication"
                                class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline">
                                {{ __('file.add_medication') }}
                            </button>
                        </div>

                        <div id="medications-container" class="space-y-4"
                            data-medicines="{{ json_encode($medicines ?? []) }}">

                            @foreach($medicineTemplate->medications as $index => $medication)
                                <div
                                    class="medication-row grid grid-cols-1 md:grid-cols-12 gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                            {{ __('file.medication') }} *
                                        </label>
                                        <select name="medications[{{ $index }}][inventory_item_id]" required
                                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white medicine-select"
                                            data-index="{{ $index }}">
                                            <option value="">{{ __('file.select_medicine') }}</option>
                                            @foreach($medicines as $medicine)
                                                <option value="{{ $medicine['id'] }}" {{ old("medications.$index.inventory_item_id", $medication->inventory_item_id) == $medicine['id'] ? 'selected' : '' }}>
                                                    {{ $medicine['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="medications[{{ $index }}][name]" class="hidden-name"
                                            value="{{ old("medications.$index.name", $medication->name) }}">
                                        @error("medications.$index.inventory_item_id")
                                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                            {{ __('file.dosage') }} *
                                        </label>
                                        <input type="text" name="medications[{{ $index }}][dosage]" required
                                            value="{{ old("medications.$index.dosage", $medication->dosage) }}"
                                            placeholder="{{ __('file.dosage_placeholder') }}"
                                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white dosage-input">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                            {{ __('file.route') }}
                                        </label>
                                        <select name="medications[{{ $index }}][route]"
                                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white">
                                            @foreach($routes as $value => $label)
                                                <option value="{{ $value }}" {{ old("medications.$index.route", $medication->route) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                            {{ __('file.frequency') }} *
                                        </label>
                                        <input type="text" name="medications[{{ $index }}][frequency]" required
                                            value="{{ old("medications.$index.frequency", $medication->frequency) }}"
                                            placeholder="{{ __('file.frequency_placeholder') }}"
                                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white">
                                    </div>

                                    <div class="md:col-span-1 flex items-center justify-center">
                                        <button type="button" onclick="this.closest('.medication-row').remove()"
                                            class="text-red-600 hover:text-red-700 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="md:col-span-12">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                            {{ __('file.instructions_optional') }}
                                        </label>
                                        <textarea name="medications[{{ $index }}][instructions]" rows="2"
                                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white resize-none"
                                            placeholder="{{ __('file.instructions_placeholder') }}">{{ old("medications.$index.instructions", $medication->instructions) }}</textarea>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2 justify-end">
                <a href="{{ route('medicine-templates.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ __('file.cancel') }}
                </a>

                <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 border border-gray-300 dark:border-gray-600 dark:bg-white dark:text-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('file.update_template') }}
                </button>
            </div>
        </form>
    </div>

    <script>
        let medicationIndex = {{ $medicineTemplate->medications->count() }};

        const medicines = JSON.parse(document.getElementById('medications-container')?.dataset.medicines || '[]');
        const availableRoutes = @json($routes ?? []);

        const medicineOptionsHtml = medicines
            .map(m => `<option value="${m.id}">${m.name}</option>`)
            .join('');

        const routeOptionsHtml = Object.entries(availableRoutes)
            .map(([value, label]) => `<option value="${value}">${label}</option>`)
            .join('');

        document.getElementById('add-medication')?.addEventListener('click', function () {
            const container = document.getElementById('medications-container');

            const template = `
                        <div class="medication-row grid grid-cols-1 md:grid-cols-12 gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.medication') }} *</label>
                                <select name="medications[\${medicationIndex}][inventory_item_id]" required 
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white medicine-select"
                                        data-index="\${medicationIndex}">
                                    <option value="">{{ __('file.select_medicine') }}</option>
                                    ${medicineOptionsHtml}
                                </select>
                                <input type="hidden" name="medications[\${medicationIndex}][name]" class="hidden-name">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.dosage') }} *</label>
                                <input type="text" name="medications[\${medicationIndex}][dosage]" required 
                                       placeholder="{{ __('file.dosage_placeholder') }}" 
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white dosage-input">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.route') }}</label>
                                <select name="medications[\${medicationIndex}][route]" 
                                        class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white">
                                    ${routeOptionsHtml}
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.frequency') }} *</label>
                                <input type="text" name="medications[\${medicationIndex}][frequency]" required 
                                       placeholder="{{ __('file.frequency_placeholder') }}" 
                                       class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white">
                            </div>

                            <div class="md:col-span-1 flex items-center justify-center">
                                <button type="button" onclick="this.closest('.medication-row').remove()" 
                                        class="text-red-600 hover:text-red-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="md:col-span-12">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('file.instructions_optional') }}</label>
                                <textarea name="medications[\${medicationIndex}][instructions]" rows="2"
                                          class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white resize-none"
                                          placeholder="{{ __('file.instructions_placeholder') }}"></textarea>
                            </div>
                        </div>`;

            container.insertAdjacentHTML('beforeend', template);
            medicationIndex++;
        });

        document.addEventListener('change', function (e) {
            if (!e.target.matches('.medicine-select')) return;

            const select = e.target;
            const row = select.closest('.medication-row');
            const itemId = select.value;
            const hiddenName = row.querySelector('.hidden-name');
            const dosageInput = row.querySelector('.dosage-input');

            if (itemId && hiddenName && dosageInput) {
                const selected = medicines.find(m => String(m.id) === String(itemId));
                if (selected) {
                    hiddenName.value = selected.name;
                    dosageInput.value = selected.dosage || '';
                } else {
                    hiddenName.value = '';
                    dosageInput.value = '';
                }
            }
        });
    </script>
@endsection