{{-- dashboard/partials/notifications.blade.php
Requires: $notifications (collection), $unreadCount, $primary_color
--}}
<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ __('file.notifications') ?? 'Notifications' }}
        </h2>
        @if($unreadCount > 0)
            <button type="button" onclick="markAllPersistentAsRead(this)" class="text-sm font-medium hover:underline transition"
                style="color:{{ $primary_color }}">
                {{ __('file.mark_all_read') ?? 'Mark all as read' }}
            </button>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                style="background:{{ $primary_color }}15">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    style="color:{{ $primary_color }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                {{ __('file.no_notifications') ?? 'No notifications yet' }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ __('file.no_notifications_desc') ?? 'You\'re all caught up! New notifications will appear here.' }}
            </p>
        </div>
    @else
            <div class="space-y-3">
                @php
                    $isCondensed = $condensed ?? false;
                    $displayNotifications = $isCondensed ? $notifications->take(5) : $notifications;
                @endphp

                @forelse($displayNotifications as $notification)
                        @php
                            $isUnread = is_null($notification->read_at);
                            $nData = $notification->data ?? [];
                            $message = $nData['message'] ?? ($nData['body'] ?? '');
                            $title = $nData['title'] ?? (isset($nData['type']) ? $nData['type'] : class_basename($notification->type));
                            // Clean up camelCase class names if no title
                            if (!isset($nData['title'])) {
                                $title = preg_replace('/(?<!^)([A-Z])/', ' $1', $title);
                            }
                            $url = $nData['url'] ?? ($nData['action_url'] ?? '#');
                            $icon = $nData['icon'] ?? 'bell';
                        @endphp
                        <div class="notification-item group bg-white dark:bg-gray-800 rounded-2xl border transition-all {{ $isUnread ? 'border-l-4 shadow-sm' : 'border-gray-200 dark:border-gray-700' }} hover:border-gray-300 dark:hover:border-gray-600"
                            data-id="{{ $notification->id }}"
                            style="{{ $isUnread ? 'border-left-color:' . $primary_color . '; border-color:' . $primary_color . '40' : '' }}">
                            <div class="{{ $isCondensed ? 'p-3' : 'p-4' }} flex items-start gap-4">
                                {{-- Icon --}}
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                                    style="background: {{ $primary_color }}15; color: {{ $primary_color }}">
                                    <i class="fas fa-{{ $icon }}"></i>
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $title }}
                                        </p>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            @if($isUnread)
                                                <span class="w-2 h-2 rounded-full flex-shrink-0"
                                                    style="background:{{ $primary_color }}"></span>
                                            @endif
                                            <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5 {{ $isCondensed ? 'line-clamp-1' : 'line-clamp-2' }}">{{ $message }}</p>

                                    @if(!$isCondensed)
                                        <div class="mt-2 flex items-center gap-3">
                                            @if($url !== '#')
                                                <a href="{{ $url }}" class="text-xs font-medium hover:underline transition"
                                                    style="color:{{ $primary_color }}">
                                                    {{ __('file.view') ?? 'View' }} →
                                                </a>
                                            @endif
                                            @if($isUnread)
                                                <button type="button" onclick="markPersistentAsRead('{{ $notification->id }}', this)"
                                                    class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                                                    {{ __('file.mark_as_read') ?? 'Mark as read' }}
                                                </button>
                                            @endif
                                            <button type="button" onclick="deletePersistentNotification('{{ $notification->id }}', this)"
                                                class="text-xs text-red-400 hover:text-red-600 dark:hover:text-red-300 transition flex items-center gap-1 opacity-100 md:opacity-0 group-hover:opacity-100">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                {{ __('file.remove') ?? 'Remove' }}
                                            </button>
                                        </div>
                                    @else
                                        <div class="mt-1">
                                            <a href="{{ $url }}" class="text-xs font-medium hover:underline transition"
                                                style="color:{{ $primary_color }}">
                                                {{ __('file.view') ?? 'View' }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                @empty
                    <div class="text-center py-8 px-4 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('file.no_notifications') ?? 'No notifications' }}</p>
                    </div>
                @endforelse
            </div>
            @if(!$isCondensed)
                <div class="mt-8">
                    {{ $notifications->appends(['tab' => 'notifications'])->links() }}
                </div>
            @endif
    @endif
</div>

@once
    @push('scripts')
        <script>
            function deletePersistentNotification(id, element) {
                if (!confirm('{{ __("file.are_you_sure") ?? "Are you sure?" }}')) return;

                const card = element.closest('.notification-item');
                if (!card) return;

                // Visual feedback
                element.disabled = true;
                card.style.opacity = '0.5';

                const url = '{{ route("notifications.destroy", ":id") }}'.replace(':id', id);

                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update global count if it was unread
                            if (card.querySelector('.rounded-full[style*="{{ $primary_color }}"]')) {
                                updateGlobalNotificationCount();
                            }

                            // Animate and remove
                            card.style.transition = 'all 0.3s ease';
                            card.style.transform = 'translateX(20px)';
                            card.style.opacity = '0';

                            setTimeout(() => {
                                card.remove();
                                // Check if list is empty now
                                const container = document.querySelector('.space-y-3');
                                if (container && container.querySelectorAll('.notification-item').length === 0) {
                                    window.location.reload(); // Simple way to show the "No notifications" empty state
                                }
                            }, 300);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        element.disabled = false;
                        card.style.opacity = '1';
                    });
            }

            function markPersistentAsRead(id, element) {
                const card = element.closest('.group');
                if (!card) return;

                // Visual feedback
                element.disabled = true;
                const originalText = element.textContent;
                element.textContent = '...';

                const url = '{{ route("notifications.read", ":id") }}'.replace(':id', id);

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update UI
                            card.classList.remove('border-l-4', 'shadow-sm');
                            card.classList.add('border-gray-200', 'dark:border-gray-700', 'opacity-60');
                            card.style.borderLeftColor = '';
                            card.style.borderColor = '';

                            // Remove dot
                            const dot = card.querySelector('.rounded-full');
                            if (dot) dot.remove();

                            // Remove button
                            element.remove();

                            // Update counters if any
                            updateGlobalNotificationCount();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        element.disabled = false;
                        element.textContent = originalText;
                    });
            }

            function updateGlobalNotificationCount() {
                // This updates the count in the tab and header if they exist
                const badges = document.querySelectorAll('.unread-count-badge, #tab-notifications span, [aria-label="Notifications"] .absolute');
                badges.forEach(badge => {
                    let text = badge.textContent.trim();
                    // Handle 99+ etc
                    let count = parseInt(text) || 0;
                    if (count > 0) {
                        count--;
                        badge.textContent = count > 0 ? (count > 99 ? '99+' : count) : '';
                        if (count <= 0) badge.remove();
                    }
                });

                // Also update the role info pill in dashboard if it exists
                const pillBadges = document.querySelectorAll('.inline-flex.bg-red-100.text-red-700, .inline-flex.bg-red-900\\/30.text-red-400');
                pillBadges.forEach(pillBadge => {
                    let text = pillBadge.textContent;
                    let countMatch = text.match(/\d+/);
                    if (countMatch) {
                        let count = parseInt(countMatch[0]) - 1;
                        if (count <= 0) {
                            pillBadge.remove();
                        } else {
                            // Keep the icon and update text
                            const icon = pillBadge.querySelector('svg');
                            pillBadge.innerHTML = '';
                            if (icon) pillBadge.appendChild(icon);
                            pillBadge.appendChild(document.createTextNode(` ${count} {{ __('file.unread') ?? 'Unread' }}`));
                        }
                    }
                });
            }
            function markAllPersistentAsRead(element) {
                if (element) {
                    element.disabled = true;
                    element.style.opacity = '0.5';
                }

                fetch('{{ route("notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update all notification cards in this view
                            const cards = document.querySelectorAll('.group.border-l-4');
                            cards.forEach(card => {
                                card.classList.remove('border-l-4', 'shadow-sm');
                                card.classList.add('border-gray-200', 'dark:border-gray-700', 'opacity-60');
                                card.style.borderLeftColor = '';
                                card.style.borderColor = '';
                                const dot = card.querySelector('.rounded-full');
                                if (dot) dot.remove();
                                const btn = card.querySelector('button[onclick^="markAsRead"]');
                                if (btn) btn.remove();
                            });

                            // Remove/hide the "Mark all as read" buttons
                            const allReadBtns = document.querySelectorAll('button[onclick="markAllPersistentAsRead(this)"]');
                            allReadBtns.forEach(btn => btn.remove());

                            // Clear header dropdown unread indicators if they exist
                            const headerNotifs = document.querySelectorAll('.bg-indigo-50.dark\\:bg-indigo-900\\/10');
                            headerNotifs.forEach(notif => {
                                notif.classList.remove('bg-indigo-50', 'dark:bg-indigo-900/10');
                                const dot = notif.querySelector('.bg-indigo-500');
                                if (dot) dot.classList.replace('bg-indigo-500', 'bg-transparent');
                            });

                            // Update badge counts to 0
                            const badges = document.querySelectorAll('.unread-count-badge, #tab-notifications span, [aria-label="Notifications"] .absolute, .inline-flex.bg-red-100.text-red-700, .inline-flex.bg-red-900\\/30.text-red-400');
                            badges.forEach(badge => badge.remove());
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (element) {
                            element.disabled = false;
                            element.style.opacity = '1';
                        }
                    });
            }
        </script>
    @endpush
@endonce