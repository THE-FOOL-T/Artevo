@php
    $unread = auth()->user()->unreadNotifications()->limit(8)->get();
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

<div class="av-dropdown" data-dropdown>
    <button type="button" class="av-dropdown__trigger" data-dropdown-trigger aria-haspopup="true" aria-expanded="false" aria-label="Notifications">
        <span style="position: relative; display: inline-flex;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            @if ($unreadCount > 0)
                <span class="av-notification-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
            @endif
        </span>
    </button>

    <div class="av-dropdown__panel av-dropdown__panel--wide" data-dropdown-panel role="menu">
        <div class="av-notification-panel__header">
            <span>Notifications</span>
            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="av-notification-panel__mark-all">Mark all read</button>
                </form>
            @endif
        </div>

        @forelse ($unread as $notification)
            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                @csrf
                <button type="submit" class="av-dropdown__item av-notification-item">
                    <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong>
                    <span>{{ $notification->data['body'] ?? '' }}</span>
                </button>
            </form>
        @empty
            <p style="padding: var(--space-4); font-size: var(--text-sm); color: var(--ink-700); margin: 0;">You're all caught up.</p>
        @endforelse

        <a href="{{ route('notifications.index') }}" class="av-dropdown__item" style="text-align: center; color: var(--brass-700); font-weight: 600;">View all</a>
    </div>
</div>
