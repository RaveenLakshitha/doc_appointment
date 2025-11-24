@extends('layouts.app')

@section('title', $template->name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8 max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $template->name }}</h1>
                @if($template->description)
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $template->description }}</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @if($template->category)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                          style="background-color: {{ $template->category->color ?? '#6b7280' }}20; color: {{ $template->category->color ?? '#374151' }}">
                        {{ $template->category->name }}
                    </span>
                @endif
            </div>
        </div>

        <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
            <div>
                <div class="text-gray-500 dark:text-gray-400">Created by</div>
                <div class="font-medium text-gray-900 dark:text-white mt-1">
                    {{ $template->creator?->full_name ?? 'Unknown' }}
                </div>
            </div>
            <div>
                <div class="text-gray-500 dark:text-gray-400">Total Medications</div>
                <div class="font-medium text-gray-900 dark:text-white mt-1">
                    {{ $template->medications_count ?? $template->medications->count() }}
                </div>
            </div>
            <div>
                <div class="text-gray-500 dark:text-gray-400">Times Used</div>
                <div class="font-medium text-gray-900 dark:text-white mt-1">
                    {{ $template->usages_count ?? 0 }}
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Last used: {{ $template->last_used_at?->diffForHumans() ?? 'Never' }}
                </span>
                <div class="flex gap-3">
                    <a href="{{ route('medication-templates.edit', $template) }}"
                       class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 transition-colors">
                        Edit Template
                    </a>
                    <a href="{{ route('medication-templates.index') }}"
                       class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        @if($template->medications->isNotEmpty())
            <div class="px-6 py-5">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Medications in this Template</h3>
                <div class="space-y-4">
                    @foreach($template->medications as $med)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $med->generic_name }}
                                    @if($med->brand_name)
                                        <span class="text-gray-500 dark:text-gray-400">({{ $med->brand_name }})</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $med->strength }} • {{ $med->dosageForm?->name ?? '—' }}
                                </div>
                            </div>
                            <div class="text-right text-sm">
                                <div class="font-medium">{{ $med->pivot->dosage }}</div>
                                <div class="text-gray-500 dark:text-gray-400">{{ $med->pivot->frequency }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                <p>No medications added to this template yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection