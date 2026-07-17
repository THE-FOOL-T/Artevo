@php use Illuminate\Support\Str; @endphp

@extends('layouts.app')

@section('title', 'Curated Exhibitions & Galleries — Artevo Archive')
@section('meta_description', 'Explore curated digital and physical exhibitions from verified world-class museums on Artevo.')

@section('content')
{{-- Editorial Header / Hero --}}
<section style="background: var(--ink-900); color: var(--parchment-100); padding: var(--space-16) 0 var(--space-14); position: relative; overflow: hidden; border-bottom: 1px solid rgba(169,129,46,0.25);">
    <div style="position: absolute; inset: 0; background: radial-gradient(circle at 25% 30%, rgba(169,129,46,0.18) 0%, transparent 60%); pointer-events: none;"></div>
    <div class="container" style="position: relative; z-index: 2; max-width: 960px; text-align: center;">
        <span class="av-tag" style="background: rgba(169,129,46,0.2); color: var(--brass-500); border: 1px solid rgba(169,129,46,0.35); text-transform: uppercase; letter-spacing: 0.12em; font-size: 0.75rem; padding: 4px 14px;">
            Museum Exhibitions
        </span>
        <h1 style="font-family: var(--font-display); font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 400; line-height: 1.15; margin: var(--space-4) 0 var(--space-4); color: #fff;">
            Curated <em style="font-style: italic; color: var(--brass-500);">Exhibitions</em>
        </h1>
        <p style="font-size: clamp(1rem, 2vw, 1.25rem); color: var(--stone-400); max-width: 680px; margin: 0 auto; line-height: 1.6;">
            Immersive, story-driven galleries from verified cultural institutions across the globe — where extraordinary artifacts unite into cohesive historical narratives.
        </p>
    </div>
</section>

