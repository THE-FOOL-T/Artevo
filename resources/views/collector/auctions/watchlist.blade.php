@extends('layouts.app')

@section('title', 'My Watchlist — Artevo')

@section('content')
    <section class="av-section" style="padding-top: var(--space-10);">
        <div class="container">
            <a href="{{ route('dashboard') }}" style="display:inline-block; margin-bottom:var(--space-6); color:var(--ink-500); font-size:var(--text-sm); font-weight:500;">
                &larr; Back to Dashboard
            </a>

            <div style="margin-bottom: var(--space-8);">
                <x-tag>Collector</x-tag>
                <h1 style="margin-top: var(--space-4);">Watched Auctions</h1>
                <p>Keep track of live auctions you are interested in.</p>
            </div>

            @if ($auctions->isEmpty())
                <p style="color: var(--ink-500);">You aren't watching any auctions right now.</p>
            @else
                <div class="grid grid-3">
                    @foreach ($auctions as $auction)
                        <x-card>
                            <h3><a href="{{ route('auctions.show', $auction) }}" style="text-decoration: none; color: inherit;">{{ $auction->title }}</a></h3>
                            <p style="margin-top: var(--space-2); color: var(--ink-500);">Current Bid: {{ $auction->currency }} {{ number_format($auction->current_price, 2) }}</p>
                            @if($auction->isOpen())
                                <span class="av-tag av-tag--success" style="margin-top: var(--space-3);">Live</span>
                            @else
                                <span class="av-tag av-tag--muted" style="margin-top: var(--space-3);">Closed</span>
                            @endif
                        </x-card>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
