{{--
    Reusable site navigation.
   
--}}
<header class="av-nav" data-nav>
    <div class="container av-nav__inner">
        <a href="{{ route('home') }}" class="av-nav__brand" aria-label="Artevo home">
            <span class="av-nav__brand-mark" aria-hidden="true">AV</span>
            Artevo
        </a>

        <nav aria-label="Primary">
            <ul class="av-nav__links">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a></li>
                <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'is-active' : '' }}">About</a></li>
                <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'is-active' : '' }}">Contact</a></li>
            </ul>
        </nav>

        <div class="av-nav__actions">
            <a href="/login" class="av-btn av-btn--ghost">Sign in</a>
            <a href="/register" class="av-btn av-btn--primary">Join Artevo</a>

            <button type="button" class="av-nav__toggle" data-nav-toggle aria-expanded="false" aria-controls="mobile-nav-panel" aria-label="Toggle navigation menu">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <line x1="3" y1="6" x2="21" y2="6" />
                    <line x1="3" y1="12" x2="21" y2="12" />
                    <line x1="3" y1="18" x2="21" y2="18" />
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-nav-panel" class="av-nav__mobile-panel" data-nav-mobile>
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('about') }}">About</a>
        <a href="{{ route('contact') }}">Contact</a>
        <hr style="border-color: rgba(243,71,30,0.2); width: 100%;">
        <a href="/login">Sign in</a>
        <a href="/register" class="av-btn av-btn--primary av-btn--block">Join Artevo</a>
    </div>
</header>