<section class="av-section av-section--white" style="padding-top: var(--space-10); padding-bottom: var(--space-16);">
    <div class="container">
        
        {{-- Search and Filter Bar --}}
        <form method="GET" action="{{ route('exhibitions.index') }}" style="margin-bottom: var(--space-12); display: flex; flex-wrap: wrap; gap: var(--space-4); align-items: center; justify-content: space-between; padding: var(--space-4) var(--space-6); background: var(--color-bg-alt); border-radius: var(--radius-lg); border: 1px solid var(--color-border); box-shadow: var(--shadow-sm);">
            <div style="flex: 1; min-width: 280px; position: relative;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 1.1rem; top: 50%; transform: translateY(-50%); color: var(--stone-400); pointer-events: none;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search exhibitions by title, theme, or host museum…" style="width: 100%; padding: 0.9rem 1.2rem 0.9rem 3rem; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-family: var(--font-body); font-size: 0.95rem; outline: none; background: var(--white); transition: border-color var(--duration-fast) var(--ease-out);">
            </div>

            <div style="display: flex; gap: var(--space-4); align-items: center; padding-right: var(--space-2);">
                <label style="display: flex; align-items: center; gap: var(--space-2); cursor: pointer; font-weight: 500; color: var(--color-text); font-size: 0.92rem;">
                    <input type="checkbox" name="active_now" value="1" @checked(request()->boolean('active_now')) style="width: 18px; height: 18px; accent-color: var(--brass-600); cursor: pointer;">
                    Active Now
                </label>
                
                @if(request('search') || request('active_now'))
                    <a href="{{ route('exhibitions.index') }}" class="av-btn" style="background: var(--white); color: var(--ink-900); border: 1px solid var(--color-border); padding: 0.85rem 1.4rem;">Reset</a>
                @endif
                <button type="submit" class="av-btn av-btn--primary" style="padding: 0.85rem 1.8rem; font-weight: 600;">Find Galleries</button>
            </div>
        </form>

        {{-- Featured Spotlight --}}
        @if($featured->isNotEmpty() && ! request('search') && ! request('active_now'))
            <div style="margin-bottom: var(--space-14);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-6); border-bottom: 2px solid var(--ink-900); padding-bottom: var(--space-3);">
                    <h2 style="font-family: var(--font-display); font-size: 1.75rem; margin: 0; color: var(--ink-900);">Headline Exhibitions</h2>
                    <span style="font-size: 0.85rem; color: var(--stone-500); font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em;">Featured Galleries</span>
                </div>
                
                <div class="grid grid-2" style="gap: var(--space-8);">
                    @foreach($featured as $feat)
                    <article class="av-card" style="display: flex; flex-direction: column; border-radius: var(--radius-lg); overflow: hidden; height: 100%; box-shadow: var(--shadow-md); border: 1px solid var(--color-border); transition: transform 0.3s var(--ease-out), box-shadow 0.3s var(--ease-out);">
                        <a href="{{ route('exhibitions.show', $feat) }}" style="display: block; position: relative; aspect-ratio: 16/9; overflow: hidden; background: var(--color-bg-alt);" tabindex="-1" aria-hidden="true">
                            @if($feat->coverImageUrl())
                                <img src="{{ $feat->coverImageUrl() }}" alt="{{ $feat->name }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s var(--ease-out);" loading="lazy">
                            @else
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; opacity:.3; background: var(--porcelain-100);">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="m3 9 9-6 9 6"/><path d="M9 22V12h6v10"/></svg>
                                </div>
                            @endif
                            <div style="position: absolute; top: var(--space-4); left: var(--space-4); display: flex; gap: var(--space-2);">
                                <span class="av-tag av-tag--gold" style="box-shadow: var(--shadow-md);">★ Featured</span>
                                @if($feat->isActive())
                                    <span class="av-tag" style="background: #10B981; color: #fff; font-weight: 700; box-shadow: var(--shadow-md);">🟢 Active Gallery</span>
                                @endif
                            </div>
                        </a>
                        <div style="padding: var(--space-6); display: flex; flex-direction: column; flex: 1; background: var(--white);">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-2);">
                                <span style="font-size: 0.8rem; color: var(--brass-700); font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;">
                                    🏛 {{ $feat->museum->name }}
                                </span>
                                @if($feat->starts_at)
                                    <span style="font-size: 0.8rem; color: var(--stone-500); font-weight: 500;">
                                        📅 {{ $feat->starts_at->format('M j, Y') }}@if($feat->ends_at) — {{ $feat->ends_at->format('M j, Y') }}@endif
                                    </span>
                                @endif
                            </div>
                            <h3 style="font-family: var(--font-display); font-size: 1.6rem; margin: 0 0 var(--space-3); line-height: 1.25;">
                                <a href="{{ route('exhibitions.show', $feat) }}" style="color: var(--ink-900); text-decoration: none;">{{ $feat->name }}</a>
                            </h3>
                            @if($feat->description)
                                <p style="font-size: 0.95rem; color: var(--color-text-muted); line-height: 1.6; margin: 0 0 var(--space-6); flex: 1;">
                                    {{ Str::limit($feat->description, 140) }}
                                </p>
                            @endif
                            <div style="margin-top: auto; padding-top: var(--space-4); border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 0.85rem; font-weight: 600; color: var(--ink-800); display: flex; align-items: center; gap: 6px;">
                                    📍 {{ $feat->location ?? 'Online / Museum Gallery' }}
                                </span>
                                <a href="{{ route('exhibitions.show', $feat) }}" class="av-btn av-btn--outline-dark" style="padding: 6px 16px; font-size: 0.82rem;">Enter Exhibition &rarr;</a>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- All Exhibitions Grid --}}
        @if($exhibitions->isEmpty())
            <div style="text-align:center; padding: var(--space-16) var(--space-6); color: var(--color-text-muted); background: var(--color-bg-alt); border-radius: var(--radius-lg); border: 1px dashed var(--color-border);">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="margin: 0 auto var(--space-4); color: var(--brass-600); opacity: 0.6;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="m3 9 9-6 9 6"/><path d="M9 22V12h6v10"/></svg>
                <h3 style="font-family: var(--font-display); font-size: 1.5rem; margin-bottom: var(--space-2); color: var(--ink-900);">No exhibitions matched your criteria</h3>
                <p style="max-width: 440px; margin: 0 auto;">{{ request('search') || request('active_now') ? 'Try clearing your search keyword or active checkbox to browse all available museum galleries.' : 'New curated exhibitions are being finalized by our partner institutions.' }}</p>
                @if(request('search') || request('active_now'))
                    <a href="{{ route('exhibitions.index') }}" class="av-btn av-btn--primary" style="margin-top: var(--space-6);">Clear Filters</a>
                @endif
            </div>
        @else
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-8); border-bottom: 2px solid var(--ink-900); padding-bottom: var(--space-3);">
                <h2 style="font-family: var(--font-display); font-size: 1.75rem; margin: 0; color: var(--ink-900);">
                    {{ request('search') || request('active_now') ? 'Filtered Exhibitions' : 'Explore All Exhibitions' }}
                </h2>
                <span style="font-size: 0.9rem; font-weight: 500; color: var(--stone-600);">
                    Showing {{ $exhibitions->firstItem() }}–{{ $exhibitions->lastItem() }} of {{ $exhibitions->total() }} exhibitions
                </span>
            </div>

            <div class="grid grid-3" style="gap: var(--space-8);">
                @foreach($exhibitions as $exhibition)
                <article class="av-card" style="display: flex; flex-direction: column; overflow: hidden; border-radius: var(--radius-lg); height: 100%; border: 1px solid var(--color-border); background: var(--white); transition: transform 0.3s var(--ease-out), box-shadow 0.3s var(--ease-out);">
                    {{-- Cover --}}
                    <a href="{{ route('exhibitions.show', $exhibition) }}" style="display:block; aspect-ratio: 16/9; overflow:hidden; background: var(--color-bg-alt); position: relative;" tabindex="-1" aria-hidden="true">
                        @if($exhibition->coverImageUrl())
                            <img src="{{ $exhibition->coverImageUrl() }}" alt="{{ $exhibition->name }}" style="width:100%; height:100%; object-fit:cover; transition: transform 0.6s var(--ease-out);" loading="lazy">
                        @else
                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; opacity:.3;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="m3 9 9-6 9 6"/><path d="M9 22V12h6v10"/></svg>
                            </div>
                        @endif
                        <div style="position: absolute; top: var(--space-3); left: var(--space-3); display: flex; gap: var(--space-2);">
                            @if($exhibition->is_featured)
                                <span class="av-tag av-tag--gold">Featured</span>
                            @endif
                            @if($exhibition->isActive())
                                <span class="av-tag" style="background: #10B981; color: #fff; font-weight: 700;">Active Now</span>
                            @endif
                        </div>
                    </a>

                    <div style="padding: var(--space-6); flex: 1; display: flex; flex-direction: column; gap: var(--space-2);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.75rem; color: var(--brass-700); font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">
                                🏛 {{ $exhibition->museum->name }}
                            </span>
                        </div>

                        <h3 style="font-family: var(--font-display); font-size: 1.35rem; margin: var(--space-1) 0 0; line-height: 1.3;">
                            <a href="{{ route('exhibitions.show', $exhibition) }}" style="color: var(--ink-900); text-decoration: none;">
                                {{ $exhibition->name }}
                            </a>
                        </h3>

                        @if($exhibition->description)
                            <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6; flex: 1; margin: var(--space-2) 0 0;">
                                {{ Str::limit($exhibition->description, 110) }}
                            </p>
                        @endif

                        <div style="margin-top: auto; padding-top: var(--space-4); border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: space-between; font-size: 0.85rem; color: var(--stone-600);">
                            <span>
                                @if($exhibition->starts_at)
                                    📅 {{ $exhibition->starts_at->format('M j') }}@if($exhibition->ends_at) — {{ $exhibition->ends_at->format('M j, Y') }}@endif
                                @else
                                    Permanent Gallery
                                @endif
                            </span>
                            <a href="{{ route('exhibitions.show', $exhibition) }}" style="color: var(--brass-700); font-weight: 600; text-decoration: none;">Visit Show &rarr;</a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <div style="margin-top: var(--space-12);">
                {{ $exhibitions->links() }}
            </div>
        @endif
    </div>
</section>
@endsection

@push('styles')
<style>
.av-card:hover img { transform: scale(1.06); }
.av-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-5px); border-color: rgba(169, 129, 46, 0.45) !important; }
input[type="search"]:focus { border-color: var(--brass-600) !important; box-shadow: 0 0 0 3px rgba(169, 129, 46, 0.15) !important; }
</style>
@endpush
