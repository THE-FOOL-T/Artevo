@extends('layouts.app')

@section('title', 'Dashboard — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            @include('dashboards.partials.verification-banner')

            <x-tag>Visitor Dashboard</x-tag>
            <h1 style="margin-top: var(--space-4);">Welcome back, {{ $user->name }}</h1>
            <p style="max-width: 560px;">Browse the public archive, save favorites, and follow auctions. Want to
                upload artifacts, bid, or donate a piece? Upgrade to a Collector account below.</p>

            <x-card style="margin-top: var(--space-6); background: var(--brass-100); border-color: transparent;">
                <h3>Become a Collector</h3>
                <p>Collectors can upload and archive their own artifacts, join and bid in auctions, request
                    verification, and donate pieces to partner museums.</p>
                <form method="POST" action="{{ route('role-upgrade.store') }}">
                    @csrf
                    <x-button type="submit" variant="dark">Upgrade to Collector</x-button>
                </form>
            </x-card>

            <x-card style="margin-top: var(--space-4); background: var(--brass-100); border-color: transparent;">
                <h3>Museum Staff?</h3>
                <p>If you work at a museum and want to manage its digital presence, you can apply for a Curator account.</p>
                @if ($user->curatorApplication?->isPending())
                    <p style="margin-top: var(--space-3); color: var(--brass-700); font-weight: 500;">
                        ✓ Your curator application is pending review.
                    </p>
                @else
                    <div style="margin-top: var(--space-3);">
                        <x-button type="link" href="{{ route('curator-applications.create') }}" variant="dark">Apply to be a Curator</x-button>
                    </div>
                @endif
            </x-card>

            <div class="grid grid-3" style="margin-top: var(--space-8);">
                <x-card data-reveal>
                    <span class="av-card__eyebrow">Account</span>
                    <h3>Your profile</h3>
                    <p>Update your name, email, avatar, and password.</p>
                    <a href="{{ route('profile.edit') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Go to profile &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="1">
                    <span class="av-card__eyebrow">Saved</span>
                    <h3>My Favorites</h3>
                    <p>Artifacts, collections, and exhibitions you have bookmarked.</p>
                    <a href="{{ route('favorites.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">View favorites &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="2">
                    <span class="av-card__eyebrow">Auctions</span>
                    <h3>Watched Auctions</h3>
                    <p>Keep track of live auctions you are interested in.</p>
                    <p style="margin-top: var(--space-2); font-size: var(--text-sm); color: var(--stone-600);">Note: Upgrade to Collector to place bids.</p>
                    <a href="{{ route('auctions.watchlist') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm); display: inline-block; margin-top: var(--space-3);">View watchlist &rarr;</a>
                </x-card>
            </div>
        </div>
    </section>
@endsection
