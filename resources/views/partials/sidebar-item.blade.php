{{-- partials/sidebar-item.blade.php
Required vars: $route, $active (bool), $label, $icon (SVG path d=""), $tooltip (optional)
--}}
<a href="{{ route($route) }}"
    class="group flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-300 relative overflow-hidden sidebar-item-hover {{ $active ? 'sidebar-item-active' : 'text-gray-600 dark:text-gray-400' }}"
    @isset($tooltip) data-tooltip="{{ $tooltip }}" @endisset>
    <svg class="h-5 w-5 flex-shrink-0 transition-colors duration-300" fill="none" stroke="currentColor"
        viewBox="0 0 24 24">
        {!! $icon !!}
    </svg>
    <span class="ml-3 sidebar-text">{{ $label }}</span>
</a>