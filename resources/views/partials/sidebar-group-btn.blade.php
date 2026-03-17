{{-- partials/sidebar-group-btn.blade.php
Required vars: $label, $icon (SVG path d="...")
Uses AlpineJS x-data parent's `open` variable.
--}}
<button @click="activeGroup = (activeGroup === '{{ $name }}' ? 'none' : '{{ $name }}')"
    class="w-full group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-300 text-gray-700 dark:text-gray-300 sidebar-item-hover">
    <div class="flex items-center">
        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $icon !!}
        </svg>
        <span class="ml-3 sidebar-text">{{ $label }}</span>
    </div>
    <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-90': activeGroup === '{{ $name }}' }"
        fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
    </svg>
</button>