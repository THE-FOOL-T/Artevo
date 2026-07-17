@extends('layouts.app')

@section('title', "{$artifact->name} — Artevo")
@section('meta_description', $artifact->short_description ?? "Explore {$artifact->name} on Artevo.")

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            <p><a href="{{ route('artifacts.index') }}" style="color: var(--brass-700); font-weight: 600;">&larr; Back to the archive</a></p>

            <div class="grid grid-2" style="gap: var(--space-10); align-items: start; margin-top: var(--space-6);">
                <div>
                    {{-- Lightbox gallery: click any image to view it fullscreen with
                         prev/next navigation (resources/js/modules/lightbox.js). --}}
                    <div data-gallery>
                        <div class="av-gallery-thumb" data-gallery-item data-gallery-full="{{ $artifact->primaryImageUrl() }}">
                            <img src="{{ $artifact->primaryImageUrl() }}" alt="{{ $artifact->name }}" style="width: 100%; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                        </div>

                        @if ($artifact->images->count() > 1)
                            <div class="grid grid-4" style="gap: var(--space-2); margin-top: var(--space-3);">
                                @foreach ($artifact->images->skip(1) as $image)
                                    <div class="av-gallery-thumb" data-gallery-item data-gallery-full="{{ $image->url() }}">
                                        <img src="{{ $image->url() }}" alt="{{ $image->caption ?? $artifact->name }}" style="width: 100%; height: 80px; object-fit: cover; border-radius: var(--radius-sm);" loading="lazy">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <x-card class="mt-6">
                        <span class="av-card__eyebrow">System Timeline</span>
                        <div style="border-left: 2px solid var(--color-border); padding-left: var(--space-4);">
                            <div style="margin-bottom: var(--space-3);">
                                <strong style="font-size: var(--text-sm);">Added to Artevo</strong>
                                <p style="margin: 0; font-size: var(--text-sm); color: var(--stone-600);">{{ $artifact->created_at->format('F j, Y') }}</p>
                            </div>
                            @if ($artifact->updated_at->diffInMinutes($artifact->created_at) > 1)
                                <div>
                                    <strong style="font-size: var(--text-sm);">Last updated</strong>
                                    <p style="margin: 0; font-size: var(--text-sm); color: var(--stone-600);">{{ $artifact->updated_at->format('F j, Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </x-card>

                    {{-- ── Phase 11: Provenance Timeline ────────────────────── --}}
                    @if($artifact->provenance->isNotEmpty())
                    <x-card class="mt-6">
                        <span class="av-card__eyebrow">Provenance</span>
                        <p style="font-size:.8rem; color:var(--color-muted); margin-bottom:var(--space-4);">Ownership and history chain for this artifact.</p>
                        <ol style="list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:0;">
                            @foreach($artifact->provenance as $record)
                            <li style="display:flex; gap:var(--space-3); padding:var(--space-3) 0; {{ !$loop->last ? 'border-bottom:1px solid var(--color-border);' : '' }}">
                                <div style="font-size:1.3rem; line-height:1; flex-shrink:0; margin-top:2px;">{{ $record->typeIcon() }}</div>
                                <div style="flex:1;">
                                    <div class="flex" style="align-items:center; gap:var(--space-2); flex-wrap:wrap; margin-bottom:var(--space-1);">
                                        <span style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-gold);">{{ $record->typeLabel() }}</span>
                                        @if($record->date)
                                            <span style="font-size:.75rem; color:var(--color-muted);">{{ $record->date->format('M j, Y') }}</span>
                                        @endif
                                        @if($record->location)
                                            <span style="font-size:.75rem; color:var(--color-muted);">· {{ $record->location }}</span>
                                        @endif
                                    </div>
                                    <p style="font-size:.9rem; font-weight:600; margin:0 0 var(--space-1);">{{ $record->title }}</p>
                                    @if($record->description)
                                        <p style="font-size:.82rem; color:var(--color-muted); margin:0 0 var(--space-1); line-height:1.6;">{{ $record->description }}</p>
                                    @endif
                                    @if($record->source_url)
                                        <a href="{{ $record->source_url }}" target="_blank" rel="noopener" style="font-size:.78rem; color:var(--color-gold);">Reference →</a>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ol>
                    </x-card>
                    @endif

                    {{-- ── Phase 12: Restoration History ──────────────────────── --}}
                    @if($artifact->restorationRecords->isNotEmpty())
                    <x-card class="mt-6">
                        <span class="av-card__eyebrow">Restoration History</span>
                        <p style="font-size:.8rem; color:var(--color-muted); margin-bottom:var(--space-4);">Conservation and intervention records.</p>
                        <ol style="list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:0;">
                            @foreach($artifact->restorationRecords as $record)
                            <li style="display:flex; gap:var(--space-3); padding:var(--space-3) 0; {{ !$loop->last ? 'border-bottom:1px solid var(--color-border);' : '' }}">
                                <div style="font-size:1.3rem; line-height:1; flex-shrink:0; margin-top:2px;">{{ $record->categoryIcon() }}</div>
                                <div style="flex:1;">
                                    <div class="flex" style="align-items:center; gap:var(--space-2); flex-wrap:wrap; margin-bottom:var(--space-1);">
                                        <span style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-gold);">{{ $record->categoryLabel() }}</span>
                                        @if($record->durationLabel())
                                            <span style="font-size:.75rem; color:var(--color-muted);">{{ $record->durationLabel() }}</span>
                                        @endif
                                        @if($record->institution)
                                            <span style="font-size:.75rem; color:var(--color-muted);">· {{ $record->institution }}</span>
                                        @endif
                                    </div>
                                    <p style="font-size:.9rem; font-weight:600; margin:0 0 var(--space-1);">{{ $record->title }}</p>
                                    @if($record->conservator_name)
                                        <p style="font-size:.8rem; color:var(--color-muted); margin:0 0 var(--space-1);">Lead: {{ $record->conservator_name }}</p>
                                    @endif
                                    @if($record->description)
                                        <p style="font-size:.82rem; color:var(--color-muted); margin:0 0 var(--space-1); line-height:1.6;">{{ $record->description }}</p>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ol>
                    </x-card>
                    @endif

                    {{-- ── Phase 15: Geographic map ────────────────────────────── --}}
                    @if($artifact->hasOriginCoordinates() || $artifact->hasDiscoveryCoordinates())
                        <x-artifact-map :artifact="$artifact" />
                    @endif
                </div>


                <div>
                    <x-tag>{{ $artifact->category->name }}</x-tag>
                    <h1 style="margin-top: var(--space-2);">{{ $artifact->name }}</h1>
                    <p style="font-family: var(--font-mono); font-size: var(--text-sm); color: var(--stone-600);">{{ $artifact->artifact_code }}</p>

                    {{-- ── Phase 14: Live Auction Banner ───────────────────────── --}}
                    @if($artifact->activeAuction && $artifact->activeAuction->isOpen())
                    @php $liveAuction = $artifact->activeAuction; @endphp
                    <a href="{{ route('auctions.show', $liveAuction) }}"
                       style="display:flex; align-items:center; justify-content:space-between; gap:var(--space-3); padding:var(--space-3) var(--space-4); border-radius:var(--radius-sm); background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.35); text-decoration:none; margin-bottom:var(--space-4); flex-wrap:wrap;">
                        <div>
                            <span style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#10b981;">● Live Auction</span>
                            <p style="font-size:.88rem; color:var(--color-heading); margin:2px 0 0; font-weight:600;">
                                Current bid: {{ $liveAuction->currency }} {{ number_format((float) $liveAuction->current_price, 2) }}
                                @if($liveAuction->bids_count > 0)
                                    <span style="font-weight:400; color:var(--color-muted); font-size:.8rem;">({{ $liveAuction->bids_count }} {{ Str::plural('bid', $liveAuction->bids_count) }})</span>
                                @endif
                            </p>
                        </div>
                        <span style="font-size:.8rem; font-weight:600; color:#10b981; white-space:nowrap;">Bid now →</span>
                    </a>
                    @endif

                    {{-- ── Phase 11: Verification badge ───────────────────── --}}
                    <div class="flex" style="align-items:center; gap:var(--space-3); margin-bottom:var(--space-4); flex-wrap:wrap;">
                        @php
                            $badgeColors = [
                                'success' => ['bg'=>'rgba(16,185,129,.12)', 'color'=>'#10b981', 'border'=>'rgba(16,185,129,.3)'],
                                'warning' => ['bg'=>'rgba(245,158,11,.12)',  'color'=>'#d97706', 'border'=>'rgba(245,158,11,.3)'],
                                'danger'  => ['bg'=>'rgba(239,68,68,.1)',    'color'=>'#ef4444', 'border'=>'rgba(239,68,68,.25)'],
                                'muted'   => ['bg'=>'var(--color-surface-2)','color'=>'var(--color-muted)', 'border'=>'var(--color-border)'],
                            ];
                            $badge = $badgeColors[$artifact->verificationBadgeColor()];
                        @endphp
                        <span style="display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:999px; font-size:.8rem; font-weight:600; background:{{ $badge['bg'] }}; color:{{ $badge['color'] }}; border:1px solid {{ $badge['border'] }};">
                            @if($artifact->isVerified()) ✅
                            @elseif($artifact->isPendingVerification()) 🕐
                            @elseif($artifact->isVerificationRejected()) ✗
                            @else ○
                            @endif
                            {{ $artifact->verificationLabel() }}
                        </span>

                        @auth
                            @can('submitForVerification', $artifact)
                                <form method="POST" action="{{ route('artifacts.verify-request', $artifact) }}">
                                    @csrf
                                    <button class="av-btn av-btn--outline" style="font-size:.82rem; padding: 4px 12px;">
                                        Submit for Verification
                                    </button>
                                </form>
                            @endcan

                            {{-- ── Phase 14: Donate this artifact ────────────────── --}}
                            @can('donate', $artifact)
                                <a href="{{ route('donations.create', ['artifact' => $artifact->slug]) }}"
                                   class="av-btn av-btn--outline"
                                   style="font-size:.82rem; padding: 4px 12px; color: var(--brass-700); border-color: rgba(212,175,55,.4);">
                                    🎁 Donate to Museum
                                </a>
                            @endcan

                            {{-- Favorite / Unfavorite --}}
                            @if(auth()->user()->favoritedArtifacts()->where('artifact_id', $artifact->id)->exists())
                                <form method="POST" action="{{ route('artifacts.favorite.destroy', $artifact) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="av-btn av-btn--outline" style="font-size:.82rem; padding: 4px 12px; color: #ef4444; border-color: rgba(239,68,68,.3);">
                                        ♥ Favorited
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('artifacts.favorite.store', $artifact) }}" style="display:inline;">
                                    @csrf
                                    <button class="av-btn av-btn--outline" style="font-size:.82rem; padding: 4px 12px; color: var(--ink-500); border-color: var(--color-border);">
                                        ♡ Favorite
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>

                    @if ($artifact->tags->isNotEmpty())
                        <div class="flex gap-2" style="flex-wrap: wrap; margin-bottom: var(--space-4);">
                            @foreach ($artifact->tags as $tag)
                                <x-tag variant="muted" class="av-tag--pill">{{ $tag->name }}</x-tag>
                            @endforeach
                        </div>
                    @endif

                    @if ($artifact->description)
                        <p style="white-space: pre-line;">{{ $artifact->description }}</p>
                    @endif

                    <x-card class="mt-6">
                        <span class="av-card__eyebrow">Details</span>
                        <dl style="margin: 0; font-size: var(--text-sm);">
                            @foreach ([
                                'Civilization' => $artifact->civilization,
                                'Era' => $artifact->era,
                                'Century' => $artifact->century,
                                'Material' => $artifact->material?->name,
                                'Country of origin' => $artifact->country_of_origin,
                                'Region' => $artifact->region,
                                'Discovery location' => $artifact->discovery_location,
                                'Dimensions' => $artifact->dimensions,
                                'Weight' => $artifact->weight,
                                'Condition' => $artifact->condition,
                            ] as $label => $value)
                                @if ($value)
                                    <div class="flex-between" style="padding: 6px 0; border-bottom: 1px solid var(--color-border);">
                                        <dt style="color: var(--stone-600);">{{ $label }}</dt>
                                        <dd style="margin: 0; font-weight: 600;">{{ $value }}</dd>
                                    </div>
                                @endif
                            @endforeach
                        </dl>
                    </x-card>

                    <x-card class="mt-6">
                        <span class="av-card__eyebrow">{{ $artifact->museum ? 'Held by' : 'Collection' }}</span>
                        @if ($artifact->museum)
                            <h3>{{ $artifact->museum->name }}</h3>
                            <a href="{{ route('museums.show', $artifact->museum) }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">View museum profile &rarr;</a>
                        @else
                            <h3>Private collection</h3>
                            <p style="margin: 0; font-size: var(--text-sm);">Held by {{ $artifact->collector->name }}.</p>
                        @endif
                    </x-card>

                    @if ($artifact->documents->isNotEmpty())
                        <x-card class="mt-6">
                            <span class="av-card__eyebrow">Documents</span>
                            @auth
                                @foreach ($artifact->documents as $document)
                                    <div class="mt-2">
                                        <a href="{{ $document->url() }}" target="_blank" rel="noopener" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">{{ $document->title }}</a>
                                        @if ($document->document_type)
                                            <span style="color: var(--stone-600); font-size: var(--text-xs);"> &middot; {{ $document->document_type }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <p style="margin: 0; font-size: var(--text-sm);">
                                    {{ $artifact->documents->count() }} document(s) available.
                                    <a href="{{ route('login') }}" style="color: var(--brass-700); font-weight: 600;">Sign in</a> to download.
                                </p>
                            @endauth
                        </x-card>
                    @endif

                    {{-- ── Phase 15: QR Code panel ─────────────────────────────── --}}
                    @auth
                        @can('view', $artifact)
                            <div class="mt-6">
                                @include('artifacts.partials.qr-panel', ['artifact' => $artifact])
                            </div>
                        @endcan
                    @endauth

                    {{-- ── Phase 16: Certificate of Authenticity ───────────────── --}}
                    @auth
                        @can('view', $artifact)
                            @php
                                $activeCert = $artifact->certificates
                                    ->where('type', \App\Models\Certificate::TYPE_VERIFICATION)
                                    ->whereNull('revoked_at')
                                    ->first();
                            @endphp
                            @if($activeCert)
                            <div class="mt-6">
                                <div style="border:1px solid rgba(212,175,55,.3); border-radius:var(--radius-md); background:rgba(212,175,55,.05); overflow:hidden;">
                                    <div style="padding:var(--space-3) var(--space-4); background:rgba(212,175,55,.1); border-bottom:1px solid rgba(212,175,55,.25); display:flex; align-items:center; gap:var(--space-2);">
                                        <span style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brass-700);">🏛 Certificate of Authenticity</span>
                                        <span style="margin-left:auto; font-size:.68rem; font-weight:700; color:#10b981; text-transform:uppercase; letter-spacing:.05em;">✓ Issued</span>
                                    </div>
                                    <div style="padding:var(--space-3) var(--space-4);">
                                        <p style="font-size:.75rem; color:var(--color-muted); margin:0 0 var(--space-2);">
                                            Serial: <span style="font-family:var(--font-mono); color:var(--color-body);">{{ $activeCert->serial }}</span>
                                        </p>
                                        <p style="font-size:.72rem; color:var(--color-muted); margin:0 0 var(--space-3);">
                                            Issued {{ $activeCert->created_at->format('d M Y') }}
                                        </p>
                                        <div class="flex" style="gap:var(--space-2); flex-wrap:wrap;">
                                            <a href="{{ route('certificates.download', $activeCert) }}"
                                               class="av-btn av-btn--outline"
                                               style="font-size:.75rem; padding:4px 12px;"
                                               download>⬇ PDF</a>
                                            <a href="{{ $activeCert->verificationUrl() }}"
                                               style="font-size:.75rem; font-weight:600; color:var(--brass-700); text-decoration:none; display:flex; align-items:center;">
                                               Verify →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @elseif($artifact->isVerified())
                            <div class="mt-6">
                                <form method="POST" action="{{ route('artifacts.certificate.issue', $artifact) }}">
                                    @csrf
                                    <button type="submit"
                                            class="av-btn av-btn--outline"
                                            style="width:100%; font-size:.82rem;"
                                            onclick="return confirm('Issue a Certificate of Authenticity for this artifact?')">
                                        🏛 Issue Certificate of Authenticity
                                    </button>
                                </form>
                            </div>
                            @endif
                        @endcan
                    @endauth

                </div>

            </div>

            @if ($relatedArtifacts->isNotEmpty())
                <div class="mt-8" style="border-top: 1px solid var(--color-border); padding-top: var(--space-8);">
                    <x-section-heading eyebrow="You Might Also Like">
                        <h2>Related artifacts</h2>
                    </x-section-heading>
                    <div class="grid grid-4">
                        @foreach ($relatedArtifacts as $related)
                            <x-card class="av-card--media" data-reveal>
                                <img src="{{ $related->primaryImageUrl() }}" alt="{{ $related->name }}" class="av-card--media__image" loading="lazy">
                                <div class="av-card--media__body">
                                    <x-tag>{{ $related->category->name }}</x-tag>
                                    <h3 class="mt-4" style="font-size: var(--text-lg);">{{ $related->name }}</h3>
                                    <a href="{{ route('artifacts.show', $related) }}" style="color: var(--brass-700); font-weight: 600; font-size: var(--text-sm);">View &rarr;</a>
                                </div>
                            </x-card>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
