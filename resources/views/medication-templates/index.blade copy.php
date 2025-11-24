@extends('layouts.app')

@section('title', 'Medication Templates')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="flex flex-col gap-8">

        <!-- Categories Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Categories</h3>
                <button onclick="openCategoryDrawer()" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors">+ Add Category</button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                            <th class="bunny class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Color</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Templates</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr class="{{ !request('category') ? 'bg-gray-50 dark:bg-gray-900/50' : '' }} hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                <a href="{{ route('medication-templates.index') }}" class="hover:underline">All Templates</a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">-</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ \App\Models\MedicationTemplate::count() }}</td>
                            <td class="px-6 py-4 text-right text-sm"></td>
                        </tr>
                        @foreach($categories as $cat)
                            <tr class="{{ request('category') == $cat->id ? 'bg-gray-50 dark:bg-gray-900/50' : '' }} hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    <a href="{{ route('medication-templates.index', ['category' => $cat->id]) }}" class="flex items-center gap-2 hover:underline">
                                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $cat->color ?? '#6b7280' }}"></span>
                                        {{ $cat->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="w-4 h-4 rounded border" style="background-color: {{ $cat->color ?? '#6b7280' }}"></span>
                                        {{ $cat->color ?? 'gray-600' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $cat->templates()->count() }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <button type="button" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-sm font-medium">Edit</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                        Medication Templates
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Manage reusable prescription templates
                    </p>
                </div>
                <button onclick="openTemplateDrawer()"
                        class="px-5 py-2.5 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-md hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Template
                </button>
            </div>

            <form method="POST" action="{{ route('medication-templates.bulkDelete') }}" id="bulk-delete-form" class="hidden mb-6">
                @csrf @method('DELETE')
                <input type="hidden" name="ids" id="bulk-ids">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 flex items-center justify-between gap-4">
                    <span class="text-sm font-medium text-red-800 dark:text-red-300">
                        <span id="selected-count">0</span> template(s) selected
                    </span>
                    <button type="submit" onclick="return confirm('Move selected templates to trash?')"
                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Move to Trash
                    </button>
                </div>
            </form>

            <form method="GET" class="mb-6 flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search templates..."
                       class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-600 transition-colors">

                @if(request('search') || request('category'))
                    <a href="{{ route('medication-templates.index') }}"
                       class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-md text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Clear
                    </a>
                @endif
            </form>

            <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" id="select-all"
                                       class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Meds</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($templates as $template)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                                <td class="px-4 py-3">
                                    <input type="checkbox" value="{{ $template->id }}" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900">
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $template->short_name }}
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
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400">{{ $template->usage_text }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $template->medications_count }}</td>
                                <td class="px-6 py-4 text-right text-sm space-x-3">
                                    <button type="button" onclick="openTemplateDetailsDrawer({{ $template->toJson() }})"
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-medium transition-colors">View</button>
                                    <button type="button" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <p>No templates found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="lg:hidden space-y-4">
                @forelse($templates as $template)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5 transition-shadow hover:shadow-md">
                        <div class="flex items-start gap-3 mb-3">
                            <input type="checkbox" value="{{ $template->id }}" class="row-checkbox mt-1 w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900 dark:text-white truncate">{{ $template->short_name }}</h3>
                                @if($template->category)
                                    <span class="text-xs px-2 py-1 rounded-full mt-1 inline-block"
                                          style="background-color: {{ $template->category->color ?? '#6b7280' }}20; color: {{ $template->category->color ?? '#374151' }}">
                                        {{ $template->category->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1 mb-4">
                            <div>Usage: {{ $template->usage_text }}</div>
                            <div>Medications: {{ $template->medications_count }}</div>
                        </div>
                        <div class="flex gap-4 text-sm">
                            <button type="button" onclick="openTemplateDetailsDrawer({{ $template->toJson() }})"
                                    class="text-indigo-600 dark:text-indigo-400 font-medium transition-colors">View</button>
                            <button class="text-gray-600 dark:text-gray-400 transition-colors">Edit</button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">No templates found.</div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $templates->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Drawers remain unchanged -->
<div id="template-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
    <div id="template-drawer-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeTemplateDrawer()"></div>
    <div id="template-drawer-panel" class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col transform transition-transform duration-300 ease-out translate-y-full md:translate-y-0 md:translate-x-full h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
        <div class="md:hidden flex justify-center pt-4 pb-2"><div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div></div>
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <div><h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create Template</h3><p class="text-xs text-gray-500 dark:text-gray-400">Add a new medication template</p></div>
            <button onclick="closeTemplateDrawer()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 text-sm">
            <form method="POST" action="{{ route('medication-templates.store') }}" id="template-form">@csrf
                <div class="space-y-5">
                    <div><label for="name" class="block text-sm font-medium text-gray-900 dark:text-white mb-1.5">Template Name *</label><input type="text" id="name" name="name" required placeholder="e.g., Hypertension Pack" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-600 transition-colors"></div>
                    <div><label for="category_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-1.5">Category</label><select id="category_id" name="category_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-600 transition-colors"><option value="">No category</option>@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select></div>
                    <div><label for="description" class="block text-sm font-medium text-gray-900 dark:text-white mb-1.5">Description</label><textarea id="description" name="description" rows="4" placeholder="Add template details..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-600 transition-colors resize-none"></textarea></div>
                </div>
            </form>
        </div>
        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
            <button type="button" onclick="closeTemplateDrawer()" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">Cancel</button>
            <button type="submit" form="template-form" class="px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors font-medium">Create Template</button>
        </div>
    </div>
</div>

<div id="category-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
    <div id="category-drawer-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeCategoryDrawer()"></div>
    <div id="category-drawer-panel" class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col transform transition-transform duration-300 ease-out translate-y-full md:translate-y-0 md:translate-x-full h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
        <div class="md:hidden flex justify-center pt-4 pb-2"><div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div></div>
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <div><h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Category</h3><p class="text-xs text-gray-500 dark:text-gray-400">Create a new template category</p></div>
            <button onclick="closeCategoryDrawer()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 text-sm">
            <form method="POST" action="{{ route('medication-templates.categories.store') }}" id="category-form">@csrf
                <div class="space-y-5">
                    <div><label for="cat-name" class="block text-sm font-medium text-gray-900 dark:text-white mb-1.5">Category Name *</label><input type="text" id="cat-name" name="name" required placeholder="e.g., Cardiovascular" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-600 transition-colors"></div>
                    <div><label for="cat-color" class="block text-sm font-medium text-gray-900 dark:text-white mb-1.5">Color</label>
                        <div class="flex items-center gap-3">
                            <input type="text" id="cat-color" name="color" placeholder="e.g., blue-600" value="gray-600" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-600 transition-colors">
                            <div id="color-preview" class="w-10 h-10 rounded-lg border-2 border-gray-300 dark:border-gray-600 flex-shrink-0" style="background-color: rgb(75, 85, 99)"></div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Use Tailwind colors like: blue-600, red-500, green-500, purple-600, etc.</p>
                    </div>
                </div>
            </form>
        </div>
        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
            <button type="button" onclick="closeCategoryDrawer()" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">Cancel</button>
            <button type="submit" form="category-form" class="px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors font-medium">Create Category</button>
        </div>
    </div>
</div>

<div id="template-details-drawer" class="fixed inset-0 z-50 hidden overflow-hidden">
    <div id="details-drawer-overlay" class="absolute inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeTemplateDetailsDrawer()"></div>
    <div id="details-drawer-panel" class="absolute inset-x-0 bottom-0 md:inset-y-0 md:right-0 md:left-auto w-full md:max-w-md bg-white dark:bg-gray-800 shadow-2xl flex flex-col transform transition-transform duration-300 ease-out translate-y-full md:translate-y-0 md:translate-x-full h-[90vh] md:h-full rounded-t-3xl md:rounded-none">
        <div class="md:hidden flex justify-center pt-4 pb-2"><div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-600 rounded-full"></div></div>
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <div><h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="details-name"></h3><p class="text-xs text-gray-500 dark:text-gray-400">Template details</p></div>
            <button onclick="closeTemplateDetailsDrawer()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-5 text-sm">
            <div class="space-y-5">
                <div><h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Basic Info</h4>
                    <div class="grid grid-cols-1 gap-3">
                        <div><label class="text-xs text-gray-500 dark:text-gray-400">Name</label><div class="text-gray-900 dark:text-white font-medium" id="details-short-name"></div></div>
                        <div><label class="text-xs text-gray-500 dark:text-gray-400">Description</label><div class="text-gray-900 dark:text-white" id="details-description"></div></div>
                    </div>
                </div>
                <div><h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Category</h4><div id="details-category"></div></div>
                <div><h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Statistics</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="text-xs text-gray-500 dark:text-gray-400">Usage Count</label><div class="text-gray-900 dark:text-white font-semibold" id="details-usage"></div></div>
                        <div><label class="text-xs text-gray-500 dark:text-gray-400">Medications</label><div class="text-gray-900 dark:text-white font-semibold" id="details-meds"></div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
            <button onclick="closeTemplateDetailsDrawer()" class="w-full px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors">Close</button>
        </div>
    </div>
</div>

<style>html, body { overscroll-behavior-y: contain; }</style>

<script>
    let bodyScrollPosition = 0;

    // Template Drawer Functions
    function openTemplateDrawer() {
        const drawer = document.getElementById('template-drawer');
        const overlay = document.getElementById('template-drawer-overlay');
        const panel = document.getElementById('template-drawer-panel');

        bodyScrollPosition = window.pageYOffset;
        document.body.style.position = 'fixed';
        document.body.style.top = `-${bodyScrollPosition}px`;
        document.body.style.width = '100%';
        document.body.style.overflowY = 'scroll';

        drawer.classList.remove('hidden');
        overlay.classList.remove('opacity-0');
        overlay.classList.add('opacity-100');
        panel.classList.remove('translate-y-full', 'md:translate-x-full');
    }

    function closeTemplateDrawer() {
        const drawer = document.getElementById('template-drawer');
        const overlay = document.getElementById('template-drawer-overlay');
        const panel = document.getElementById('template-drawer-panel');

        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.overflowY = '';
        window.scrollTo(0, bodyScrollPosition);

        overlay.classList.remove('opacity-100');
        overlay.classList.add('opacity-0');
        panel.classList.add(window.innerWidth < 640 ? 'translate-y-full' : 'md:translate-x-full');
        setTimeout(() => drawer.classList.add('hidden'), 300);
    }

    // Category Drawer Functions
    function openCategoryDrawer() {
        const drawer = document.getElementById('category-drawer');
        const overlay = document.getElementById('category-drawer-overlay');
        const panel = document.getElementById('category-drawer-panel');

        bodyScrollPosition = window.pageYOffset;
        document.body.style.position = 'fixed';
        document.body.style.top = `-${bodyScrollPosition}px`;
        document.body.style.width = '100%';
        document.body.style.overflowY = 'scroll';

        drawer.classList.remove('hidden');
        overlay.classList.remove('opacity-0');
        overlay.classList.add('opacity-100');
        panel.classList.remove('translate-y-full', 'md:translate-x-full');
    }

    function closeCategoryDrawer() {
        const drawer = document.getElementById('category-drawer');
        const overlay = document.getElementById('category-drawer-overlay');
        const panel = document.getElementById('category-drawer-panel');

        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.overflowY = '';
        window.scrollTo(0, bodyScrollPosition);

        overlay.classList.remove('opacity-100');
        overlay.classList.add('opacity-0');
        panel.classList.add(window.innerWidth < 640 ? 'translate-y-full' : 'md:translate-x-full');
        setTimeout(() => drawer.classList.add('hidden'), 300);
    }

    // Template Details Drawer Functions
    function openTemplateDetailsDrawer(template) {
        const drawer = document.getElementById('template-details-drawer');
        const overlay = document.getElementById('details-drawer-overlay');
        const panel = document.getElementById('details-drawer-panel');

        document.getElementById('details-name').textContent = template.short_name || template.name;
        document.getElementById('details-short-name').textContent = template.short_name || template.name;
        document.getElementById('details-description').textContent = template.description || '—';
        document.getElementById('details-usage').textContent = template.usage_text || '—';
        document.getElementById('details-meds').textContent = template.medications_count || 0;

        if (template.category) {
            document.getElementById('details-category').innerHTML = `
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      style="background-color: ${template.category.color || '#6b7280'}20; color: ${template.category.color || '#374151'}">
                    ${template.category.name}
                </span>
            `;
        } else {
            document.getElementById('details-category').innerHTML = '<span class="text-xs text-gray-400">—</span>';
        }

        bodyScrollPosition = window.pageYOffset;
        document.body.style.position = 'fixed';
        document.body.style.top = `-${bodyScrollPosition}px`;
        document.body.style.width = '100%';
        document.body.style.overflowY = 'scroll';

        drawer.classList.remove('hidden');
        overlay.classList.remove('opacity-0');
        overlay.classList.add('opacity-100');
        panel.classList.remove('translate-y-full', 'md:translate-x-full');
    }

    function closeTemplateDetailsDrawer() {
        const drawer = document.getElementById('template-details-drawer');
        const overlay = document.getElementById('details-drawer-overlay');
        const panel = document.getElementById('details-drawer-panel');

        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.overflowY = '';
        window.scrollTo(0, bodyScrollPosition);

        overlay.classList.remove('opacity-100');
        overlay.classList.add('opacity-0');
        panel.classList.add(window.innerWidth < 640 ? 'translate-y-full' : 'md:translate-x-full');
        setTimeout(() => drawer.classList.add('hidden'), 300);
    }

    // Close all drawers with Escape key
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeTemplateDrawer();
            closeCategoryDrawer();
            closeTemplateDetailsDrawer();
        }
    });

    // Mobile swipe to close functionality
    function setupSwipeToClose(drawerId, closeFn) {
        const drawer = document.getElementById(drawerId);
        const panel = drawer.querySelector('[id$="-panel"]');
        let startY = 0;

        panel.addEventListener('touchstart', e => {
            if (window.innerWidth >= 640) return;
            startY = e.touches[0].clientY;
        }, {passive: true});

        panel.addEventListener('touchmove', e => {
            if (window.innerWidth >= 640) return;
            const delta = e.touches[0].clientY - startY;
            if (delta > 0) panel.style.transform = `translateY(${delta}px)`;
        }, {passive: true});

        panel.addEventListener('touchend', e => {
            if (window.innerWidth >= 640) return;
            const delta = e.changedTouches[0].clientY - startY;
            if (delta > 100) closeFn();
            else panel.style.transform = '';
        });
    }

    setupSwipeToClose('template-drawer', closeTemplateDrawer);
    setupSwipeToClose('category-drawer', closeCategoryDrawer);
    setupSwipeToClose('template-details-drawer', closeTemplateDetailsDrawer);

    // Select All Checkbox
    document.getElementById('select-all')?.addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkDelete();
    });

    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkDelete);
    });

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

    // Color preview update
    const colorInput = document.getElementById('cat-color');
    const colorPreview = document.getElementById('color-preview');
    if (colorInput) {
        colorInput.addEventListener('input', function() {
            colorPreview.style.backgroundColor = this.value || 'rgb(75, 85, 99)';
        });
    }
    </script>
@endsection