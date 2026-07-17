@php use Illuminate\Support\Str; @endphp

@extends('layouts.app')

@section('title', $exhibition->name . ' — Artevo')
@section('meta_description', $exhibition->tagline ?: Str::limit(strip_tags($exhibition->description), 160))

@section('content')

{{-- Cover hero --}}
<div style="position: relative; height: clamp(300px, 50vh, 560px); overflow: hidden; background: var(--color-ink);">
    @if($exhibition->coverImageUrl())
        <img src="{{ $exhibition->coverImageUrl() }}"
             alt="{{ $exhibition->name }}"
             style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:.65;">
    @endif
    <div style="position:absolute; inset:0; background: linear-gradient(to top, var(--color-ink) 0%, rgba(15,12,20,.3) 50%, transparent 100%);"></div>

    <div class="container" style="position:relative; height:100%; display:flex; flex-direction:column; justify-content:flex-end; padding-bottom: var(--space-8);">
        <p style="color: var(--color-gold); font-weight:600; font-size:.8rem; text-transform:uppercase; letter-spacing:.08em; margin-bottom: var(--space-2);">
            <a href="{{ route('museums.show', $exhibition->museum) }}" style="color: inherit; text-decoration:none;">{{ $exhibition->museum->name }}</a>
        </p>
        <h1 style="color: var(--color-parchment); font-size: clamp(1.75rem, 5vw, 3rem); line-height: 1.15; margin: 0 0 var(--space-2);">{{ $exhibition->name }}</h1>
        @if($exhibition->tagline)
            <p style="color: rgba(248,245,239,.8); font-size: 1.05rem; max-width: 540px;">{{ $exhibition->tagline }}</p>
        @endif
    </div>
</div>

{{-- Meta bar --}}
<div style="background: var(--color-surface-2); border-bottom: 1px solid var(--color-border);">
    <div class="container" style="padding-top: var(--space-4); padding-bottom: var(--space-4);">
        <div class="flex" style="flex-wrap: wrap; gap: var(--space-6); align-items: center; font-size: .875rem; color: var(--color-muted);">
            @if($exhibition->starts_at)
                <span>📅 <strong>{{ $exhibition->starts_at->format('M j, Y') }}</strong>@if($exhibition->ends_at) — {{ $exhibition->ends_at->format('M j, Y') }}@endif</span>
            @endif
            @if($exhibition->location)
                <span>📍 {{ $exhibition->location }}</span>
            @endif
            <span>{{ $exhibition->admissionLabel() }}</span>
            <span>🗂 {{ $exhibition->sections->count() }} {{ Str::plural('section', $exhibition->sections->count()) }}</span>
            <span>👁 {{ number_format($exhibition->views_count) }} {{ Str::plural('view', $exhibition->views_count) }}</span>

            <div class="flex" style="gap: var(--space-2); margin-left: auto; align-items: center;">
                @auth
                    @if(auth()->user()->favoritedExhibitions()->where('exhibition_id', $exhibition->id)->exists())
                        <form method="POST" action="{{ route('exhibitions.favorite.destroy', $exhibition) }}" style="display:inline; margin-right: var(--space-2);">
                            @csrf
                            @method('DELETE')
                            <button class="av-btn av-btn--outline" style="font-size:.75rem; padding: 2px 8px; color: #ef4444; border-color: rgba(239,68,68,.3);">
                                ♥ Favorited
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('exhibitions.favorite.store', $exhibition) }}" style="display:inline; margin-right: var(--space-2);">
                            @csrf
                            <button class="av-btn av-btn--outline" style="font-size:.75rem; padding: 2px 8px; color: var(--ink-500); border-color: var(--color-border);">
                                ♡ Favorite
                            </button>
                        </form>
                    @endif
                @endauth

                @if($exhibition->isActive())
                    <span class="av-tag av-tag--success">Active Now</span>
                @endif
                @if($exhibition->is_featured)
                    <span class="av-tag av-tag--gold">Featured</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Content --}}
