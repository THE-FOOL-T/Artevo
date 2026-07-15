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

            <div class="grid grid-3" style="margin-top: var(--space-8);">
                <x-card data-reveal>
                    <span class="av-card__eyebrow">Account</span>
                    <h3>Your profile</h3>
                    <p>Update your name, email, avatar, and password.</p>
                    <a href="{{ route('profile.edit') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Go to profile &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="1">
                    <span class="av-card__eyebrow">Coming in Phase 8–10</span>
                    <h3>Favorites &amp; saved exhibitions</h3>
                    <p>Bookmark artifacts and exhibitions to find them again quickly.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="2">
                    <span class="av-card__eyebrow">Coming in Phase 13–14</span>
                    <h3>Auctions you're watching</h3>
                    <p>Track live auctions and see your bid history once you upgrade.</p>
                </x-card>
            </div>
        </div>
    </section>
@endsection
