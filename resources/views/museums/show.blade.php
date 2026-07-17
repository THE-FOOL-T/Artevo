@extends('layouts.app')

@section('title', "{$museum->name} — Artevo")
@section('meta_description', $museum->tagline ?? "Explore {$museum->name} on Artevo.")

@section('content')

    <section style="position: relative; height: 320px; overflow: hidden;">
        <img src="{{ $museum->coverImageUrl() }}" alt="{{ $museum->name }}" style="width: 100%; height: 100%; object-fit: cover;">
        <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(21,18,13,0.75), rgba(21,18,13,0.1));"></div>
    </section>

    <section class="av-section av-section--white" style="padding-top: var(--space-8);">
        <div class="container">
            <div class="flex gap-4" style="align-items: flex-start; margin-top: -80px; margin-bottom: var(--space-6);">
                <img src="{{ $museum->logoUrl() }}" alt="{{ $museum->name }} logo" style="width: 96px; height: 96px; border-radius: var(--radius-md); border: 4px solid var(--white); object-fit: cover; box-shadow: var(--shadow-md); background: var(--dark);">
                <div style="padding-top: 88px;">
                    <div class="flex gap-2">
                        @if ($museum->featured)
                            <x-tag variant="success" class="av-tag--pill">Featured</x-tag>
                        @endif
                        <x-museum-verification-badge :museum="$museum" />
                    </div>
                    <h1 style="margin-top: var(--space-2); margin-bottom: 0;">{{ $museum->name }}</h1>
                    @if ($museum->tagline)
                        <p>{{ $museum->tagline }}</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-2" style="gap: var(--space-10); align-items: start;">
                <div>
                    @if ($museum->description)
                        <h3>About</h3>
                        <p style="white-space: pre-line;">{{ $museum->description }}</p>
                    @endif

                    @if ($museum->images->isNotEmpty())
                        <h3 class="mt-8">Gallery</h3>
                        <div class="grid grid-3" style="gap: var(--space-3);">
                            @foreach ($museum->images as $image)
                                <img src="{{ $image->url() }}" alt="{{ $image->caption ?? $museum->name }}" style="width: 100%; height: 120px; object-fit: cover; border-radius: var(--radius-sm); transition: transform var(--duration-base) var(--ease-out);" onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <x-card>
                        <span class="av-card__eyebrow">Details</span>
                        <dl style="margin: 0; font-size: var(--text-sm);">
                            @if ($museum->foundation_year)
                                <dt style="color: var(--stone-600);">Founded</dt>
                                <dd style="margin: 0 0 var(--space-3);">{{ $museum->foundation_year }}</dd>
                            @endif
                            @if ($museum->address || $museum->city)
                                <dt style="color: var(--stone-600);">Location</dt>
                                <dd style="margin: 0 0 var(--space-3);">
                                    {{ implode(', ', array_filter([$museum->address, $museum->city, $museum->country])) }}
                                    @if ($museum->directionsUrl())
                                        <br><a href="{{ $museum->directionsUrl() }}" target="_blank" rel="noopener" style="color: var(--brass-700); font-weight: 600;">Get directions &rarr;</a>
                                    @endif
                                </dd>
                            @endif
                            @if ($museum->website)
                                <dt style="color: var(--stone-600);">Website</dt>
                                <dd style="margin: 0 0 var(--space-3);"><a href="{{ $museum->website }}" target="_blank" rel="noopener" style="color: var(--brass-700); font-weight: 600;">{{ $museum->website }}</a></dd>
                            @endif
                        </dl>

                        @if (! empty(array_filter($museum->social_links ?? [])))
                            <div class="flex gap-3" style="margin-top: var(--space-3);">
                                @foreach (($museum->social_links ?? []) as $platform => $url)
                                    @if ($url)
                                        <a href="{{ $url }}" target="_blank" rel="noopener" class="av-footer__social" style="border-color: var(--color-border);" aria-label="{{ ucfirst($platform) }}">{{ strtoupper(substr($platform, 0, 2)) }}</a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </x-card>

                    @if ($museum->opening_hours)
                        <x-card class="mt-6">
                            <span class="av-card__eyebrow">Opening hours</span>
                            <table style="width: 100%; font-size: var(--text-sm);">
                                @foreach ($museum->opening_hours as $day => $hours)
                                    @if ($hours)
                                        <tr>
                                            <td style="padding: 4px 0; text-transform: capitalize; color: var(--stone-600);">{{ $day }}</td>
                                            <td style="padding: 4px 0; text-align: right;">{{ $hours }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </table>
                        </x-card>
                    @endif

                    @if ($museum->contacts->isNotEmpty())
                        <x-card class="mt-6">
                            <span class="av-card__eyebrow">Contact</span>
                            @foreach ($museum->contacts as $contact)
                                <div class="mt-2">
                                    <strong style="font-size: var(--text-sm);">{{ $contact->label }}</strong>
                                    <p style="margin: 0; font-size: var(--text-sm);">
                                        {{ $contact->email }}@if($contact->email && $contact->phone)<br>@endif{{ $contact->phone }}
                                    </p>
                                </div>
                            @endforeach
                        </x-card>
                    @endif

                    {{-- ── Phase 15: Location map ──────────────────────────────── --}}
                    @if($museum->latitude && $museum->longitude)
                        <div class="mt-6">
                            <span style="display:block; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-muted); margin-bottom:var(--space-2);">Location</span>
                            <x-museum-map :museum="$museum" height="280px" :zoom="14" />
                        </div>
                    @endif

                </div>
            </div>

            {{-- ── Museum Curated Collections ────────────────────────────── --}}
            @if(isset($collections) && $collections->isNotEmpty())
                <div style="margin-top: var(--space-14); border-top: 2px solid var(--ink-900); padding-top: var(--space-8);">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: var(--space-6);">
                        <h2 style="font-family: var(--font-display); font-size: 1.75rem; margin: 0; color: var(--ink-900);">Curated Collections</h2>
                        <a href="{{ route('collections.index', ['museum' => $museum->id]) }}" style="font-size: 0.9rem; font-weight: 600; color: var(--brass-700); text-decoration: none;">View all collections &rarr;</a>
                    </div>
                    <div class="grid grid-3" style="gap: var(--space-6);">
                        @foreach($collections as $col)
                            <article class="av-card" style="display: flex; flex-direction: column; overflow: hidden; border-radius: var(--radius-lg); border: 1px solid var(--color-border); background: var(--white);">
                                <a href="{{ route('collections.show', $col) }}" style="display:block; aspect-ratio: 16/9; overflow:hidden; background: var(--color-bg-alt); position: relative;">
                                    @if($col->coverImageUrl())
                                        <img src="{{ $col->coverImageUrl() }}" alt="{{ $col->name }}" style="width:100%; height:100%; object-fit:cover;">
                                    @endif
                                </a>
                                <div style="padding: var(--space-5); display: flex; flex-direction: column; gap: var(--space-2); flex: 1;">
                                    <span style="font-size: 0.75rem; color: var(--brass-700); font-weight: 700; text-transform: uppercase;">🏛 {{ $museum->name }}</span>
                                    <h3 style="font-family: var(--font-display); font-size: 1.25rem; margin: 0;"><a href="{{ route('collections.show', $col) }}" style="color: inherit; text-decoration: none;">{{ $col->name }}</a></h3>
                                    @if($col->description)
                                        <p style="font-size: 0.88rem; color: var(--color-text-muted); margin: 0; flex: 1;">{{ Str::limit($col->description, 95) }}</p>
                                    @endif
                                    <div style="margin-top: auto; padding-top: var(--space-3); border-top: 1px solid var(--color-border); font-size: 0.82rem; color: var(--stone-600); font-weight: 600;">
                                        🏺 {{ $col->artifacts_count ?? $col->artifacts()->count() }} items
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── Museum Exhibitions ─────────────────────────────────────── --}}
            @if(isset($exhibitions) && $exhibitions->isNotEmpty())
                <div style="margin-top: var(--space-14); border-top: 2px solid var(--ink-900); padding-top: var(--space-8);">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: var(--space-6);">
                        <h2 style="font-family: var(--font-display); font-size: 1.75rem; margin: 0; color: var(--ink-900);">Headline Exhibitions</h2>
                        <a href="{{ route('exhibitions.index') }}" style="font-size: 0.9rem; font-weight: 600; color: var(--brass-700); text-decoration: none;">View galleries &rarr;</a>
                    </div>
                    <div class="grid grid-3" style="gap: var(--space-6);">
                        @foreach($exhibitions as $exh)
                            <article class="av-card" style="display: flex; flex-direction: column; overflow: hidden; border-radius: var(--radius-lg); border: 1px solid var(--color-border); background: var(--white);">
                                <a href="{{ route('exhibitions.show', $exh) }}" style="display:block; aspect-ratio: 16/9; overflow:hidden; background: var(--color-bg-alt); position: relative;">
                                    @if($exh->coverImageUrl())
                                        <img src="{{ $exh->coverImageUrl() }}" alt="{{ $exh->name }}" style="width:100%; height:100%; object-fit:cover;">
                                    @endif
                                </a>
                                <div style="padding: var(--space-5); display: flex; flex-direction: column; gap: var(--space-2); flex: 1;">
                                    <span style="font-size: 0.75rem; color: #10B981; font-weight: 700; text-transform: uppercase;">Exhibition Gallery</span>
                                    <h3 style="font-family: var(--font-display); font-size: 1.25rem; margin: 0;"><a href="{{ route('exhibitions.show', $exh) }}" style="color: inherit; text-decoration: none;">{{ $exh->name }}</a></h3>
                                    @if($exh->tagline || $exh->description)
                                        <p style="font-size: 0.88rem; color: var(--color-text-muted); margin: 0; flex: 1;">{{ Str::limit($exh->tagline ?: $exh->description, 95) }}</p>
                                    @endif
                                    <div style="margin-top: auto; padding-top: var(--space-3); border-top: 1px solid var(--color-border); font-size: 0.82rem; color: var(--stone-600); font-weight: 600;">
                                        📍 {{ $exh->location ?: 'Online / Museum Gallery' }}
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── Museum Artifacts Archive ────────────────────────────────── --}}
            @if(isset($artifacts) && $artifacts->isNotEmpty())
                <div style="margin-top: var(--space-14); border-top: 2px solid var(--ink-900); padding-top: var(--space-8);">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: var(--space-6);">
                        <h2 style="font-family: var(--font-display); font-size: 1.75rem; margin: 0; color: var(--ink-900);">Artifact Highlights</h2>
                        <a href="{{ route('artifacts.index', ['museum' => $museum->id]) }}" style="font-size: 0.9rem; font-weight: 600; color: var(--brass-700); text-decoration: none;">View all {{ $museum->artifacts_count ?? '' }} artifacts &rarr;</a>
                    </div>
                    <div class="grid grid-3" style="gap: var(--space-6);">
                        @foreach($artifacts as $art)
                            <article class="av-card av-card--media" style="display: flex; flex-direction: column; overflow: hidden; border-radius: var(--radius-lg); border: 1px solid var(--color-border); background: var(--white);">
                                <a href="{{ route('artifacts.show', $art) }}" style="display:block; aspect-ratio: 16/9; overflow:hidden; background: var(--color-bg-alt); position: relative;">
                                    <img src="{{ $art->primaryImageUrl() }}" alt="{{ $art->name }}" style="width:100%; height:100%; object-fit:cover;">
                                </a>
                                <div style="padding: var(--space-5); display: flex; flex-direction: column; gap: var(--space-2); flex: 1;">
                                    <span style="font-size: 0.75rem; color: var(--brass-700); font-weight: 700; text-transform: uppercase;">{{ $art->category->name ?? 'Artifact' }}</span>
                                    <h3 style="font-family: var(--font-display); font-size: 1.25rem; margin: 0;"><a href="{{ route('artifacts.show', $art) }}" style="color: inherit; text-decoration: none;">{{ $art->name }}</a></h3>
                                    @if($art->short_description)
                                        <p style="font-size: 0.88rem; color: var(--color-text-muted); margin: 0; flex: 1;">{{ Str::limit($art->short_description, 95) }}</p>
                                    @endif
                                    <div style="margin-top: auto; padding-top: var(--space-3); border-top: 1px solid var(--color-border); font-size: 0.82rem; color: var(--stone-600); font-weight: 600;">
                                        @if($art->estimated_value)
                                            Est. ${{ number_format($art->estimated_value) }}
                                        @else
                                            Verified Lot
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </section>

@endsection
