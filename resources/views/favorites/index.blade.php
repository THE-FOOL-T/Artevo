@extends('layouts.app')

@section('title', 'My Favorites — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10); padding-bottom: var(--space-16);">
        <div class="container">
            <a href="{{ route('dashboard') }}" style="display:inline-block; margin-bottom:var(--space-6); color:var(--ink-500); font-size:var(--text-sm); font-weight:500;">
                &larr; Back to Dashboard
            </a>

            <div style="margin-bottom: var(--space-12);">
                <x-tag>My Favorites</x-tag>
                <h1 style="font-family: var(--font-display); font-size: clamp(2rem, 4vw, 3rem); font-weight: 400; line-height: 1.15; margin: var(--space-4) 0; color: var(--ink-900);">
                    Saved <em style="font-style: italic; color: var(--brass-700);">items</em>
                </h1>
                <p style="font-size: clamp(1rem, 2vw, 1.25rem); color: var(--stone-500); max-width: 680px; margin: 0; line-height: 1.6;">
                    Artifacts, collections, and exhibitions you have bookmarked.
                </p>
            </div>

            {{-- ARTIFACTS --}}
            <div style="margin-bottom: var(--space-16);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-6); border-bottom: 2px solid var(--ink-900); padding-bottom: var(--space-3);">
                    <h2 style="font-family: var(--font-display); font-size: 1.75rem; margin: 0; color: var(--ink-900);">Artifacts</h2>
                    <span style="font-size: 0.85rem; color: var(--stone-500); font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em;">{{ $artifacts->count() }} {{ \Illuminate\Support\Str::plural('Item', $artifacts->count()) }}</span>
                </div>
                
                @if ($artifacts->isEmpty())
                    <div style="text-align:center; padding: var(--space-12) var(--space-6); color: var(--color-text-muted); background: var(--color-bg-alt); border-radius: var(--radius-lg); border: 1px dashed var(--color-border);">
                        <p style="margin: 0;">You haven't saved any artifacts yet.</p>
                    </div>
                @else
                    <div class="grid grid-4" style="gap: var(--space-6);">
                        @foreach ($artifacts as $artifact)
                            <x-card class="av-card--media" data-reveal>
                                <a href="{{ route('artifacts.show', $artifact) }}" tabindex="-1" aria-hidden="true" style="display: block;">
                                    @if($artifact->primaryImage())
                                        <img src="{{ $artifact->primaryImageUrl() }}" alt="{{ $artifact->name }}" class="av-card--media__image" loading="lazy">
                                    @else
                                        <div style="aspect-ratio: 1; background: var(--porcelain-100); display: flex; align-items: center; justify-content: center; color: var(--stone-400);">
                                            No Image
                                        </div>
                                    @endif
                                </a>
                                <div class="av-card--media__body">
                                    @if($artifact->category)
                                        <x-tag>{{ $artifact->category->name }}</x-tag>
                                    @endif
                                    <h3 class="mt-4" style="font-size: var(--text-lg); font-family: var(--font-display); line-height: 1.25; margin-bottom: var(--space-2);">
                                        <a href="{{ route('artifacts.show', $artifact) }}" style="text-decoration: none; color: inherit;">{{ $artifact->name }}</a>
                                    </h3>
                                    <p style="margin: 0 0 var(--space-4); font-size: var(--text-sm); color: var(--ink-500);">{{ $artifact->origin_period }}</p>
                                    <div style="margin-top: auto;">
                                        <a href="{{ route('artifacts.show', $artifact) }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">View Details &rarr;</a>
                                    </div>
                                </div>
                            </x-card>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- COLLECTIONS --}}
            <div style="margin-bottom: var(--space-16);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-6); border-bottom: 2px solid var(--ink-900); padding-bottom: var(--space-3);">
                    <h2 style="font-family: var(--font-display); font-size: 1.75rem; margin: 0; color: var(--ink-900);">Collections</h2>
                    <span style="font-size: 0.85rem; color: var(--stone-500); font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em;">{{ $collections->count() }} {{ \Illuminate\Support\Str::plural('Collection', $collections->count()) }}</span>
                </div>
                
                @if ($collections->isEmpty())
                    <div style="text-align:center; padding: var(--space-12) var(--space-6); color: var(--color-text-muted); background: var(--color-bg-alt); border-radius: var(--radius-lg); border: 1px dashed var(--color-border);">
                        <p style="margin: 0;">You haven't saved any collections yet.</p>
                    </div>
                @else
                    <div class="grid grid-3" style="gap: var(--space-6);">
                        @foreach ($collections as $collection)
                            <article class="av-card" data-reveal style="display: flex; flex-direction: column; border-radius: var(--radius-lg); overflow: hidden; height: 100%; box-shadow: var(--shadow-sm); border: 1px solid var(--color-border);">
                                <a href="{{ route('collections.show', $collection) }}" style="display: block; position: relative; aspect-ratio: 16/9; overflow: hidden; background: var(--color-bg-alt);" tabindex="-1" aria-hidden="true">
                                    @if($collection->coverImageUrl())
                                        <img src="{{ $collection->coverImageUrl() }}" alt="{{ $collection->name }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s var(--ease-out);" loading="lazy">
                                    @else
                                        <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; opacity:.3; background: var(--porcelain-100);">
                                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"/><polyline points="14 2 14 8 20 8"/><path d="M2 15h10"/><path d="m9 18 3-3-3-3"/></svg>
                                        </div>
                                    @endif
                                </a>
                                <div style="padding: var(--space-6); display: flex; flex-direction: column; flex: 1; background: var(--white);">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-2);">
                                        <span style="font-size: 0.8rem; color: var(--brass-700); font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;">
                                            🏛 {{ $collection->museum?->name ?? $collection->collector?->name ?? 'Private Collection' }}
                                        </span>
                                    </div>
                                    <h3 style="font-family: var(--font-display); font-size: 1.4rem; margin: 0 0 var(--space-3); line-height: 1.25;">
                                        <a href="{{ route('collections.show', $collection) }}" style="color: var(--ink-900); text-decoration: none;">{{ $collection->name }}</a>
                                    </h3>
                                    @if($collection->description)
                                        <p style="font-size: 0.95rem; color: var(--color-text-muted); line-height: 1.6; margin: 0 0 var(--space-4); flex: 1;">
                                            {{ \Illuminate\Support\Str::limit($collection->description, 100) }}
                                        </p>
                                    @endif
                                    <div style="margin-top: auto; padding-top: var(--space-4); border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: space-between;">
                                        <a href="{{ route('collections.show', $collection) }}" class="av-btn av-btn--outline-dark" style="padding: 6px 16px; font-size: 0.82rem;">View Collection &rarr;</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- EXHIBITIONS --}}
            <div style="margin-bottom: var(--space-12);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-6); border-bottom: 2px solid var(--ink-900); padding-bottom: var(--space-3);">
                    <h2 style="font-family: var(--font-display); font-size: 1.75rem; margin: 0; color: var(--ink-900);">Exhibitions</h2>
                    <span style="font-size: 0.85rem; color: var(--stone-500); font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em;">{{ $exhibitions->count() }} {{ \Illuminate\Support\Str::plural('Exhibition', $exhibitions->count()) }}</span>
                </div>
                
                @if ($exhibitions->isEmpty())
                    <div style="text-align:center; padding: var(--space-12) var(--space-6); color: var(--color-text-muted); background: var(--color-bg-alt); border-radius: var(--radius-lg); border: 1px dashed var(--color-border);">
                        <p style="margin: 0;">You haven't saved any exhibitions yet.</p>
                    </div>
                @else
                    <div class="grid grid-2" style="gap: var(--space-6);">
                        @foreach ($exhibitions as $exhibition)
                            <article class="av-card" data-reveal style="display: flex; flex-direction: column; border-radius: var(--radius-lg); overflow: hidden; height: 100%; box-shadow: var(--shadow-sm); border: 1px solid var(--color-border);">
                                <a href="{{ route('exhibitions.show', $exhibition) }}" style="display: block; position: relative; aspect-ratio: 16/9; overflow: hidden; background: var(--color-bg-alt);" tabindex="-1" aria-hidden="true">
                                    @if($exhibition->coverImageUrl())
                                        <img src="{{ $exhibition->coverImageUrl() }}" alt="{{ $exhibition->title }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s var(--ease-out);" loading="lazy">
                                    @else
                                        <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; opacity:.3; background: var(--porcelain-100);">
                                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"/><polyline points="14 2 14 8 20 8"/><path d="M2 15h10"/><path d="m9 18 3-3-3-3"/></svg>
                                        </div>
                                    @endif
                                </a>
                                <div style="padding: var(--space-6); display: flex; flex-direction: column; flex: 1; background: var(--white);">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-2);">
                                        <span style="font-size: 0.8rem; color: var(--brass-700); font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;">
                                            🏛 {{ $exhibition->museum?->name ?? 'Museum Exhibition' }}
                                        </span>
                                    </div>
                                    <h3 style="font-family: var(--font-display); font-size: 1.5rem; margin: 0 0 var(--space-3); line-height: 1.25;">
                                        <a href="{{ route('exhibitions.show', $exhibition) }}" style="color: var(--ink-900); text-decoration: none;">{{ $exhibition->title }}</a>
                                    </h3>
                                    @if($exhibition->description)
                                        <p style="font-size: 0.95rem; color: var(--color-text-muted); line-height: 1.6; margin: 0 0 var(--space-4); flex: 1;">
                                            {{ \Illuminate\Support\Str::limit($exhibition->description, 120) }}
                                        </p>
                                    @endif
                                    <div style="margin-top: auto; padding-top: var(--space-4); border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: space-between;">
                                        <a href="{{ route('exhibitions.show', $exhibition) }}" class="av-btn av-btn--outline-dark" style="padding: 6px 16px; font-size: 0.82rem;">View Exhibition &rarr;</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
