@extends('layouts.app')

@section('title', __('file.dropdown_management'))

@section('content')
    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ __('file.dropdown_management') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('file.manage_all_dropdown_options') }}
                </p>
            </div>

            <button id="create-drawer-toggle" type="button"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="hidden sm:inline">{{ __('file.add_option') }}</span>
                <span class="sm:hidden">Add</span>
            </button>
        </div>

        <!-- Drawer for Add / Edit Option -->
        <div id="option-backdrop"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300 opacity-0"></div>

        <div id="option-drawer"
            class="fixed z-50 bg-white dark:bg-gray-800 shadow-2xl transition-transform duration-300 ease-in-out
                            bottom-0 left-0 right-0 max-h-[85vh] rounded-t-2xl translate-y-full
                            md:top-0 md:right-0 md:bottom-auto md:left-auto md:h-full md:w-96 md:max-h-none md:rounded-none md:rounded-l-lg md:translate-y-0 md:translate-x-full">

            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="drawer-title">
                    {{ __('file.add_new_option') }}
                </h3>
                <button type="button" id="close-option-drawer"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 p-1.5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(85vh-140px)] md:max-h-[calc(100vh-140px)]">
                <form id="option-form" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    <input type="hidden" name="id" id="option-id">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.type') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="option-type" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="" disabled selected hidden>{{ __('file.select_type') }}</option>
                            @foreach($groupedOptions as $type => $options)
                                <option value="{{ $type }}" {{ old('type') ? ($old('type') == $type ? 'selected' : '') : ($loop->first ? 'selected' : '') }}>
                                    {{ __('file.' . $type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('type') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="option-name" required
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                            placeholder="e.g., Attending Physician">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.order') }}
                        </label>
                        <input type="number" name="order" id="option-order" min="0"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-transparent dark:text-white transition-shadow"
                            value="0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('file.status') }}
                        </label>
                        <select name="status" id="option-status"
                            class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-500 focus:border-transparent dark:bg-gray-900 dark:text-white transition-shadow">
                            <option value="1">{{ __('file.active') }}</option>
                            <option value="0">{{ __('file.inactive') }}</option>
                        </select>
                    </div>
                </form>
            </div>

            <div
                class="bottom-0 left-0 right-0 flex gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                <button type="button" id="cancel-option"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    {{ __('file.cancel') }}
                </button>
                <button type="submit" form="option-form" id="save-option"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm">
                    {{ __('file.save') }}
                </button>
            </div>
        </div>

        <!-- Table -->
        <div
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.type') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('file.options') }}
                            </th>
                            <th
                                class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider no-export">
                                {{ __('file.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($groupedOptions as $type => $options)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td
                                    class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('file.' . $type) }}
                                </td>
                                <td class="px-4 sm:px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    @if($options->isNotEmpty())
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($options as $option)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                    {{ $option->name }}
                                                    @if(!$option->status)
                                                        <span class="text-red-500 dark:text-red-400">(inactive)</span>
                                                    @endif
                                                    <button type="button"
                                                        class="edit-option ml-1 text-blue-600 dark:text-blue-300 hover:text-blue-800"
                                                        data-id="{{ $option->id }}" data-type="{{ $option->type }}"
                                                        data-name="{{ $option->name }}" data-order="{{ $option->order }}"
                                                        data-status="{{ $option->status }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </button>
                                                    <form method="POST" action="{{ route('dropdowns.destroy', $option->id) }}"
                                                        class="inline ml-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            onclick="return confirm('{{ __('file.confirm_delete_option') }}')"
                                                            class="text-red-600 dark:text-red-400 hover:text-red-800">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500 italic">No options</span>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-4 text-right whitespace-nowrap">
                                    <button type="button"
                                        class="add-option p-1.5 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                        data-type="{{ $type }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('file.no_dropdown_types_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const backdrop = document.getElementById('option-backdrop');
                const drawer = document.getElementById('option-drawer');
                const closeBtn = document.getElementById('close-option-drawer');
                const cancelBtn = document.getElementById('cancel-option');
                const createToggle = document.getElementById('create-drawer-toggle');
                const form = document.getElementById('option-form');
                const title = document.getElementById('drawer-title');
                const methodInput = document.getElementById('form-method');

                // Global Add button
                createToggle.addEventListener('click', () => {
                    form.action = '{{ route("dropdowns.store") }}';
                    methodInput.value = 'POST';
                    document.getElementById('option-id').value = '';
                    document.getElementById('option-type').value = '';
                    document.getElementById('option-name').value = '';
                    document.getElementById('option-order').value = 0;
                    document.getElementById('option-status').value = 1;
                    title.textContent = '{{ __('file.add_new_option') }}';
                    openDrawer();
                });

                // Add option to specific type
                document.querySelectorAll('.add-option').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const type = e.target.closest('button').dataset.type;
                        form.action = '{{ route("dropdowns.store") }}';
                        methodInput.value = 'POST';
                        document.getElementById('option-id').value = '';
                        document.getElementById('option-type').value = type;
                        document.getElementById('option-name').value = '';
                        document.getElementById('option-order').value = 0;
                        document.getElementById('option-status').value = 1;
                        title.textContent = `Add option to "${type}"`;
                        openDrawer();
                    });
                });

                // Edit option
                document.querySelectorAll('.edit-option').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const data = e.target.closest('button').dataset;
                        form.action = '{{ route("dropdowns.update", ":id") }}'.replace(':id', data.id);
                        methodInput.value = 'PUT';
                        document.getElementById('option-id').value = data.id;
                        document.getElementById('option-type').value = data.type;
                        document.getElementById('option-name').value = data.name;
                        document.getElementById('option-order').value = data.order;
                        document.getElementById('option-status').value = data.status;
                        title.textContent = `Edit "${data.name}"`;
                        openDrawer();
                    });
                });

                function openDrawer() {
                    backdrop.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => {
                        backdrop.classList.add('opacity-100');
                        backdrop.classList.remove('opacity-0');
                        if (window.innerWidth >= 768) drawer.classList.remove('md:translate-x-full');
                        else drawer.classList.remove('translate-y-full');
                    }, 10);
                }

                function closeDrawer() {
                    backdrop.classList.remove('opacity-100');
                    backdrop.classList.add('opacity-0');
                    if (window.innerWidth >= 768) drawer.classList.add('md:translate-x-full');
                    else drawer.classList.add('translate-y-full');
                    setTimeout(() => {
                        backdrop.classList.add('hidden');
                        document.body.style.overflow = '';
                    }, 300);
                }

                closeBtn.addEventListener('click', closeDrawer);
                cancelBtn.addEventListener('click', closeDrawer);
                backdrop.addEventListener('click', closeDrawer);
                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape' && !backdrop.classList.contains('hidden')) closeDrawer();
                });
            });
        </script>
    @endpush
@endsection