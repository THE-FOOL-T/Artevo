{{--
    Reusable site navigation.
    Nav links intentionally list only routes that exist as of Phase 1
    (Home, About, Contact). Museums / Artifacts / Exhibitions / Auctions
    links are added to this same component in the phases that build them —
    never hard-code a link to a route that doesn't exist yet.
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
            @guest
                <a href="{{ route('login') }}" class="av-btn av-btn--outline-dark">Sign in</a>
                <a href="{{ route('register') }}" class="av-btn av-btn--primary">Join Artevo</a>
            @else
                <x-dropdown>
                    <x-slot name="trigger">
                        <x-avatar :user="auth()->user()" />
                        <span style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ auth()->user()->name }}</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </x-slot>

                    <a href="{{ route('dashboard') }}" class="av-dropdown__item">Dashboard</a>
                    <a href="{{ route('profile.edit') }}" class="av-dropdown__item">Profile</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}" class="av-dropdown__item">Manage users</a>
                    @endif
                    <hr class="av-dropdown__divider">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="av-dropdown__item">Log out</button>
                    </form>
                </x-dropdown>
            @endguest

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
        <hr style="border-color: var(--color-border); width: 100%;">
        @guest
            <a href="{{ route('login') }}">Sign in</a>
            <a href="{{ route('register') }}" class="av-btn av-btn--primary av-btn--block">Join Artevo</a>
        @else
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('profile.edit') }}">Profile</a>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}">Manage users</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="av-btn av-btn--outline-dark av-btn--block">Log out</button>
            </form>
        @endguest
    </div>
</header>
