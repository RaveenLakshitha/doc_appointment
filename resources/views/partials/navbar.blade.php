{{-- resources/views/partials/navbar.blade.php --}}
<header
    class="bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700 transition-colors fixed top-0 left-0 right-0 z-50">
    <div class="max-w-full px-4 sm:px-6 flex items-center justify-between h-16">

        {{-- Left: sidebar toggle --}}
        <div class="flex items-center">
            <button id="open-sidebar" class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100
                           dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700
                           focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2
                           dark:focus:ring-offset-gray-800 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        {{-- Right: all controls --}}
        <div class="flex items-center gap-1 sm:gap-2">

            {{-- Appointment Manager Link --}}
            <a href="{{ route('appointments.manager') }}" title="{{ __('Appointment Manager') }}" class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100
                           dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700
                           focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2
                           dark:focus:ring-offset-gray-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </a>

            @can('invoices.index')
                {{-- POS Link --}}
                <a href="{{ route('invoices.pos') }}" title="{{ __('file.pos') ?? 'POS' }}" class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100
                               dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700
                               focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2
                               dark:focus:ring-offset-gray-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </a>
            @endcan

            {{-- Language switcher --}}
            <div x-data="{ open: false }" x-init="open = false" class="relative">
                <button @click="open = !open" class="flex items-center space-x-1 text-sm font-medium text-gray-700 dark:text-gray-300
                               hover:text-primary dark:hover:text-primary-400
                               focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2
                               dark:focus:ring-offset-gray-800 rounded-md px-2 py-1 transition">
                    <span>{{ strtoupper(app()->getLocale()) }}</span>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-cloak @click.away="open = false"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-36 origin-top-right bg-white dark:bg-gray-800
                            rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50 overflow-hidden">
                    <form method="POST" action="{{ route('language.switch') }}">
                        @csrf
                        <input type="hidden" name="locale" value="en">
                        <button type="submit"
                            class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            {{ __('English') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('language.switch') }}">
                        @csrf
                        <input type="hidden" name="locale" value="es">
                        <button type="submit"
                            class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            {{ __('Español') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Dark mode toggle --}}
            <button id="theme-toggle-navbar" aria-label="Toggle dark mode" class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100
                        dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700
                        focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2
                        dark:focus:ring-offset-gray-800 transition">
                <svg id="sun-icon-navbar" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m8.485-11.485l-.707.707M5.636 18.364l-.707.707m12.021 0l-.707-.707M6.343 5.636l-.707-.707m12.728 12.728l-.707-.707M6.343 18.364l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" />
                </svg>
                <svg id="moon-icon-navbar" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            {{-- Fullscreen toggle --}}
            <button id="fullscreen-toggle" aria-label="Toggle fullscreen" class="hidden sm:block p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100
                    dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700
                    focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2
                    dark:focus:ring-offset-gray-800 transition">
                <svg id="enter-fullscreen-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8V4m0 0h4m-4 0l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 0h-4m4 0l-5-5" />
                </svg>
                <svg id="exit-fullscreen-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8V4m0 0h4m-4 0l5 5m11-8v4m0-4h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0-4h-4m4 4l-5-5" />
                </svg>
            </button>

            {{-- ── NOTIFICATIONS BELL ─────────────────────────────────────────── --}}
            @auth
                <div class="relative" x-data="{ open: false }" x-init="open = false">
                    <button @click="open = !open" id="notif-btn" aria-label="Notifications" class="relative p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100
                                                               dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700
                                                               focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2
                                                               dark:focus:ring-offset-gray-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @php
                            $unreadNotifCount = auth()->user()->unreadNotifications()->count();
                        @endphp
                        @if($unreadNotifCount > 0)
                            <span
                                class="absolute top-1 right-1 inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-500 rounded-full leading-none">
                                {{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}
                            </span>
                        @endif
                    </button>

                    {{-- Dropdown panel — fixed width, sticks to right on all screen sizes --}}
                    <div x-show="open" x-cloak @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="fixed sm:absolute top-16 sm:top-auto sm:mt-2 left-1/2 sm:left-auto right-auto sm:right-0 -translate-x-1/2 sm:translate-x-0
                                                            w-[calc(100vw-1rem)] sm:w-96 max-w-sm
                                                            origin-top-right bg-white dark:bg-gray-800
                                                            rounded-xl shadow-xl ring-1 ring-black ring-opacity-10
                                                            dark:ring-gray-700 z-50 overflow-hidden">

                        {{-- Header --}}
                        <div
                            class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ __('file.notifications') ?? 'Notifications' }}
                                @if($unreadNotifCount > 0)
                                    <span
                                        class="ml-1.5 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                                        {{ $unreadNotifCount }}
                                    </span>
                                @endif
                            </h3>
                            @if($unreadNotifCount > 0)
                                <button type="button"
                                    onclick="typeof markAllAsReadGlobal === 'function' ? markAllAsReadGlobal(this) : window.location.reload()"
                                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                    {{ __('file.mark_all_read') ?? 'Mark all read' }}
                                </button>
                            @endif
                        </div>

                        {{-- Notification list --}}
                        <div class="overflow-y-auto max-h-80 divide-y divide-gray-100 dark:divide-gray-700">
                            @php
                                $headerNotifs = auth()->user()->notifications()->latest()->take(10)->get();
                            @endphp
                            @forelse($headerNotifs as $notif)
                                @php
                                    $nData = $notif->data ?? [];
                                    $nTitle = $nData['title'] ?? class_basename($notif->type);
                                    $nMsg = $nData['message'] ?? ($nData['body'] ?? '');
                                    $nUrl = $nData['url'] ?? ($nData['link'] ?? '#');
                                    $isUnread = is_null($notif->read_at);
                                @endphp
                                <div
                                    class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ $isUnread ? 'bg-indigo-50 dark:bg-indigo-900/10' : '' }}">
                                    {{-- Unread dot --}}
                                    <div class="mt-1 flex-shrink-0">
                                        @if($isUnread)
                                            <span class="inline-block w-2 h-2 rounded-full bg-indigo-500"></span>
                                        @else
                                            <span class="inline-block w-2 h-2 rounded-full bg-transparent"></span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ $nTitle }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">{{ $nMsg }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if($nUrl !== '#')
                                        <a href="{{ $nUrl }}"
                                            class="flex-shrink-0 text-xs text-indigo-600 dark:text-indigo-400 hover:underline mt-1">
                                            {{ __('file.view') ?? 'View' }}
                                        </a>
                                    @endif
                                </div>
                            @empty
                                <div class="px-4 py-8 text-center">
                                    <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('file.no_notifications') ?? 'No notifications' }}
                                    </p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Footer: link to full notifications on dashboard --}}
                        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 text-center">
                            <a href="{{ route('dashboard') }}"
                                class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ __('file.view_all_notifications') ?? 'View all notifications' }}
                            </a>
                        </div>
                    </div>
                </div>
            @endauth

            {{-- User menu --}}
            <div class="relative" x-data="{ open: false }" x-cloak x-init="open = false">
                <button id="user-menu-button" @click="open = !open" class="flex items-center space-x-2 text-sm font-medium text-gray-700 dark:text-gray-300
                               hover:text-primary dark:hover:text-primary-400
                               focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2
                               dark:focus:ring-offset-gray-800 rounded-md px-2 py-1 transition">
                    <img class="h-8 w-8 rounded-full object-cover ring-2 ring-gray-300 dark:ring-gray-600"
                        src="{{ auth()->user()?->avatar }}" alt="{{ auth()->user()?->name }}">
                    <span class="hidden sm:block">{{ auth()->user()?->name }}</span>
                </button>

                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-48 origin-top-right bg-white dark:bg-gray-800
                            rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50 overflow-hidden">

                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300
                                       hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>