@extends('layouts.app')

@section('title', 'Admin Dashboard — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            @include('dashboards.partials.verification-banner')

            <x-tag>Administrator Dashboard</x-tag>
            <h1 style="margin-top: var(--space-4);">Welcome back, {{ $user->name }}</h1>
            <p style="max-width: 560px;">User accounts, museums, and artifacts are live. Verification and auction
                metrics surface here as those modules go live.</p>

            <div class="grid grid-4" style="margin-top: var(--space-8);">
                <x-card data-reveal>
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ number_format($totalUsers) }}</div>
                    <p style="margin-top: var(--space-2);">Total registered users</p>
                </x-card>
                @foreach (['admin' => 'Administrators', 'curator' => 'Curators', 'collector' => 'Collectors', 'visitor' => 'Visitors'] as $role => $label)
                    <x-card data-reveal>
                        <span class="av-card__eyebrow">Live</span>
                        <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ number_format($roleCounts[$role] ?? 0) }}</div>
                        <p style="margin-top: var(--space-2);">{{ $label }}</p>
                    </x-card>
                @endforeach
            </div>

            <div class="grid grid-4" style="margin-top: var(--space-6);">
                <x-card data-reveal>
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ number_format($totalMuseums) }}</div>
                    <p style="margin-top: var(--space-2);">Museum{{ $totalMuseums === 1 ? '' : 's' }} on the platform</p>
                    <a href="{{ route('curator.museums.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">View all &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="1">
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ number_format($totalArtifacts) }}</div>
                    <p style="margin-top: var(--space-2);">Artifact{{ $totalArtifacts === 1 ? '' : 's' }} archived</p>
                    <a href="{{ route('artifacts.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">View all &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="2">
                    <span class="av-card__eyebrow">Coming in Phase 11</span>
                    <h3>Verification queue</h3>
                    <p>Pending and completed verification requests.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="3">
                    <span class="av-card__eyebrow">Coming in Phase 13–14</span>
                    <h3>Auctions &amp; revenue</h3>
                    <p>Active auctions and platform revenue.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="4">
                    <span class="av-card__eyebrow">Live</span>
                    <h3>Activity log</h3>
                    <p>A searchable feed of platform-wide actions.</p>
                    <a href="{{ route('admin.activity-logs.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">View activity log &rarr;</a>
                </x-card>
            </div>

            <div class="grid grid-2 mt-8">
                <x-card>
                    <span class="av-card__eyebrow">Manage</span>
                    <h3>Users &amp; roles</h3>
                    <p>View every account and change a user's role.</p>
                    <a href="{{ route('admin.users.index') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Manage users &rarr;</a>
                </x-card>
                <x-card>
                    <span class="av-card__eyebrow">Account</span>
                    <h3>Your profile</h3>
                    <p>Update your name, email, avatar, and password.</p>
                    <a href="{{ route('profile.edit') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Go to profile &rarr;</a>
                </x-card>
            </div>
        </div>
    </section>
@endsection
