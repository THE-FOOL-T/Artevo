@extends('layouts.app')

@section('title', 'Dashboard — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            @include('dashboards.partials.verification-banner')

            <x-tag>Curator Dashboard</x-tag>
            <h1 style="margin-top: var(--space-4);">Welcome back, {{ $user->name }}</h1>
            <p style="max-width: 560px;">Museum collections, verification queues, and exhibition management
                will surface here as those modules go live.</p>

            <div class="grid grid-4" style="margin-top: var(--space-8);">
                <x-card data-reveal>
                    <span class="av-card__eyebrow">Coming in Phase 5–6</span>
                    <h3>Managed museums</h3>
                    <p>Museum profiles and collections you curate.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="1">
                    <span class="av-card__eyebrow">Coming in Phase 11</span>
                    <h3>Verification queue</h3>
                    <p>Artifacts awaiting your review, approval, or rejection.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="2">
                    <span class="av-card__eyebrow">Coming in Phase 10</span>
                    <h3>Active exhibitions</h3>
                    <p>Exhibitions you're currently running or building.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="3">
                    <span class="av-card__eyebrow">Coming in Phase 12 &amp; 15</span>
                    <h3>Curator notes &amp; restoration</h3>
                    <p>Provenance commentary and restoration record updates.</p>
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
