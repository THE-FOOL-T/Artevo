@php use Illuminate\Support\Str; @endphp

@extends('layouts.app')

@section('title', "{$collection->name} — Artevo Collections")
@section('meta_description', $collection->description ? Str::limit($collection->description, 155) : "Explore the {$collection->name} collection on Artevo.")


@section('content')
    {{-- Cover hero --}}
    <section style="position: relative; background: var(--ink-900); min-height: 340px; display: flex; align-items: flex-end; overflow: hidden;">
        @if($collection->coverImageUrl())
            <img src="{{ $collection->coverImageUrl() }}" alt="{{ $collection->name }}"
                 style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.35;">
        @endif
        <div style="position: relative; width: 100%; padding: var(--space-10) 0 var(--space-8);">
            <div class="container">
                <p style="margin: 0 0 var(--space-4);"><a href="{{ route('collections.index') }}" style="color: var(--stone-400); font-size: var(--text-sm);">← All collections</a></p>
                <x-tag style="background: rgba(169,129,46,0.2); color: var(--brass-600); border-color: rgba(169,129,46,0.3);">
                    {{ $collection->museum?->name ?? $collection->collector?->name ?? 'Private collection' }}
                </x-tag>
                <h1 style="color: var(--parchment-100); font-family: var(--font-display); font-size: var(--text-4xl); margin: var(--space-3) 0 var(--space-2);">
                    {{ $collection->name }}
                </h1>
                @if($collection->description)
                    <p style="color: var(--stone-400); max-width: 580px; font-size: var(--text-lg); margin: 0;">{{ $collection->description }}</p>
                @endif

                {{-- Stats row --}}
                <div class="flex gap-6" style="margin-top: var(--space-5); flex-wrap: wrap;">
                    <span style="color: var(--stone-400); font-size: var(--text-sm);">
                        🏺 <strong style="color: var(--parchment-100);">{{ $artifacts->count() }}</strong> artifact{{ $artifacts->count() !== 1 ? 's' : '' }}
                    </span>
                    <span style="color: var(--stone-400); font-size: var(--text-sm);">
                        👁 <strong style="color: var(--parchment-100);">{{ number_format($collection->views_count) }}</strong> views
                    </span>
                    <span style="color: var(--stone-400); font-size: var(--text-sm);">
                        ♥ <strong style="color: var(--parchment-100);" id="favorites-count">{{ $favoritesCount }}</strong> favorites
                    </span>
                    @if($collection->is_featured)
                        <span style="background: var(--brass-600); color: #fff; font-size: var(--text-xs); font-weight: 700; padding: 3px 12px; border-radius: 999px; letter-spacing: 0.05em;">FEATURED</span>
                    @endif
                </div>

                {{-- Favorite button --}}
                @auth
                    <div style="margin-top: var(--space-5);">
                        @if($collection->isPublic())
                            <button id="favorite-btn"
                                    data-collection-id="{{ $collection->id }}"
                                    data-favorited="{{ $userHasFavorited ? 'true' : 'false' }}"
                                    data-favorite-url="{{ route('collections.favorite', $collection) }}"
                                    data-unfavorite-url="{{ route('collections.unfavorite', $collection) }}"
                                    class="av-btn {{ $userHasFavorited ? 'av-btn--primary' : '' }}"
                                    style="{{ !$userHasFavorited ? 'background: rgba(255,255,255,0.1); color: var(--parchment-100); border: 1px solid rgba(237,231,216,0.2);' : '' }}">
                                <span id="favorite-label">{{ $userHasFavorited ? '♥ Favorited' : '♡ Add to favorites' }}</span>
                            </button>
                        @endif
                    </div>
                @endauth
            </div>
        </div>
    </section>

    <section class="av-section av-section--white" style="padding: var(--space-10) 0;">
        <div class="container">
            @if($artifacts->isNotEmpty())
                <x-section-heading eyebrow="Artifacts in this collection">
                    <h2>{{ $artifacts->count() }} artifact{{ $artifacts->count() !== 1 ? 's' : '' }}</h2>
                </x-section-heading>

                <div class="grid grid-3" style="margin-top: var(--space-8); gap: var(--space-6);">
                    @foreach($artifacts as $artifact)
                        <article class="av-card av-card--media" style="display: flex; flex-direction: column; height: 100%; border-radius: var(--radius-lg); overflow: hidden;" data-reveal>
                            <a href="{{ route('artifacts.show', $artifact) }}" style="display: block; position: relative; aspect-ratio: 16/9; overflow: hidden; background: var(--color-bg-alt);" tabindex="-1" aria-hidden="true">
                                <img src="{{ $artifact->primaryImageUrl() }}"
                                     alt="{{ $artifact->name }}"
                                     style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s var(--ease-out);"
                                     loading="lazy">
                                <div style="position: absolute; top: var(--space-3); left: var(--space-3); display: flex; gap: var(--space-2);">
                                    <x-tag style="background: rgba(15,12,20,0.85); color: var(--parchment-100); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(4px);">
                                        {{ $artifact->category->name }}
                                    </x-tag>
                                </div>
                            </a>
                            <div class="av-card--media__body" style="padding: var(--space-5); display: flex; flex-direction: column; flex: 1; gap: var(--space-2);">
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.78rem; color: var(--brass-700); font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em;">
                                    <span>🏛 {{ $artifact->museum->name ?? 'Private Collection' }}</span>
                                    @if($artifact->civilization)
                                        <span style="color: var(--stone-500); font-weight: 500; text-transform: none;">{{ $artifact->civilization }}</span>
                                    @endif
                                </div>
                                <h3 style="font-size: 1.25rem; font-family: var(--font-display); margin: 0; line-height: 1.3;">
                                    <a href="{{ route('artifacts.show', $artifact) }}" style="color: var(--color-text); text-decoration: none;">
                                        {{ $artifact->name }}
                                    </a>
                                </h3>
                                <div style="margin-top: auto; padding-top: var(--space-4); border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: space-between;">
                                    <span style="font-size: 0.82rem; color: var(--stone-500);">
                                        @if($artifact->estimated_value)
                                            Est. ${{ number_format($artifact->estimated_value) }}
                                        @else
                                            Verified Artifact
                                        @endif
                                    </span>
                                    <a href="{{ route('artifacts.show', $artifact) }}" style="color: var(--brass-700); font-weight: 600; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                        View Details &rarr;
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: var(--space-16) 0; color: var(--stone-600);">
                    <p style="font-size: 3rem; margin-bottom: var(--space-4);">🏺</p>
                    <h3>This collection is empty</h3>
                    <p>No artifacts have been added yet.</p>
                </div>
            @endif

            {{-- Related collections --}}
            @if(!empty($relatedCollections) && $relatedCollections->isNotEmpty())
                <div style="margin-top: var(--space-12); border-top: 1px solid var(--color-border); padding-top: var(--space-10);">
                    <x-section-heading eyebrow="From the same source">
                        <h2>Related collections</h2>
                    </x-section-heading>

                    <div class="grid grid-3" style="margin-top: var(--space-6); gap: var(--space-6);">
                        @foreach($relatedCollections as $related)
                            <article class="av-card" data-reveal style="border-radius: var(--radius-lg); overflow: hidden; border: 1px solid var(--color-border); background: var(--white); display: flex; flex-direction: column;">
                                <a href="{{ route('collections.show', $related) }}" style="display: block; aspect-ratio: 16/9; background: var(--porcelain-100); overflow: hidden; text-decoration: none;" tabindex="-1" aria-hidden="true">
                                    @if($related->coverImageUrl())
                                        <img src="{{ $related->coverImageUrl() }}" alt="{{ $related->name }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s var(--ease-out);" loading="lazy">
                                    @else
                                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--stone-400); font-size: 2rem;">🏛️</div>
                                    @endif
                                </a>
                                <div style="padding: var(--space-5); display: flex; flex-direction: column; gap: var(--space-2); flex: 1;">
                                    <h3 style="margin: 0; font-size: 1.15rem; font-family: var(--font-display);">
                                        <a href="{{ route('collections.show', $related) }}" style="color: var(--color-text); text-decoration: none;">{{ $related->name }}</a>
                                    </h3>
                                    <p style="margin: 0; font-size: 0.85rem; color: var(--stone-600);">{{ $related->artifacts_count ?? 0 }} artifact{{ ($related->artifacts_count ?? 0) !== 1 ? 's' : '' }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
<script>
(function () {
    const btn = document.getElementById('favorite-btn');
    if (!btn) return;

    const countEl = document.getElementById('favorites-count');
    const label = document.getElementById('favorite-label');
    let favorited = btn.dataset.favorited === 'true';

    btn.addEventListener('click', async function () {
        const url = favorited ? btn.dataset.unfavoriteUrl : btn.dataset.favoriteUrl;
        const method = favorited ? 'DELETE' : 'POST';

        btn.disabled = true;

        try {
            const res = await fetch(url, {
                method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            const data = await res.json();
            favorited = data.favorited;
            countEl.textContent = data.count;
            label.textContent = favorited ? '♥ Favorited' : '♡ Add to favorites';

            if (favorited) {
                btn.classList.add('av-btn--primary');
                btn.style.cssText = '';
            } else {
                btn.classList.remove('av-btn--primary');
                btn.style.cssText = 'background: rgba(255,255,255,0.1); color: var(--parchment-100); border: 1px solid rgba(237,231,216,0.2);';
            }
        } catch (e) {
            console.error(e);
        } finally {
            btn.disabled = false;
        }
    });
})();
</script>
@endpush
