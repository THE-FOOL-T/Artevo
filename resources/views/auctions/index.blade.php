@php use Illuminate\Support\Str; @endphp

@extends('layouts.app')

@section('title', 'Historical Artifact Auctions — Artevo Archive')
@section('meta_description', 'Bid on verified historical artifacts in live and upcoming auctions curated by renowned institutions and collectors.')

@section('content')
{{-- Editorial Header / Hero --}}
<section style="background: var(--ink-900); color: var(--parchment-100); padding: var(--space-16) 0 var(--space-14); position: relative; overflow: hidden; border-bottom: 1px solid rgba(169,129,46,0.25);">
    <div style="position: absolute; inset: 0; background: radial-gradient(circle at 80% 30%, rgba(169,129,46,0.18) 0%, transparent 60%); pointer-events: none;"></div>
    <div class="container" style="position: relative; z-index: 2; max-width: 960px; text-align: center;">
        <span class="av-tag" style="background: rgba(169,129,46,0.2); color: var(--brass-500); border: 1px solid rgba(169,129,46,0.35); text-transform: uppercase; letter-spacing: 0.12em; font-size: 0.75rem; padding: 4px 14px;">
            Provenance & Acquisition
        </span>
        <h1 style="font-family: var(--font-display); font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 400; line-height: 1.15; margin: var(--space-4) 0 var(--space-4); color: #fff;">
            Artifact <em style="font-style: italic; color: var(--brass-500);">Auctions</em>
        </h1>
        <p style="font-size: clamp(1rem, 2vw, 1.25rem); color: var(--stone-400); max-width: 680px; margin: 0 auto; line-height: 1.6;">
            Acquire verified historical antiquities with authenticated provenance. Participate in live and upcoming lots hosted by distinguished global cultural estates.
        </p>
    </div>
</section>