<div class="container" style="padding-top: var(--space-10); padding-bottom: var(--space-16);">
    <div style="display: grid; grid-template-columns: 1fr 300px; gap: var(--space-10); align-items: start;">

        {{-- Main: description + sections --}}
        <div>
            @if($exhibition->description)
            <section style="margin-bottom: var(--space-10);" aria-label="Exhibition description">
                <div style="font-size: 1.05rem; line-height: 1.8; color: var(--color-body); max-width: 680px;">
                    {!! nl2br(e($exhibition->description)) !!}
                </div>
            </section>
            @endif

            {{-- Sections --}}
            @foreach($exhibition->sections as $section)
            <section class="av-exhibition-section" aria-labelledby="section-{{ $section->id }}-heading" style="margin-bottom: var(--space-12);">
                <h2 id="section-{{ $section->id }}-heading" style="font-size: 1.5rem; margin-bottom: var(--space-3); padding-bottom: var(--space-3); border-bottom: 2px solid var(--color-gold);">
                    {{ $section->title }}
                </h2>

                @if($section->body)
                <div style="font-size: 1rem; line-height: 1.8; color: var(--color-body); margin-bottom: var(--space-6); max-width: 680px;">
                    {!! $section->body !!}
                </div>
                @endif

                @if($section->artifacts->isNotEmpty())
                <div class="grid grid-3" style="gap: var(--space-6);">
                    @foreach($section->artifacts as $artifact)
                    <article class="av-card av-card--media" style="display:flex; flex-direction:column; border-radius: var(--radius-lg); overflow:hidden; border: 1px solid var(--color-border); background: var(--white);">
                        <a href="{{ route('artifacts.show', $artifact) }}" style="display:block; position:relative; aspect-ratio: 16/9; overflow:hidden; background: var(--color-bg-alt);" tabindex="-1" aria-hidden="true">
                            <img src="{{ $artifact->primaryImageUrl() }}" alt="{{ $artifact->name }}" style="width:100%; height:100%; object-fit:cover; transition: transform 0.6s var(--ease-out);" loading="lazy">
                            <div style="position: absolute; top: var(--space-3); left: var(--space-3); display: flex; gap: var(--space-2);">
                                <x-tag style="background: rgba(15,12,20,0.85); color: var(--parchment-100); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(4px);">
                                    {{ $artifact->category?->name ?? 'Artifact' }}
                                </x-tag>
                            </div>
                        </a>
                        <div style="padding: var(--space-5); display: flex; flex-direction: column; flex: 1; gap: var(--space-2);">
                            <div style="font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brass-700);">
                                🏛 {{ $artifact->museum->name ?? 'Exhibition Archive' }}
                            </div>
                            <h3 style="font-size: 1.2rem; font-family: var(--font-display); margin: 0; line-height: 1.3;">
                                <a href="{{ route('artifacts.show', $artifact) }}" style="color: var(--color-text); text-decoration: none;">
                                    {{ $artifact->name }}
                                </a>
                            </h3>
                            @if($artifact->era || $artifact->civilization)
                                <p style="font-size: 0.85rem; color: var(--stone-500); margin: 0;">
                                    {{ $artifact->civilization ?: $artifact->era }}
                                </p>
                            @endif
                            <div style="margin-top: auto; padding-top: var(--space-4); border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 0.82rem; color: var(--stone-500);">Verified Exhibition Lot</span>
                                <a href="{{ route('artifacts.show', $artifact) }}" style="color: var(--brass-700); font-weight: 600; font-size: 0.85rem; text-decoration: none;">View Lot &rarr;</a>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
                @endif
            </section>
            @endforeach
        </div>

        {{-- Sidebar: museum card + related --}}
        <aside style="position: sticky; top: var(--space-8);">
            {{-- Museum card --}}
            <div class="av-card" style="padding: var(--space-5); margin-bottom: var(--space-6);">
                <p style="font-size:.75rem; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--color-muted); margin-bottom: var(--space-3);">Presented by</p>
                @if($exhibition->museum->logoUrl())
                    <img src="{{ $exhibition->museum->logoUrl() }}" alt="{{ $exhibition->museum->name }} logo" style="height: 40px; object-fit: contain; margin-bottom: var(--space-3);">
                @endif
                <h3 style="font-size: 1rem; margin: 0 0 var(--space-2);">{{ $exhibition->museum->name }}</h3>
                @if($exhibition->museum->city)
                    <p style="font-size:.8rem; color:var(--color-muted); margin-bottom: var(--space-3);">{{ $exhibition->museum->city }}@if($exhibition->museum->country), {{ $exhibition->museum->country }}@endif</p>
                @endif
                <a href="{{ route('museums.show', $exhibition->museum) }}" class="av-btn av-btn--outline" style="width:100%; text-align:center;">View Museum</a>
            </div>

            {{-- Related exhibitions --}}
            @if($related->isNotEmpty())
            <div>
                <p style="font-size:.75rem; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--color-muted); margin-bottom: var(--space-3);">More from this museum</p>
                <div style="display:flex; flex-direction:column; gap: var(--space-3);">
                    @foreach($related as $rel)
                    <a href="{{ route('exhibitions.show', $rel) }}" class="av-card" style="display:flex; gap: var(--space-3); text-decoration:none; padding: var(--space-3); align-items:center;">
                        @if($rel->coverImageUrl())
                            <img src="{{ $rel->coverImageUrl() }}" alt="{{ $rel->name }}" style="width:60px; height:60px; object-fit:cover; border-radius:var(--radius-sm); flex-shrink:0;">
                        @else
                            <div style="width:60px; height:60px; background:var(--color-surface-2); border-radius:var(--radius-sm); flex-shrink:0; display:flex; align-items:center; justify-content:center;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-muted)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="m3 9 9-6 9 6"/></svg>
                            </div>
                        @endif
                        <div>
                            <p style="font-size:.85rem; color:var(--color-heading); margin:0; font-weight:500; line-height:1.3;">{{ Str::limit($rel->name, 40) }}</p>
                            <p style="font-size:.75rem; color:var(--color-muted); margin:var(--space-1) 0 0;">{{ $rel->sections_count }} {{ Str::plural('section', $rel->sections_count) }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </aside>
    </div>
</div>
@endsection

@push('styles')
<style>
.av-exhibition-section { scroll-margin-top: var(--space-8); }
.av-card:hover img { transform: scale(1.04); }
.av-tag--success { background: rgba(16,185,129,.12); color: #10b981; border: 1px solid rgba(16,185,129,.25); }
@media (max-width: 900px) {
    .container > div[style*="grid-template-columns"] { grid-template-columns: 1fr !important; }
    aside { position: static !important; }
}
</style>
@endpush
