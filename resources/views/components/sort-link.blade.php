{{-- resources/views/components/sort-link.blade.php --}}
@props(['field'])

@php
    $currentSort = request('sort', 'name');
    $currentDir  = request('direction', 'asc');
    $isActive    = $currentSort === $field;
    $newDir      = $isActive && $currentDir === 'asc' ? 'desc' : 'asc';
    $url         = request()->fullUrlWithQuery(['sort' => $field, 'direction' => $newDir]);
@endphp

<a href="{{ $url }}"
   class="inline-flex items-center space-x-1 font-medium text-gray-700 dark:text-gray-300 hover:text-primary {{ $isActive ? 'text-primary' : '' }}">
    <span>{{ $slot }}</span>
    @if($isActive)
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            @if($currentDir === 'asc')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
            @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            @endif
        </svg>
    @else
        <svg class="w-4 h-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
        </svg>
    @endif
</a>