<section class="av-section av-section--white" style="padding-top: var(--space-10); padding-bottom: var(--space-16);">
    <div class="container">
        
        {{-- Search and Filter Bar --}}
        <form method="GET" action="{{ route('auctions.index') }}" style="margin-bottom: var(--space-12); display: flex; flex-wrap: wrap; gap: var(--space-4); align-items: center; justify-content: space-between; padding: var(--space-4) var(--space-6); background: var(--color-bg-alt); border-radius: var(--radius-lg); border: 1px solid var(--color-border); box-shadow: var(--shadow-sm);">
            <div style="flex: 1; min-width: 280px; position: relative;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 1.1rem; top: 50%; transform: translateY(-50%); color: var(--stone-400); pointer-events: none;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search auction lots by artifact name, dynasty, or institution…" style="width: 100%; padding: 0.9rem 1.2rem 0.9rem 3rem; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-family: var(--font-body); font-size: 0.95rem; outline: none; background: var(--white); transition: border-color var(--duration-fast) var(--ease-out);">
            </div>

            <div style="display: flex; gap: var(--space-3); align-items: center; flex-wrap: wrap;">
                <select name="status" onchange="this.form.submit()" style="padding: 0.85rem 1.2rem; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-family: var(--font-body); outline: none; background: var(--white); font-size: 0.92rem; font-weight: 500; color: var(--ink-900); cursor: pointer;">
                    <option value="" @selected(request('status') === null)>All Lots & Statuses</option>
                    <option value="live"    @selected(request('status') === 'live')>🔴 Live Now</option>
                    <option value="upcoming" @selected(request('status') === 'upcoming')>🕒 Upcoming Lots</option>
                    <option value="closed"  @selected(request('status') === 'closed')>⬛ Closed Archives</option>
                </select>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('auctions.index') }}" class="av-btn" style="background: var(--white); color: var(--ink-900); border: 1px solid var(--color-border); padding: 0.85rem 1.4rem;">Reset</a>
                @endif
                <button type="submit" class="av-btn av-btn--primary" style="padding: 0.85rem 1.8rem; font-weight: 600;">Filter Lots</button>
            </div>
        </form>

        @if($auctions->isEmpty())
            <div style="text-align:center; padding: var(--space-16) var(--space-6); color: var(--color-text-muted); background: var(--color-bg-alt); border-radius: var(--radius-lg); border: 1px dashed var(--color-border);">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="margin: 0 auto var(--space-4); color: var(--brass-600); opacity: 0.6;"><path d="m14 4 6 6M6.5 11.5l6 6M3 21l4.5-4.5M12.5 8 8 12.5l3.5 3.5L16 11.5 12.5 8Z"/></svg>
                <h3 style="font-family: var(--font-display); font-size: 1.5rem; margin-bottom: var(--space-2); color: var(--ink-900);">No auction lots matched your criteria</h3>
                <p style="max-width: 440px; margin: 0 auto;">{{ request('search') || request('status') ? 'Try broadening your filter status or search keywords to view active or archived auction lots.' : 'New auction lots are currently being prepared and cataloged by our curatorial team.' }}</p>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('auctions.index') }}" class="av-btn av-btn--primary" style="margin-top: var(--space-6);">Clear Filters</a>
                @endif
            </div>
        @else
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-8); border-bottom: 2px solid var(--ink-900); padding-bottom: var(--space-3);">
                <h2 style="font-family: var(--font-display); font-size: 1.75rem; margin: 0; color: var(--ink-900);">
                    {{ request('search') || request('status') ? 'Filtered Lots' : 'All Auction Lots' }}
                </h2>
                <span style="font-size: 0.9rem; font-weight: 500; color: var(--stone-600);">
                    Showing {{ $auctions->firstItem() }}–{{ $auctions->lastItem() }} of {{ $auctions->total() }} lots
                </span>
            </div>

            <div class="grid grid-3" style="gap: var(--space-8);">
                @foreach ($auctions as $auction)
                    <article class="av-card av-card--media" data-reveal style="border-radius: var(--radius-lg); overflow: hidden; height: 100%; border: 1px solid var(--color-border); background: var(--white); display: flex; flex-direction: column; transition: transform 0.3s var(--ease-out), box-shadow 0.3s var(--ease-out);">
                        <a href="{{ route('auctions.show', $auction) }}" style="display:block; position:relative; aspect-ratio:16/9; overflow:hidden; background: var(--color-bg-alt);" tabindex="-1" aria-hidden="true">
                            <img src="{{ $auction->artifact->primaryImageUrl() }}" 
                                 alt="{{ $auction->artifact->name }}" 
                                 style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s var(--ease-out);"
                                 loading="lazy">
                            
                            {{-- Status Badges overlaid on top left --}}
                            <div style="position: absolute; top: var(--space-4); left: var(--space-4); display: flex; gap: var(--space-2);">
                                @if($auction->status === 'live')
                                    <span class="av-tag" style="background: var(--terracotta-600); color: var(--white); border: none; box-shadow: var(--shadow-md); animation: pulse 2s infinite; font-weight: 700;">🔴 Live Now</span>
                                @elseif($auction->status === 'upcoming')
                                    <span class="av-tag" style="background: var(--white); color: var(--ink-900); border: 1px solid var(--color-border); box-shadow: var(--shadow-md); font-weight: 700;">🕒 Upcoming</span>
                                @elseif($auction->status === 'closed')
                                    <span class="av-tag" style="background: var(--ink-900); color: var(--white); border: none; box-shadow: var(--shadow-md); font-weight: 600;">⬛ Closed Lot</span>
                                @endif
                            </div>

                            {{-- Current bid/price overlaid on bottom right --}}
                            @if($auction->status !== 'closed')
                            <div style="position: absolute; bottom: var(--space-3); right: var(--space-3); background: rgba(15, 12, 20, 0.9); color: var(--white); padding: 6px 14px; border-radius: var(--radius-sm); font-size: 0.9rem; font-weight: 600; font-family: var(--font-mono); backdrop-filter: blur(6px); border: 1px solid rgba(255,255,255,0.15);">
                                @if($auction->current_bid)
                                    Bid: ${{ number_format($auction->current_bid ?? 0, 2) }}
                                @else
                                    Start: ${{ number_format($auction->starting_price ?? 0, 2) }}
                                @endif
                            </div>
                            @endif
                        </a>
                        
                        <div style="padding: var(--space-6); display: flex; flex-direction: column; gap: var(--space-2); flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.75rem; color: var(--brass-700); font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">
                                    🏛 {{ $auction->artifact->museum->name ?? 'Private Estate' }}
                                </span>
                            </div>
                            
                            <h3 style="margin: var(--space-1) 0 0; font-family: var(--font-display); font-size: 1.35rem; line-height: 1.3;">
                                <a href="{{ route('auctions.show', $auction) }}" style="color: var(--ink-900); text-decoration: none;">{{ $auction->artifact->name }}</a>
                            </h3>

                            @if($auction->artifact->short_description)
                                <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6; flex: 1; margin: var(--space-2) 0 0;">
                                    {{ Str::limit($auction->artifact->short_description, 100) }}
                                </p>
                            @endif

                            @if($auction->status === 'live' && $auction->ends_at)
                                <div style="margin-top: auto; padding-top: var(--space-4); border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: space-between; font-size: 0.85rem;">
                                    <span style="color: var(--terracotta-600); font-weight: 600;">
                                        Ends {{ $auction->ends_at->diffForHumans() }}
                                    </span>
                                    <a href="{{ route('auctions.show', $auction) }}" style="color: var(--brass-700); font-weight: 600; text-decoration: none;">Place Bid &rarr;</a>
                                </div>
                            @elseif($auction->status === 'upcoming' && $auction->starts_at)
                                <div style="margin-top: auto; padding-top: var(--space-4); border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: space-between; font-size: 0.85rem; color: var(--stone-600);">
                                    <span>Starts {{ $auction->starts_at->format('M j, Y') }}</span>
                                    <a href="{{ route('auctions.show', $auction) }}" style="color: var(--brass-700); font-weight: 600; text-decoration: none;">Lot Details &rarr;</a>
                                </div>
                            @elseif($auction->status === 'closed')
                                <div style="margin-top: auto; padding-top: var(--space-4); border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: space-between; font-size: 0.85rem; color: var(--stone-600);">
                                    <span>Ended {{ $auction->ends_at?->format('M j, Y') }}</span>
                                    <a href="{{ route('auctions.show', $auction) }}" style="color: var(--ink-700); font-weight: 600; text-decoration: none;">View Results &rarr;</a>
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div style="margin-top: var(--space-12);">
                {{ $auctions->links() }}
            </div>
        @endif
    </div>
</section>
@endsection

@push('styles')
<style>
.av-card--media:hover img { transform: scale(1.06); }
.av-card--media:hover { box-shadow: var(--shadow-lg); transform: translateY(-5px); border-color: rgba(169, 129, 46, 0.45) !important; }
input[type="search"]:focus, select:focus { border-color: var(--brass-600) !important; box-shadow: 0 0 0 3px rgba(169, 129, 46, 0.15) !important; }
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.6; }
    100% { opacity: 1; }
}
</style>
@endpush
