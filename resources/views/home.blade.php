@extends('layouts.app')

@section('title', 'Artevo — Preserve, Verify & Exhibit History')

@section('content')

    {{-- ============================= HERO =============================
         Full-bleed crossfading photo slider. Placeholder photography for
         now (Lorem Picsum) — swap for curated imagery or a dynamic feed
         of real featured-artifact photos once that module exists
         (Phase 7-9). Nav renders transparent/light-text over this via
         the [data-hero] hook in navigation.js. --}}
    <section class="av-hero" data-hero>
        <div class="av-hero__slide is-active" style="background-image: url('{{ asset('images/seed/british_museum.jpg') }}')"></div>
        <div class="av-hero__slide" style="background-image: url('{{ asset('images/seed/louvre_museum.jpg') }}')"></div>
        <div class="av-hero__slide" style="background-image: url('{{ asset('images/seed/egyptian_museum.jpg') }}')"></div>
        <div class="av-hero__slide" style="background-image: url('{{ asset('images/seed/met_museum.jpg') }}')"></div>
        <div class="av-hero__overlay"></div>

        <div class="av-hero__dots">
            <button type="button" class="av-hero__dot is-active" aria-label="Slide 1"></button>
            <button type="button" class="av-hero__dot" aria-label="Slide 2"></button>
            <button type="button" class="av-hero__dot" aria-label="Slide 3"></button>
            <button type="button" class="av-hero__dot" aria-label="Slide 4"></button>
        </div>

        <div class="container av-hero__content">
            <span class="av-tag" data-hero-in="1" style="color: var(--parchment-100); border-bottom-color: var(--brass-100);">Est. 2026 &middot; Digital Heritage Registry</span>
            <h1 data-hero-in="2" style="margin-top: var(--space-4);">
                Every artifact has <em style="color: var(--brass-100);">a story</em>. Artevo keeps it.
            </h1>
            <p data-hero-in="3" style="font-size: var(--text-lg);">
                A single home for museums, collectors and researchers to archive, verify, exhibit and
                auction historical artifacts — with provenance you can trace and authenticity you can trust.
            </p>
            <div class="flex gap-4" data-hero-in="4" style="margin-top: var(--space-6); flex-wrap: wrap;">
                <x-button href="/register" variant="primary">Create your archive</x-button>
                <x-button href="{{ route('about') }}" variant="ghost">How it works</x-button>
            </div>
        </div>

        <a href="#how-it-works" class="av-hero__scroll-cue" aria-label="Scroll to next section">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
        </a>
    </section>

    {{-- ========================= HOW IT WORKS ========================= --}}
    <section class="av-section av-section--white" id="how-it-works">
        <div class="container">
            <x-section-heading eyebrow="How Artevo Works" class="text-center" style="margin-inline:auto;">
                <h2>From attic to archive to <em>auction block</em></h2>
            </x-section-heading>

            <div class="grid grid-3">
                <x-card eyebrow="Step 01" data-reveal>
                    <h3>Archive</h3>
                    <p>Museums and collectors document an artifact — images, dimensions, materials, historical
                        description and supporting documents — in a structured digital record.</p>
                </x-card>
                <x-card eyebrow="Step 02" data-reveal data-reveal-delay="1">
                    <h3>Verify</h3>
                    <p>A curator reviews metadata, images and documentation, then approves, rejects or requests
                        more information — every decision is logged and notified.</p>
                </x-card>
                <x-card eyebrow="Step 03" data-reveal data-reveal-delay="2">
                    <h3>Exhibit or Auction</h3>
                    <p>Verified artifacts join public exhibitions, permanent collections, or timed auctions —
                        complete with provenance and a scannable authenticity record.</p>
                </x-card>
            </div>
        </div>
    </section>

    {{-- ============================ PILLARS =========================== --}}
    <section class="av-section">
        <div class="container">
            <x-section-heading eyebrow="Platform Pillars">
                <h2>Built like a <em>real</em> museum's back office</h2>
                <p>Every module below is a first-class citizen of the platform, not a bolted-on feature.</p>
            </x-section-heading>

            <div class="grid grid-4">
                @foreach ([
                    ['title' => 'Smart Archive', 'desc' => 'Structured records for every artifact, with categories, materials, era and origin.', 'icon' => 'archive'],
                    ['title' => 'Verification', 'desc' => 'A curator-led review workflow with a clear audit trail on every artifact.', 'icon' => 'check'],
                    ['title' => 'Provenance', 'desc' => 'An append-only ownership timeline you can trust and trace.', 'icon' => 'clock'],
                    ['title' => 'Auctions', 'desc' => 'Timed, transparent bidding with live countdowns and full bid history.', 'icon' => 'gavel'],
                ] as $i => $pillar)
                    <x-card data-reveal data-reveal-delay="{{ $i }}">
                        <div class="av-icon-badge">
                            @switch($pillar['icon'])
                                @case('archive')
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="5" rx="1"/><path d="M5 9v9a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V9"/><path d="M10 13h4"/></svg>
                                    @break
                                @case('check')
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/></svg>
                                    @break
                                @case('clock')
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                                    @break
                                @case('gavel')
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m14 4 6 6M6.5 11.5l6 6M3 21l4.5-4.5M12.5 8 8 12.5l3.5 3.5L16 11.5 12.5 8Z"/></svg>
                                    @break
                            @endswitch
                        </div>
                        <h3>{{ $pillar['title'] }}</h3>
                        <p>{{ $pillar['desc'] }}</p>
                    </x-card>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ======================= IMMERSIVE STATS BAND ===================== --}}
    <section class="av-section av-section--dark">
        <div class="container text-center">
            <x-section-heading eyebrow="Artevo in numbers" class="text-center" style="margin-inline:auto;">
                <h2>A growing <em>living archive</em></h2>
            </x-section-heading>
            <div class="grid grid-3" data-reveal>
                <div>
                    <div class="av-stat-num av-counter" data-counter="{{ $stats['artifacts'] }}">0</div>
                    <div class="av-stat-label">Artifacts archived</div>
                </div>
                <div>
                    <div class="av-stat-num av-counter" data-counter="{{ $stats['museums'] }}">0</div>
                    <div class="av-stat-label">Verified museums</div>
                </div>
                <div>
                    <div class="av-stat-num av-counter" data-counter="{{ $stats['verified'] }}">0</div>
                    <div class="av-stat-label">Verified this month</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== BUILT FOR EVERY ROLE ====================== --}}
    <section class="av-section av-section--white">
        <div class="container">
            <x-section-heading eyebrow="Who It's For">
                <h2>One platform, <em>four ways</em> to use it</h2>
            </x-section-heading>

            <div class="grid grid-4">
                <x-card eyebrow="Museums &amp; Curators" data-reveal>
                    <h3>Curate</h3>
                    <p>Manage collections, run exhibitions and lead the verification process.</p>
                </x-card>
                <x-card eyebrow="Collectors" data-reveal data-reveal-delay="1">
                    <h3>Preserve</h3>
                    <p>Archive private collections, request verification, donate or auction pieces.</p>
                </x-card>
                <x-card eyebrow="Researchers" data-reveal data-reveal-delay="2">
                    <h3>Study</h3>
                    <p>Trace provenance, read curator notes and follow an artifact's full history.</p>
                </x-card>
                <x-card eyebrow="Visitors" data-reveal data-reveal-delay="3">
                    <h3>Explore</h3>
                    <p>Browse public exhibitions, scan QR codes and watch auctions unfold live.</p>
                </x-card>
            </div>
        </div>
    </section>

    {{-- ===================== FEATURED EXHIBITIONS ======================= --}}
    @if($featuredExhibitions->isNotEmpty())
    <section class="av-section av-section--white" style="padding-top: var(--space-8);">
        <div class="container">
            <div class="flex" style="align-items:center; justify-content:space-between; margin-bottom:var(--space-6); flex-wrap:wrap; gap:var(--space-3);">
                <x-section-heading eyebrow="Curated Shows" style="margin:0;">
                    <h2 style="margin:0;">Featured <em>exhibitions</em></h2>
                </x-section-heading>
                <a href="{{ route('exhibitions.index') }}" class="av-btn av-btn--outline" style="font-size:.85rem;">Browse all &rarr;</a>
            </div>
            <div class="grid grid-3" style="gap: var(--space-6);">
                @foreach($featuredExhibitions as $feat)
                <a href="{{ route('exhibitions.show', $feat) }}" class="av-card" style="display:block; text-decoration:none; position:relative; overflow:hidden; min-height:260px;" data-reveal>
                    @if($feat->coverImageUrl())
                        <img src="{{ $feat->coverImageUrl() }}" alt="{{ $feat->name }}" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
                        <div style="position:absolute; inset:0; background: linear-gradient(to top, rgba(15,12,20,.92) 0%, rgba(15,12,20,.2) 65%, transparent 100%);"></div>
                    @else
                        <div style="position:absolute; inset:0; background: linear-gradient(135deg, #1e1428 0%, #2d1f3d 100%);"></div>
                    @endif
                    <div style="position:relative; display:flex; flex-direction:column; min-height:260px; padding:var(--space-4);">
                        <span class="av-tag av-tag--gold" style="align-self:flex-start;">Featured</span>
                        <div style="margin-top:auto;">
                            <p style="font-size:.72rem; color:var(--color-gold); font-weight:600; text-transform:uppercase; letter-spacing:.06em; margin-bottom:var(--space-1);">{{ $feat->museum->name }}</p>
                            <h3 style="color:var(--color-parchment); font-size:1.05rem; margin:0 0 var(--space-1); line-height:1.3;">{{ $feat->name }}</h3>
                            @if($feat->tagline)
                                <p style="font-size:.8rem; color:rgba(248,245,239,.65); margin:0;">{{ Str::limit($feat->tagline, 70) }}</p>
                            @endif
                            <p style="font-size:.75rem; color:rgba(248,245,239,.5); margin:var(--space-2) 0 0;">🗂 {{ $feat->sections_count }} {{ Str::plural('section', $feat->sections_count) }}</p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ======================= FEATURED MUSEUMS ========================= --}}
    @if($featuredMuseums->isNotEmpty())
    <section class="av-section" style="background: var(--color-surface-2);">
        <div class="container">
            <div class="flex" style="align-items:center; justify-content:space-between; margin-bottom:var(--space-6); flex-wrap:wrap; gap:var(--space-3);">
                <x-section-heading eyebrow="Partner Institutions" style="margin:0;">
                    <h2 style="margin:0;">Verified <em>museums</em></h2>
                </x-section-heading>
                <a href="{{ route('museums.index') }}" class="av-btn av-btn--outline" style="font-size:.85rem;">Explore directory &rarr;</a>
            </div>
            <div class="grid grid-4" style="gap: var(--space-5);">
                @foreach($featuredMuseums as $museum)
                <a href="{{ route('museums.show', $museum) }}" class="av-card" style="display:flex; flex-direction:column; text-decoration:none;" data-reveal>
                    <div style="height:140px; overflow:hidden; background:linear-gradient(135deg,#1e1428,#2d1f3d); border-radius:var(--radius-sm) var(--radius-sm) 0 0; position:relative; flex-shrink:0;">
                        @if($museum->coverImageUrl())
                            <img src="{{ $museum->coverImageUrl() }}" alt="{{ $museum->name }}" style="width:100%; height:100%; object-fit:cover; opacity:.7;">
                        @endif
                        @if($museum->logoUrl())
                            <img src="{{ $museum->logoUrl() }}" alt="{{ $museum->name }} logo" style="position:absolute; bottom:var(--space-2); left:var(--space-3); width:36px; height:36px; border-radius:var(--radius-sm); border:2px solid rgba(255,255,255,0.85); object-fit:cover; background:var(--dark); box-shadow:var(--shadow-sm);">
                        @endif
                        @if($museum->featured)
                            <span class="av-tag av-tag--gold" style="position:absolute; top:var(--space-2); right:var(--space-2); font-size:.7rem;">Featured</span>
                        @endif
                    </div>
                    <div style="padding:var(--space-4); flex:1; display:flex; flex-direction:column; gap:var(--space-2);">
                        <h3 style="font-size:.95rem; margin:0; line-height:1.3;">{{ $museum->name }}</h3>
                        @if($museum->city)
                            <p style="font-size:.78rem; color:var(--color-muted); margin:0;">📍 {{ $museum->city }}{{ $museum->country ? ', '.$museum->country : '' }}</p>
                        @endif
                        <div class="flex" style="gap:var(--space-3); margin-top:auto; padding-top:var(--space-3); border-top:1px solid var(--color-border); font-size:.75rem; color:var(--color-muted);">
                            <span>{{ $museum->artifacts_count }} artifact{{ $museum->artifacts_count !== 1 ? 's' : '' }}</span>
                            <span>{{ $museum->exhibitions_count }} exhibition{{ $museum->exhibitions_count !== 1 ? 's' : '' }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ============================== CTA =============================== --}}

    <section class="av-section" style="background: var(--brass-100);">
        <div class="container text-center">
            <h2>Start preserving <em>history</em> today</h2>
            <p class="max-w-content">Join as a museum, collector, or curator — or simply create a free account
                to explore what's already been archived.</p>
            <div class="flex-center gap-4" style="margin-top: var(--space-6);">
                <x-button href="/register" variant="dark">Create an account</x-button>
                <x-button href="{{ route('contact') }}" variant="outline-dark">Talk to us</x-button>
            </div>
        </div>
    </section>

@endsection
