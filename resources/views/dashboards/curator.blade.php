@extends('layouts.app')

@section('title', 'Dashboard — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            @include('dashboards.partials.verification-banner')

            <x-tag>Curator Dashboard</x-tag>
            <h1 style="margin-top: var(--space-4);">Welcome back, {{ $user->name }}</h1>
            <p style="max-width: 560px;">Manage your museum profiles, artifacts, collections, and exhibitions below.</p>

            <div class="grid grid-4" style="margin-top: var(--space-8);">
                <x-card data-reveal>
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ $museumCount }}</div>
                    <p style="margin-top: var(--space-2);">Museum{{ $museumCount === 1 ? '' : 's' }} you manage</p>
                    <a href="{{ route('curator.museums.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Manage museums &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="1">
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ $artifactCount }}</div>
                    <p style="margin-top: var(--space-2);">Artifact{{ $artifactCount === 1 ? '' : 's' }} across your museums</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="2">
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ $collectionCount }}</div>
                    <p style="margin-top: var(--space-2);">Collection{{ $collectionCount === 1 ? '' : 's' }}</p>
                    <a href="{{ route('curator.museums.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Manage collections &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="2">
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ $auctionCount }}</div>
                    <p style="margin-top: var(--space-2);">Active auction{{ $auctionCount === 1 ? '' : 's' }}</p>
                    <a href="{{ route('auctions.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Browse auctions &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="3">
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ $exhibitionCount }}</div>
                    <p style="margin-top: var(--space-2);">Exhibition{{ $exhibitionCount === 1 ? '' : 's' }}</p>
                    <a href="{{ route('curator.museums.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Manage exhibitions &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="4">
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ $restorationCount }}</div>
                    <p style="margin-top: var(--space-2);">Restoration record{{ $restorationCount === 1 ? '' : 's' }}</p>
                    <a href="{{ route('curator.museums.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Manage artifacts &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="5">
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: {{ $pendingDonations > 0 ? '#d97706' : 'var(--brass-700)' }}; font-size: var(--text-3xl);">{{ $pendingDonations }}</div>
                    <p style="margin-top: var(--space-2);">Donation{{ $pendingDonations === 1 ? '' : 's' }} to your museum{{ $pendingDonations === 1 ? '' : 's' }}</p>
                    <a href="{{ route('admin.donations.index', ['status' => 'pending']) }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">View donations &rarr;</a>
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
