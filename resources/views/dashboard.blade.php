@extends('layouts.app')

@section('title', 'Dashboard — Artevo')

@section('content')

    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            @if (request('verified') === '1')
                <x-alert type="success">Your email has been verified — welcome to Artevo.</x-alert>
            @endif

            @unless (auth()->user()->hasVerifiedEmail())
                <x-alert type="error">
                    Your email address isn't verified yet.
                    <form method="POST" action="{{ route('verification.send') }}" style="display: inline;">
                        @csrf
                        <button type="submit" style="text-decoration: underline; background: none; border: none; padding: 0; font: inherit; color: inherit; cursor: pointer;">Resend the verification email.</button>
                    </form>
                </x-alert>
            @endunless

            <x-tag>Dashboard</x-tag>
            <h1 style="margin-top: var(--space-4);">Welcome back, {{ auth()->user()->name }}</h1>
            <p style="max-width: 560px;">
                This is a placeholder dashboard. Role-specific views for Administrators, Curators, Collectors
                and Visitors — with real archive/verification/auction activity — are built in Phase 3 and
                beyond.
            </p>

            <div class="grid grid-3" style="margin-top: var(--space-8);">
                <x-card data-reveal>
                    <span class="av-card__eyebrow">Account</span>
                    <h3>Your profile</h3>
                    <p>Update your name, email, avatar, and password.</p>
                    <a href="{{ route('profile.edit') }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Go to profile &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="1">
                    <span class="av-card__eyebrow">Coming in Phase 3</span>
                    <h3>Role &amp; permissions</h3>
                    <p>Administrator, Curator, Collector and Visitor roles, with dedicated dashboards.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="2">
                    <span class="av-card__eyebrow">Coming later</span>
                    <h3>Your archive</h3>
                    <p>Museums, artifacts, collections, exhibitions and auctions will surface here.</p>
                </x-card>
            </div>
        </div>
    </section>

@endsection
