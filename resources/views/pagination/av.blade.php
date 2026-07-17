@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" style="display: flex; align-items: center; justify-content: center; gap: var(--space-2); flex-wrap: wrap;">
        @if ($paginator->onFirstPage())
            <span class="av-btn av-btn--outline-dark" style="opacity: 0.4; pointer-events: none; padding: 0.5rem 0.9rem;" aria-disabled="true">&larr;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="av-btn av-btn--outline-dark" style="padding: 0.5rem 0.9rem;" rel="prev">&larr;</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="padding: 0.5rem 0.4rem; color: var(--stone-600); font-size: var(--text-sm);">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="av-btn av-btn--primary" style="padding: 0.5rem 0.9rem;" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="av-btn av-btn--outline-dark" style="padding: 0.5rem 0.9rem;">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="av-btn av-btn--outline-dark" style="padding: 0.5rem 0.9rem;" rel="next">&rarr;</a>
        @else
            <span class="av-btn av-btn--outline-dark" style="opacity: 0.4; pointer-events: none; padding: 0.5rem 0.9rem;" aria-disabled="true">&rarr;</span>
        @endif
    </nav>
@endif
