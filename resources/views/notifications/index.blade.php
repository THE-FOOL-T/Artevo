@extends('layouts.app')

@section('title', 'Notifications — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container max-w-content">
            <div class="flex-between">
                <div>
                    <x-tag>Notifications</x-tag>
                    <h1 style="margin-top: var(--space-4);">Your notifications</h1>
                </div>
                @if ($notifications->getCollection()->contains(fn ($n) => $n->read_at === null))
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <x-button type="submit" variant="outline-dark">Mark all read</x-button>
                    </form>
                @endif
            </div>

            @if ($notifications->isEmpty())
                <x-card class="mt-8">
                    <p style="margin: 0;">You don't have any notifications yet.</p>
                </x-card>
            @else
                <div style="margin-top: var(--space-6); display: flex; flex-direction: column; gap: var(--space-3);">
                    @foreach ($notifications as $notification)
                        <x-card style="{{ $notification->read_at ? 'opacity: 0.65;' : '' }}">
                            <div class="flex-between">
                                <div>
                                    <h3 style="font-size: var(--text-lg);">{{ $notification->data['title'] ?? 'Notification' }}</h3>
                                    <p style="margin-bottom: 0;">{{ $notification->data['body'] ?? '' }}</p>
                                    <span style="font-size: var(--text-xs); color: var(--stone-600);">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                @unless ($notification->read_at)
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                        @csrf
                                        <x-button type="submit" variant="outline-dark" style="padding: 0.5rem 1rem; font-size: var(--text-xs);">Mark read</x-button>
                                    </form>
                                @endunless
                            </div>
                        </x-card>
                    @endforeach
                </div>

                <div style="margin-top: var(--space-6);">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
