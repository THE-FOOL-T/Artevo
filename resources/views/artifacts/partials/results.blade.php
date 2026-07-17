@if ($artifacts->isEmpty())
    <x-card class="mt-8" style="text-align: center; padding: var(--space-12) var(--space-6); background: var(--color-bg-alt); border: 1px dashed var(--color-border);">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="margin: 0 auto var(--space-3); color: var(--brass-600); opacity: 0.6;"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
        <h3 style="font-family: var(--font-display); font-size: 1.35rem; margin-bottom: var(--space-2); color: var(--ink-900);">No artifacts found</h3>
        <p style="margin: 0; color: var(--color-text-muted);">Try adjusting your filters or search keywords above.</p>
    </x-card>
@else
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: var(--space-6); margin-bottom: var(--space-4); border-bottom: 1px solid var(--color-border); padding-bottom: var(--space-3);">
        <span style="font-size: 0.88rem; font-weight: 600; color: var(--stone-600);">
            Showing <strong style="color: var(--ink-900);">{{ $artifacts->firstItem() }}–{{ $artifacts->lastItem() }}</strong> of <strong style="color: var(--ink-900);">{{ $artifacts->total() }}</strong> verified artifacts
        </span>
        <div style="display: flex; gap: var(--space-2); align-items: center;">
            <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--stone-500); font-weight: 700;">Layout:</span>
            <button type="button" class="av-btn av-btn--sm av-btn--outline" id="view-toggle-grid" style="padding: 4px 10px; font-size: 0.75rem; background: var(--ink-900); color: #fff; border-color: var(--ink-900);">Grid</button>
            <button type="button" class="av-btn av-btn--sm av-btn--outline" id="view-toggle-list" style="padding: 4px 10px; font-size: 0.75rem;">List</button>
        </div>
    </div>

    <div class="grid grid-3" style="gap: var(--space-5);" id="artifact-results-grid">
        @foreach ($artifacts as $artifact)
            <article class="av-card av-card--media artifact-card-item" style="display: flex; flex-direction: column; height: 100%; border-radius: var(--radius-lg); overflow: hidden; border: 1px solid var(--color-border); background: var(--white); transition: transform 0.3s var(--ease-out), box-shadow 0.3s var(--ease-out);" data-reveal>
                <a href="{{ route('artifacts.show', $artifact) }}" class="artifact-card-img-link" style="display: block; position: relative; aspect-ratio: 16/10; overflow: hidden; background: var(--color-bg-alt); flex-shrink: 0;" tabindex="-1" aria-hidden="true">
                    <img src="{{ $artifact->primaryImageUrl() }}" 
                         alt="{{ $artifact->name }}" 
                         class="av-card--media__image" 
                         style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s var(--ease-out);" 
                         loading="lazy">
                    <div style="position: absolute; top: var(--space-3); left: var(--space-3); display: flex; gap: var(--space-2);">
                        <x-tag style="background: rgba(15,12,20,0.85); color: var(--parchment-100); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(4px);">
                            {{ $artifact->category->name }}
                        </x-tag>
                    </div>
                </a>
                <div class="av-card--media__body artifact-card-body" style="padding: var(--space-4); display: flex; flex-direction: column; flex: 1; gap: var(--space-2);">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.76rem; color: var(--brass-700); font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">
                        <span>🏛 {{ Str::limit($artifact->museum->name ?? 'Private Collection', 32) }}</span>
                        @if($artifact->civilization)
                            <span style="color: var(--stone-500); font-weight: 500; text-transform: none;">{{ $artifact->civilization }}</span>
                        @endif
                    </div>
                    <h3 style="font-size: 1.22rem; font-family: var(--font-display); margin: 0; line-height: 1.3;">
                        <a href="{{ route('artifacts.show', $artifact) }}" style="color: var(--color-text); text-decoration: none;">
                            {{ $artifact->name }}
                        </a>
                    </h3>
                    @if ($artifact->short_description)
                        <p class="artifact-card-desc" style="font-size: 0.88rem; color: var(--color-text-muted); line-height: 1.55; margin: var(--space-1) 0 0; flex: 1;">
                            {{ Str::limit($artifact->short_description, 95) }}
                        </p>
                    @endif
                    <div style="margin-top: auto; padding-top: var(--space-3); border-top: 1px solid var(--color-border); display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.82rem; color: var(--stone-600); font-weight: 600;">
                            @if($artifact->estimated_value)
                                Est. ${{ number_format($artifact->estimated_value) }}
                            @else
                                Verified Artifact
                            @endif
                        </span>
                        <a href="{{ route('artifacts.show', $artifact) }}" style="color: var(--brass-700); font-weight: 600; font-size: 0.84rem; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                            Explore Lot &rarr;
                        </a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div style="margin-top: var(--space-8);">
        {{ $artifacts->links() }}
    </div>

    <style>
    /* View switcher styling */
    #artifact-results-grid.is-list-view {
        grid-template-columns: 1fr !important;
    }
    #artifact-results-grid.is-list-view .artifact-card-item {
        flex-direction: row !important;
        align-items: stretch;
    }
    #artifact-results-grid.is-list-view .artifact-card-img-link {
        width: 280px !important;
        aspect-ratio: auto !important;
        min-height: 200px;
    }
    @media (max-width: 640px) {
        #artifact-results-grid.is-list-view .artifact-card-item {
            flex-direction: column !important;
        }
        #artifact-results-grid.is-list-view .artifact-card-img-link {
            width: 100% !important;
            aspect-ratio: 16/9 !important;
        }
    }
    </style>
    <script>
    (function() {
        const gridBtn = document.getElementById('view-toggle-grid');
        const listBtn = document.getElementById('view-toggle-list');
        const grid = document.getElementById('artifact-results-grid');
        if (!gridBtn || !listBtn || !grid) return;

        function setView(mode) {
            if (mode === 'list') {
                grid.classList.add('is-list-view');
                listBtn.style.background = 'var(--ink-900)';
                listBtn.style.color = '#fff';
                gridBtn.style.background = 'transparent';
                gridBtn.style.color = 'var(--ink-900)';
                localStorage.setItem('artevo_view_mode', 'list');
            } else {
                grid.classList.remove('is-list-view');
                gridBtn.style.background = 'var(--ink-900)';
                gridBtn.style.color = '#fff';
                listBtn.style.background = 'transparent';
                listBtn.style.color = 'var(--ink-900)';
                localStorage.setItem('artevo_view_mode', 'grid');
            }
        }

        gridBtn.addEventListener('click', () => setView('grid'));
        listBtn.addEventListener('click', () => setView('list'));

        if (localStorage.getItem('artevo_view_mode') === 'list') {
            setView('list');
        }
    })();
    </script>
@endif
