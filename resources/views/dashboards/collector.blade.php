@extends('layouts.app')

@section('title', 'Dashboard — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            @include('dashboards.partials.verification-banner')

            <x-tag>Collector Dashboard</x-tag>
            <h1 style="margin-top: var(--space-4);">Welcome back, {{ $user->name }}</h1>
            <p style="max-width: 560px;">Manage your artifact collection and digital collections below.</p>

            <div class="grid grid-4" style="margin-top: var(--space-8);">
                <x-card data-reveal>
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ $artifactCount }}</div>
                    <p style="margin-top: var(--space-2);">Artifact{{ $artifactCount === 1 ? '' : 's' }} in your collection</p>
                    <a href="{{ route('collector.artifacts.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Manage collection &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="1">
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ $collectionCount }}</div>
                    <p style="margin-top: var(--space-2);">Collection{{ $collectionCount === 1 ? '' : 's' }}</p>
                    <a href="{{ route('collector.collections.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Manage collections &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="2">
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ $auctionCount }}</div>
                    <p style="margin-top: var(--space-2);">Active auction{{ $auctionCount === 1 ? '' : 's' }}</p>
                    <a href="{{ route('auctions.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Browse auctions &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="3">
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ $donationCount }}</div>
                    <p style="margin-top: var(--space-2);">Donation request{{ $donationCount === 1 ? '' : 's' }}</p>
                    <a href="{{ route('donations.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">My donations &rarr;</a>
                </x-card>
                @if($pendingDonations > 0)
                <x-card data-reveal data-reveal-delay="4" style="border-color: rgba(245,158,11,.3); background: rgba(245,158,11,.04);">
                    <span class="av-card__eyebrow" style="color: #d97706;">Pending</span>
                    <div class="av-stat-num" style="color: #d97706; font-size: var(--text-3xl);">{{ $pendingDonations }}</div>
                    <p style="margin-top: var(--space-2);">Donation{{ $pendingDonations === 1 ? '' : 's' }} awaiting review</p>
                    <a href="{{ route('donations.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Check status &rarr;</a>
                </x-card>
                @endif
            </div>

            <div class="grid grid-3 mt-8">
                <x-card>
                    <span class="av-card__eyebrow">Saved</span>
                    <h3>My Favorites</h3>
                    <p>Artifacts, collections, and exhibitions you have bookmarked.</p>
                    <a href="{{ route('favorites.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">View favorites &rarr;</a>
                </x-card>
                <x-card>
                    <span class="av-card__eyebrow">Auctions</span>
                    <h3>Watchlist & Bids</h3>
                    <p>Track live auctions and view your bidding history.</p>
                    <div style="display: flex; gap: var(--space-4); margin-top: var(--space-2);">
                        <a href="{{ route('auctions.watchlist') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Watchlist</a>
                        <a href="{{ route('auctions.bids') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Bid history</a>
                    </div>
                </x-card>
                <x-card>
                    <span class="av-card__eyebrow">Account</span>
                    <h3>Your profile</h3>
                    <p>Update your name, email, avatar, and password.</p>
                    <a href="{{ route('profile.edit') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Go to profile &rarr;</a>
                </x-card>
            </div>

            <section class="mt-8" style="background: #EDE8DC; border-radius: 0.5rem; padding: 2.5rem;">
                <h3 style="margin: 0 0 var(--space-2);">Museum Staff?</h3>
                <p style="max-width: 560px; margin-bottom: var(--space-4);">If you work at a museum and want to manage its digital presence, you can apply for a Curator account.</p>
                @if ($user->curatorApplication?->isPending())
                    <p style="color: var(--brass-700); font-weight: 500;">
                        ✓ Your curator application is pending review.
                    </p>
                @else
                    <a href="{{ route('curator-applications.create') }}" class="av-btn av-btn--dark" style="display: inline-block;">Apply to be a Curator</a>
                @endif
            </section>
        </div>
    </section>
@endsection
