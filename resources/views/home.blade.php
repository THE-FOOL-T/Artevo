@extends('layouts.app')

@section('title', 'Artevo — Preserve, Verify & Exhibit History')

@section('content')

   
    <section class="av-hero" data-hero>
        <div class="av-hero__slide is-active" style="background-image: url('https://picsum.photos/seed/artevo-artifact-1/1600/900')"></div>
        <div class="av-hero__slide" style="background-image: url('https://picsum.photos/seed/artevo-artifact-2/1600/900')"></div>
        <div class="av-hero__slide" style="background-image: url('https://picsum.photos/seed/artevo-artifact-3/1600/900')"></div>
        <div class="av-hero__slide" style="background-image: url('https://picsum.photos/seed/artevo-artifact-4/1600/900')"></div>
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
                    <div class="av-stat-num av-counter" data-counter="4210">0</div>
                    <div class="av-stat-label">Artifacts archived</div>
                </div>
                <div>
                    <div class="av-stat-num av-counter" data-counter="186">0</div>
                    <div class="av-stat-label">Partner museums</div>
                </div>
                <div>
                    <div class="av-stat-num av-counter" data-counter="97">0</div>
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
