@extends('layouts.app')

@section('title', 'Dashboard — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            @include('dashboards.partials.verification-banner')

            <x-tag>Collector Dashboard</x-tag>
            <h1 style="margin-top: var(--space-4);">Welcome back, {{ $user->name }}</h1>
            <p style="max-width: 560px;">Your uploaded artifacts, collections, auctions and donations will
                surface here as those modules go live.</p>

            <div class="grid grid-4" style="margin-top: var(--space-8);">
                <x-card data-reveal>
                    <span class="av-card__eyebrow">Coming in Phase 7</span>
                    <h3>Uploaded artifacts</h3>
                    <p>Archive pieces from your own collection.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="1">
                    <span class="av-card__eyebrow">Coming in Phase 9</span>
                    <h3>Your collections</h3>
                    <p>Group artifacts into public or private collections.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="2">
                    <span class="av-card__eyebrow">Coming in Phase 13–14</span>
                    <h3>Auctions &amp; bids</h3>
                    <p>Join auctions, track your current bids, and see what you've won.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="3">
                    <span class="av-card__eyebrow">Coming in Phase 11 &amp; 15</span>
                    <h3>Verification &amp; donations</h3>
                    <p>Request verification for a piece, or donate it to a partner museum.</p>
                </x-card>
            </div>

            <x-card class="mt-8">
                <span class="av-card__eyebrow">Account</span>
                <h3>Your profile</h3>
                <p>Update your name, email, avatar, and password.</p>
                <a href="{{ route('profile.edit') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Go to profile &rarr;</a>
            </x-card>
        </div>
    </section>
@endsection
