@php use Illuminate\Support\Str; @endphp

@extends('layouts.app')

@section('title', $auction->title . ' — Artevo Auctions')
@section('meta_description', Str::limit(strip_tags($auction->description ?? $auction->artifact?->short_description), 160))

@section('content')

{{-- ─── Hero ─────────────────────────────────────────────────────────────── --}}
@php
    $img       = $auction->artifact?->primaryImage();
    $isOpen    = $auction->isOpen();
    $remaining = $auction->remainingSeconds();
@endphp

<div style="position:relative; height:clamp(280px,44vh,520px); overflow:hidden; background:#0d0a12;">
    @if($img)
        <img src="{{ $img->url() }}" alt="{{ $auction->artifact->name }}"
             style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:.45;">
    @endif
    <div style="position:absolute; inset:0; background:linear-gradient(to top, #0d0a12 0%, rgba(13,10,18,.5) 50%, transparent 100%);"></div>

    <div class="container" style="position:relative; height:100%; display:flex; flex-direction:column; justify-content:flex-end; padding-bottom:var(--space-8);">
        <div class="flex" style="gap:var(--space-2); margin-bottom:var(--space-3); flex-wrap:wrap; align-items:center;">
            @if($isOpen)
                <span style="background:rgba(16,185,129,.2); color:#10b981; border:1px solid rgba(16,185,129,.4); border-radius:999px; font-size:.72rem; font-weight:700; padding:4px 12px; letter-spacing:.05em;">● LIVE AUCTION</span>
            @elseif($auction->isClosed())
                <span style="background:rgba(100,100,120,.3); color:var(--color-muted); border:1px solid rgba(255,255,255,.15); border-radius:999px; font-size:.72rem; font-weight:700; padding:4px 12px; letter-spacing:.05em;">CLOSED</span>
            @elseif($auction->isDraft())
                <span style="background:rgba(245,158,11,.15); color:#f59e0b; border:1px solid rgba(245,158,11,.3); border-radius:999px; font-size:.72rem; font-weight:700; padding:4px 12px; letter-spacing:.05em;">DRAFT</span>
            @endif
            @if($auction->artifact?->category)
                <span style="font-size:.75rem; color:var(--color-gold); font-weight:600; text-transform:uppercase; letter-spacing:.06em;">{{ $auction->artifact->category->name }}</span>
            @endif

            @auth
                @if(auth()->user()->watchedAuctions()->where('auction_id', $auction->id)->exists())
                    <form method="POST" action="{{ route('auctions.watch.destroy', $auction) }}" style="display:inline; margin-left: auto;">
                        @csrf
                        @method('DELETE')
                        <button class="av-btn av-btn--outline" style="font-size:.72rem; padding: 3px 10px; color: #ef4444; border-color: rgba(239,68,68,.3); background: rgba(0,0,0,.3); height: auto;">
                            ♥ Watching
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('auctions.watch.store', $auction) }}" style="display:inline; margin-left: auto;">
                        @csrf
                        <button class="av-btn av-btn--outline" style="font-size:.72rem; padding: 3px 10px; color: var(--parchment-100); border-color: rgba(255,255,255,.3); background: rgba(0,0,0,.3); height: auto;">
                            ♡ Watch
                        </button>
                    </form>
                @endif
            @endauth
        </div>

        <h1 style="font-size:clamp(1.6rem,4vw,2.8rem); color:var(--parchment-100); margin:0 0 var(--space-2); line-height:1.15;">{{ $auction->title }}</h1>
        @if($auction->artifact)
            <p style="color:rgba(248,245,239,.65); font-size:.9rem; margin:0;">
                <a href="{{ route('artifacts.show', $auction->artifact) }}" style="color:inherit; text-decoration:underline; text-underline-offset:3px;">{{ $auction->artifact->name }}</a>
                @if($auction->artifact->museum)
                    &nbsp;·&nbsp;
                    <a href="{{ route('museums.show', $auction->artifact->museum) }}" style="color:inherit; text-decoration:underline; text-underline-offset:3px;">{{ $auction->artifact->museum->name }}</a>
                @endif
            </p>
        @endif
    </div>
</div>

