@extends('layouts.app')

@section('title', "{$museum->name} Dashboard — Artevo")

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            <div class="flex-between">
                <div>
                    <x-tag>Museum Dashboard</x-tag>
                    <h1 style="margin-top: var(--space-4); margin-bottom: var(--space-2);">{{ $museum->name }}</h1>
                    <x-museum-verification-badge :museum="$museum" />
                </div>
                <div class="flex gap-3">
                    <x-button href="{{ route('curator.museums.edit', $museum) }}" variant="outline-dark">Edit profile</x-button>
                    <x-button href="{{ route('museums.show', $museum) }}" variant="primary">View public page</x-button>
                </div>
            </div>

            <div class="grid grid-4" style="margin-top: var(--space-8);">
                <x-card data-reveal>
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ number_format($museum->views_count) }}</div>
                    <p style="margin-top: var(--space-2);">Profile visitors</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="1">
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ $museum->images_count }}</div>
                    <p style="margin-top: var(--space-2);">Gallery images</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="2">
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ $museum->contacts_count }}</div>
                    <p style="margin-top: var(--space-2);">Contact entries</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="3">
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ $museum->artifacts_count }}</div>
                    <p style="margin-top: var(--space-2);">Artifacts</p>
                    <a href="{{ route('curator.museums.artifacts.index', $museum) }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">Manage &rarr;</a>
                </x-card>
                <x-card data-reveal data-reveal-delay="4">
                    <span class="av-card__eyebrow">Live</span>
                    <div class="av-stat-num" style="color: var(--brass-700); font-size: var(--text-3xl);">{{ $museum->foundation_year ? now()->year - $museum->foundation_year : '—' }}</div>
                    <p style="margin-top: var(--space-2);">Years since founding</p>
                </x-card>
            </div>

            <div class="grid grid-3" style="margin-top: var(--space-6);">
                <x-card data-reveal>
                    <span class="av-card__eyebrow">Coming in Phase 10</span>
                    <h3>Exhibitions</h3>
                    <p>Active and past exhibitions this museum has run.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="1">
                    <span class="av-card__eyebrow">Coming in Phase 13–14</span>
                    <h3>Auctions</h3>
                    <p>Ongoing auctions tied to this museum's artifacts.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="2">
                    <span class="av-card__eyebrow">Coming in Phase 15</span>
                    <h3>Restoration records</h3>
                    <p>Conservation work logged against this museum's pieces.</p>
                </x-card>
            </div>
        </div>
    </section>
@endsection
