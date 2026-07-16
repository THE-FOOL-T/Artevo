{{--
    Reusable site navigation.
    Exhibitions / Auctions links are added to this same component in the
    phases that build them — never hard-code a link to a route that
    doesn't exist yet.
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
                <li><a href="{{ route('museums.index') }}" class="{{ request()->routeIs('museums.*') ? 'is-active' : '' }}">Museums</a></li>
                <li><a href="{{ route('artifacts.index') }}" class="{{ request()->routeIs('artifacts.*') ? 'is-active' : '' }}">Artifacts</a></li>
                <li><a href="{{ route('collections.index') }}" class="{{ request()->routeIs('collections.*') ? 'is-active' : '' }}">Collections</a></li>
                <li><a href="{{ route('exhibitions.index') }}" class="{{ request()->routeIs('exhibitions.*') ? 'is-active' : '' }}">Exhibitions</a></li>
                <li><a href="{{ route('auctions.index') }}" class="{{ request()->routeIs('auctions.*') ? 'is-active' : '' }}" style="position:relative;">Auctions</a></li>
                <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'is-active' : '' }}">About</a></li>
                <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'is-active' : '' }}">Contact</a></li>
            </ul>
        </nav>

        <div class="av-nav__actions">
            @guest
                <a href="{{ route('login') }}" class="av-btn av-btn--outline-dark">Sign in</a>
                <a href="{{ route('register') }}" class="av-btn av-btn--primary">Join Artevo</a>
            @else
                <x-notification-bell />

                <x-dropdown>
                    <x-slot name="trigger">
                        <x-avatar :user="auth()->user()" />
                        <span style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ auth()->user()->name }}</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </x-slot>

                    <a href="{{ route('dashboard') }}" class="av-dropdown__item">Dashboard</a>
                    <a href="{{ route('profile.edit') }}" class="av-dropdown__item">Profile</a>
                    @if (auth()->user()->isCurator() || auth()->user()->isAdmin())
                        <a href="{{ route('curator.museums.index') }}" class="av-dropdown__item">My museums</a>
                    @endif
                    @if (auth()->user()->isCollector())
                        <a href="{{ route('collector.artifacts.index') }}" class="av-dropdown__item">My collection</a>
                        <a href="{{ route('collector.collections.index') }}" class="av-dropdown__item">My collections</a>
                    @endif
                    @if(auth()->user()->isCollector() || auth()->user()->isCurator())
                        <a href="{{ route('certificates.index') }}" class="av-dropdown__item">My Certificates</a>
                    @endif
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}" class="av-dropdown__item">Manage users</a>
                        <a href="{{ route('admin.activity-logs.index') }}" class="av-dropdown__item">Activity log</a>
                        <a href="{{ route('admin.donations.index') }}" class="av-dropdown__item">Donations</a>
                        <a href="{{ route('admin.qr-codes.index') }}" class="av-dropdown__item">QR Analytics</a>
                        <a href="{{ route('admin.certificates.index') }}" class="av-dropdown__item">Certificates</a>
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
        <a href="{{ route('museums.index') }}">Museums</a>
        <a href="{{ route('artifacts.index') }}">Artifacts</a>
        <a href="{{ route('collections.index') }}">Collections</a>
        <a href="{{ route('exhibitions.index') }}">Exhibitions</a>
        <a href="{{ route('auctions.index') }}">Auctions</a>
        <a href="{{ route('about') }}">About</a>
        <a href="{{ route('contact') }}">Contact</a>
        <hr style="border-color: var(--color-border); width: 100%;">
        @guest
            <a href="{{ route('login') }}">Sign in</a>
            <a href="{{ route('register') }}" class="av-btn av-btn--primary av-btn--block">Join Artevo</a>
        @else
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('profile.edit') }}">Profile</a>
            <a href="{{ route('notifications.index') }}">Notifications</a>
            @if (auth()->user()->isCurator() || auth()->user()->isAdmin())
                <a href="{{ route('curator.museums.index') }}">My museums</a>
            @endif
            @if (auth()->user()->isCollector())
                <a href="{{ route('collector.artifacts.index') }}">My collection</a>
                <a href="{{ route('collector.collections.index') }}">My collections</a>
            @endif
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}">Manage users</a>
                <a href="{{ route('admin.activity-logs.index') }}">Activity log</a>
                <a href="{{ route('admin.donations.index') }}">Donations</a>
                <a href="{{ route('admin.qr-codes.index') }}">QR Analytics</a>
                <a href="{{ route('admin.certificates.index') }}">Certificates</a>
            @endif
            @if(auth()->user()->isCollector() || auth()->user()->isCurator())
                <a href="{{ route('certificates.index') }}">My Certificates</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="av-btn av-btn--outline-dark av-btn--block">Log out</button>
            </form>
        @endguest
    </div>
</header>