{{-- ─── Body ──────────────────────────────────────────────────────────────── --}}
<div class="container" style="padding-top:var(--space-8); padding-bottom:var(--space-16);">
    <div style="display:grid; grid-template-columns:1fr 340px; gap:var(--space-10); align-items:start;">

        {{-- ── Left: Description + Bid History ── --}}
        <div>
            @if($auction->description)
            <section style="margin-bottom:var(--space-8);">
                <h2 style="font-size:1.1rem; margin-bottom:var(--space-3);">About this lot</h2>
                <div style="font-size:1rem; line-height:1.8; color:var(--color-body); max-width:640px;">
                    {!! nl2br(e($auction->description)) !!}
                </div>
            </section>
            @endif

            {{-- Bid history --}}
            <section aria-labelledby="bid-history-heading">
                <h2 id="bid-history-heading" style="font-size:1.1rem; margin-bottom:var(--space-4);">
                    Bid history
                    <span style="font-size:.85rem; font-weight:400; color:var(--color-muted);">({{ $auction->bids_count }})</span>
                </h2>

                @if($auction->bids->isEmpty())
                    <div class="av-card" style="padding:var(--space-6); text-align:center; color:var(--color-muted);">
                        <p style="margin:0;">No bids yet. Be the first to bid!</p>
                    </div>
                @else
                    <div class="av-card" style="padding:0; overflow:hidden;">
                        <table style="width:100%; border-collapse:collapse; font-size:.875rem;">
                            <thead>
                                <tr style="background:var(--color-surface-2); text-align:left;">
                                    <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em;">Bidder</th>
                                    <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em;">Amount</th>
                                    <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em;">Time</th>
                                    <th style="padding:var(--space-3) var(--space-4);"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auction->bids as $bid)
                                <tr style="border-top:1px solid var(--color-border); {{ $bid->is_winning ? 'background:rgba(16,185,129,.05);' : '' }}">
                                    <td style="padding:var(--space-3) var(--space-4); color:var(--color-heading);">
                                        {{ $bid->bidder?->name ?? 'Anonymous' }}
                                    </td>
                                    <td style="padding:var(--space-3) var(--space-4); font-weight:700; color:{{ $bid->is_winning ? '#10b981' : 'var(--color-heading)' }};">
                                        {{ $auction->currency }} {{ number_format((float) $bid->amount, 2) }}
                                    </td>
                                    <td style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.8rem;">
                                        {{ $bid->created_at->diffForHumans() }}
                                    </td>
                                    <td style="padding:var(--space-3) var(--space-4);">
                                        @if($bid->is_winning)
                                            <span style="font-size:.7rem; font-weight:700; color:#10b981; letter-spacing:.05em;">WINNING</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        {{-- ── Right: Bid panel (sticky) ── --}}
        <aside style="position:sticky; top:var(--space-8);">

            {{-- Price box --}}
            <div class="av-card" style="padding:var(--space-5); margin-bottom:var(--space-4);">
                <p style="font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; color:var(--color-muted); margin:0 0 var(--space-1);">
                    {{ $auction->bids_count > 0 ? 'Current bid' : 'Starting bid' }}
                </p>
                <p style="font-size:2rem; font-weight:800; color:var(--color-gold); margin:0 0 var(--space-4); line-height:1;">
                    {{ $auction->currency }} {{ number_format((float) $auction->current_price, 2) }}
                </p>

                @if($isOpen && $auction->ends_at)
                {{-- Live countdown --}}
                <div class="av-countdown" data-ends-at="{{ $auction->ends_at->toIso8601String() }}"
                     style="margin-bottom:var(--space-4); padding:var(--space-3); background:var(--color-surface-2); border-radius:var(--radius-sm); text-align:center;">
                    <p style="font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; color:var(--color-muted); margin:0 0 var(--space-1);">Closes in</p>
                    <p class="av-countdown__timer" style="font-size:1.5rem; font-weight:800; color:#10b981; margin:0; font-variant-numeric:tabular-nums;">—</p>
                    <p style="font-size:.72rem; color:var(--color-muted); margin:var(--space-1) 0 0;">
                        {{ $auction->ends_at->format('M j, Y \a\t g:i A') }}
                    </p>
                </div>
                @elseif($auction->isClosed())
                <div style="margin-bottom:var(--space-4); padding:var(--space-3); background:var(--color-surface-2); border-radius:var(--radius-sm); text-align:center;">
                    <p style="font-size:.75rem; color:var(--color-muted); margin:0;">Auction closed {{ $auction->ends_at?->diffForHumans() }}</p>
                    @if($auction->winner)
                        <p style="font-size:.85rem; font-weight:600; color:var(--color-heading); margin:var(--space-1) 0 0;">
                            🏆 Won by {{ $auction->winner->name }}
                        </p>
                    @else
                        <p style="font-size:.8rem; color:var(--color-muted); margin:var(--space-1) 0 0;">No winner (no bids)</p>
                    @endif
                </div>
                @endif

                {{-- Meta --}}
                <div style="font-size:.8rem; color:var(--color-muted); display:flex; flex-direction:column; gap:var(--space-2); margin-bottom:var(--space-4);">
                    <div class="flex" style="justify-content:space-between;">
                        <span>Total bids</span>
                        <strong style="color:var(--color-heading);">{{ $auction->bids_count }}</strong>
                    </div>
                    <div class="flex" style="justify-content:space-between;">
                        <span>Min. next bid</span>
                        <strong style="color:var(--color-heading);">{{ $auction->currency }} {{ number_format($auction->nextMinimumBid(), 2) }}</strong>
                    </div>
                    <div class="flex" style="justify-content:space-between;">
                        <span>Bid increment</span>
                        <strong style="color:var(--color-heading);">{{ $auction->currency }} {{ number_format((float) $auction->bid_increment, 2) }}</strong>
                    </div>
                    <div class="flex" style="justify-content:space-between;">
                        <span>Listed by</span>
                        <strong style="color:var(--color-heading);">{{ $auction->creator?->name ?? '—' }}</strong>
                    </div>
                </div>

                {{-- Bid form --}}
                @auth
                    @if($isOpen && auth()->id() !== $auction->created_by)
                    <form method="POST" action="{{ route('auctions.bid', $auction) }}" id="bid-form">
                        @csrf
                        <div style="display:flex; gap:var(--space-2); flex-wrap:wrap;">
                            <input type="number"
                                   name="amount"
                                   id="bid-amount"
                                   step="0.01"
                                   min="{{ $auction->nextMinimumBid() }}"
                                   placeholder="{{ number_format($auction->nextMinimumBid(), 2) }}"
                                   class="av-input"
                                   style="flex:1; min-width:120px;"
                                   required>
                            <button type="submit" class="av-btn av-btn--primary" style="white-space:nowrap;">
                                Place Bid
                            </button>
                        </div>
                        @error('amount')
                            <p style="color:#ef4444; font-size:.8rem; margin:var(--space-2) 0 0;">{{ $message }}</p>
                        @enderror
                    </form>
                    @elseif(auth()->id() === $auction->created_by)
                    <p style="font-size:.85rem; color:var(--color-muted); text-align:center;">You cannot bid on your own auction.</p>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="av-btn av-btn--primary" style="width:100%; text-align:center; display:block;">
                        Sign in to bid
                    </a>
                @endauth
            </div>

            {{-- Curator/Collector management buttons --}}
            @auth
            @if(auth()->id() === $auction->created_by || auth()->user()->isAdmin())
            <div class="av-card" style="padding:var(--space-4);">
                <p style="font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; color:var(--color-muted); margin:0 0 var(--space-3);">Manage auction</p>

                @if($auction->isDraft())
                    <form method="POST" action="{{ route('curator.auctions.publish', $auction) }}" style="margin-bottom:var(--space-2);">
                        @csrf @method('PATCH')
                        <button type="submit" class="av-btn av-btn--primary" style="width:100%;">Publish Auction</button>
                    </form>
                @endif

                @if($auction->isActive() && $isOpen)
                    <form method="POST" action="{{ route('curator.auctions.close', $auction) }}" style="margin-bottom:var(--space-2);">
                        @csrf @method('PATCH')
                        <button type="submit" class="av-btn av-btn--outline" style="width:100%;" onclick="return confirm('Close this auction now?')">Close Early</button>
                    </form>
                @endif

                @if(!$auction->isClosed() && !$auction->isCancelled() && $auction->bids_count === 0)
                    <form method="POST" action="{{ route('curator.auctions.cancel', $auction) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="av-btn av-btn--ghost" style="width:100%; color:#ef4444;" onclick="return confirm('Cancel this auction? This cannot be undone.')">Cancel Auction</button>
                    </form>
                @endif
            </div>
            @endif
            @endauth

        </aside>
    </div>
</div>

@endsection

@push('styles')
<style>
@media (max-width: 900px) {
    .container > div[style*="grid-template-columns"] { grid-template-columns: 1fr !important; }
    aside { position: static !important; }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    function formatDuration(seconds) {
        if (seconds <= 0) return 'Closed';
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = seconds % 60;
        if (h > 0) return `${h}h ${String(m).padStart(2,'0')}m ${String(s).padStart(2,'0')}s`;
        if (m > 0) return `${m}m ${String(s).padStart(2,'0')}s`;
        return `${s}s`;
    }

    document.querySelectorAll('.av-countdown').forEach(function (el) {
        const endsAt   = new Date(el.dataset.endsAt);
        const timerEl  = el.querySelector('.av-countdown__timer');

        function tick() {
            const remaining = Math.max(0, Math.floor((endsAt - Date.now()) / 1000));
            timerEl.textContent = formatDuration(remaining);
            if (remaining <= 300) timerEl.style.color = '#f59e0b';
            if (remaining <= 60)  timerEl.style.color = '#ef4444';
            if (remaining === 0) { clearInterval(timer); location.reload(); }
        }

        tick();
        const timer = setInterval(tick, 1000);
    });
})();
</script>
@endpush
