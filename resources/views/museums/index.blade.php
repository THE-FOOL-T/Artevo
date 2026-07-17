@php use Illuminate\Support\Str; @endphp

@extends('layouts.app')

@section('title', 'Museum Directory — Artevo')
@section('meta_description', 'Browse verified partner museums on Artevo — rich profiles, artifact collections, and curated exhibitions from cultural institutions around the world.')

@section('content')
<section class="av-section av-section--white" style="padding-top: var(--space-12); padding-bottom: var(--space-8); text-align: center;">
    <div class="container" style="max-width: 800px;">
        <x-tag>Museums</x-tag>
        <h1 style="margin-top: var(--space-4); font-size: var(--text-5xl);">Cultural <em>institutions</em></h1>
        <p style="margin: 0 auto; font-size: var(--text-lg); color: var(--color-text-muted);">
            Verified institutions preserving and exhibiting cultural heritage on Artevo.
        </p>
    </div>
</section>

<section class="av-section av-section--white" style="padding-top: 0;">
    <div class="container">
        
        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('museums.index') }}" id="museum-filter-form" style="margin-bottom: var(--space-10); display: flex; flex-wrap: wrap; gap: var(--space-4); align-items: center; justify-content: space-between; padding: var(--space-3); background: var(--color-bg-alt); border-radius: var(--radius-lg); border: 1px solid var(--color-border);">
            <div style="flex: 1; min-width: 280px; position: relative;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--stone-400); pointer-events: none;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by name, city or country…" style="width: 100%; padding: 0.85rem 1rem 0.85rem 2.8rem; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-family: var(--font-body); font-size: var(--text-base); outline: none; background: var(--white); transition: border-color var(--duration-fast) var(--ease-out);">
            </div>

            <div style="display: flex; gap: var(--space-4); align-items: center; padding-right: var(--space-2); flex-wrap: wrap;">
                @if($countries->isNotEmpty())
                    <select name="country" onchange="this.form.submit()" style="padding: 0.6rem 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: var(--font-body); outline: none; background: var(--white); font-size: var(--text-sm);">
                        <option value="">All countries</option>
                        @foreach($countries as $country)
                            <option value="{{ $country }}" @selected(request('country') === $country)>
                                {{ $country }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <label style="display: flex; align-items: center; gap: var(--space-2); cursor: pointer; font-weight: 500; color: var(--color-text); font-size: var(--text-sm);">
                    <input type="checkbox" name="verified" value="1" @checked(request()->boolean('verified')) onchange="this.form.submit()" style="width: auto; accent-color: var(--brass-600);">
                    Verified only
                </label>
                
                <label style="display: flex; align-items: center; gap: var(--space-2); cursor: pointer; font-weight: 500; color: var(--color-text); font-size: var(--text-sm);">
                    <input type="checkbox" name="featured" value="1" @checked(request()->boolean('featured')) onchange="this.form.submit()" style="width: auto; accent-color: var(--brass-600);">
                    Featured
                </label>

                @if(request()->hasAny(['search', 'country', 'verified', 'featured']))
                    <a href="{{ route('museums.index') }}" class="av-btn" style="background: var(--white); color: var(--ink-900); border: 1px solid var(--color-border);">Clear</a>
                @endif
                <button type="submit" class="av-btn av-btn--primary">Search</button>
            </div>
        </form>

        {{-- ─── View toggle (grid / map) ───────────────────────────────────────── --}}
        @if($hasMapData)
        <div class="flex" style="gap:var(--space-2); border-bottom:1px solid var(--color-border); padding-bottom:var(--space-2); margin-bottom: var(--space-6); justify-content: center;">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}"
               style="padding:6px 16px; border-radius:var(--radius-sm); font-size:.85rem; font-weight:600; text-decoration:none;
                      {{ request('view', 'grid') === 'grid' ? 'background:rgba(212,175,55,.15); color:var(--brass-700); border:1px solid rgba(212,175,55,.3);' : 'color:var(--color-text-muted); border:1px solid transparent;' }}">
                ⊞ Grid View
            </a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'map']) }}"
               style="padding:6px 16px; border-radius:var(--radius-sm); font-size:.85rem; font-weight:600; text-decoration:none;
                      {{ request('view') === 'map' ? 'background:rgba(212,175,55,.15); color:var(--brass-700); border:1px solid rgba(212,175,55,.3);' : 'color:var(--color-text-muted); border:1px solid transparent;' }}">
                🗺 Map View
            </a>
        </div>
        @endif

        {{-- ─── Map view ─────────────────────────────────────────────────────────── --}}
        @if($hasMapData && request('view') === 'map')
            <p style="font-size:.85rem; color:var(--color-text-muted); margin-bottom:var(--space-4); text-align: center;">Showing {{ count(json_decode($museumsGeoJson)) }} museum{{ count(json_decode($museumsGeoJson)) === 1 ? '' : 's' }} on the map. Click a marker to view the museum profile.</p>
            <x-museum-map :museums="$museumsGeoJson" height="520px" />
        @endif

        @if(request('view') !== 'map')
            @if ($museums->isEmpty())
                <div style="text-align:center; padding: var(--space-16) 0; color: var(--color-text-muted); background: var(--color-bg-alt); border-radius: var(--radius-lg);">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto var(--space-4); opacity:.4;"><path d="m3 9 9-6 9 6v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <h3 style="font-size:1.25rem; margin-bottom:var(--space-2); color: var(--color-text);">No museums found</h3>
                    <p>{{ request('search') ? 'Try a different search term or remove a filter.' : 'Check back soon for new institutions.' }}</p>
                </div>
            @else
                <div class="flex" style="align-items:center; justify-content:space-between; margin-bottom:var(--space-6); flex-wrap:wrap; gap:var(--space-3);">
                    <p style="font-size:.9rem; color:var(--color-text-muted); margin:0;">
                        Showing <strong style="color:var(--color-text);">{{ $museums->total() }}</strong>
                        {{ Str::plural('museum', $museums->total()) }}
                        @if(request('search')) matching "<em>{{ request('search') }}</em>" @endif
                    </p>
                </div>

                <div class="grid grid-3" style="gap: var(--space-6);">
                    @foreach ($museums as $museum)
                    <article class="av-card" style="display:flex; flex-direction:column; overflow:hidden; border-radius: var(--radius-md);" data-reveal>
                        {{-- Cover image --}}
                        <a href="{{ route('museums.show', $museum) }}" style="display:block; overflow:hidden; background: #1a1a2e; position:relative; flex-shrink:0;" tabindex="-1" aria-hidden="true">
                            @if($museum->coverImageUrl())
                                <img src="{{ $museum->coverImageUrl() }}" alt="{{ $museum->name }}"
                                     style="width:100%; height:100%; object-fit:cover; transition:transform .5s ease;" loading="lazy">
                            @else
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; opacity:.3;">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m3 9 9-6 9 6v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                </div>
                            @endif

                            {{-- Badges --}}
                            <div style="position:absolute; top:var(--space-3); left:var(--space-3); display:flex; gap:var(--space-2); flex-wrap:wrap;">
                                @if($museum->featured)
                                    <span class="av-tag av-tag--gold" style="box-shadow: var(--shadow-sm);">Featured</span>
                                @endif
                                @if($museum->verification_status === 'verified')
                                    <span style="background: var(--white); color: var(--green-600); box-shadow: var(--shadow-sm); border-radius:999px; font-size:.68rem; font-weight:700; padding:2px 8px; letter-spacing:.04em; display: flex; align-items: center;">✓ Verified</span>
                                @endif
                            </div>

                        </a>

                        {{-- Body --}}
                        <div style="padding: var(--space-5); flex:1; display:flex; flex-direction:column; gap:var(--space-2);">
                            <h3 style="font-size: 1.25rem; margin: 0; line-height: 1.3;">
                                <a href="{{ route('museums.show', $museum) }}" style="color:var(--color-text); text-decoration:none;">{{ $museum->name }}</a>
                            </h3>

                            @if($museum->tagline)
                                <p style="font-size:.9rem; color:var(--color-text-muted); line-height:1.5; flex:1; margin: var(--space-2) 0 0;">{{ Str::limit($museum->tagline, 100) }}</p>
                            @endif

                            @if($museum->city || $museum->country)
                                <p style="font-size:.8rem; color:var(--color-text-muted); margin:var(--space-2) 0 0;">
                                    📍 {{ collect([$museum->city, $museum->country])->filter()->join(', ') }}
                                </p>
                            @endif

                            {{-- Stats footer --}}
                            <div style="margin-top:var(--space-4); display:flex; flex-wrap:wrap; gap:var(--space-4); align-items:center; padding-top:var(--space-4); border-top:1px solid var(--color-border); font-size:.85rem; color:var(--color-text-muted);">
                                <span>🏺 {{ number_format($museum->artifacts_count) }} artifact{{ $museum->artifacts_count !== 1 ? 's' : '' }}</span>
                                <span>🖼 {{ $museum->exhibitions_count }} exhibition{{ $museum->exhibitions_count !== 1 ? 's' : '' }}</span>
                                <a href="{{ route('museums.show', $museum) }}" style="margin-left:auto; color:var(--brass-700); font-weight:600; text-decoration:none;">Profile &rarr;</a>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <div style="margin-top: var(--space-10);">
                    {{ $museums->links() }}
                </div>
            @endif
        @endif {{-- /map view gate --}}
    </div>
</section>
@endsection

@push('styles')
<style>
.av-card:hover img { transform: scale(1.05); }
.av-card { transition: box-shadow var(--duration-base) var(--ease-out), transform var(--duration-base) var(--ease-out); }
.av-card:hover { box-shadow: var(--shadow-md); transform: translateY(-4px); }
input[type="search"]:focus, select:focus { border-color: var(--brass-600) !important; box-shadow: 0 0 0 3px rgba(169, 129, 46, 0.15) !important; }
</style>
@endpush
