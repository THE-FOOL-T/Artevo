<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Artevo')</title>

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22><rect width=%2224%22 height=%2224%22 rx=%224%22 fill=%22%23F8F5EF%22/><text x=%2212%22 y=%2216%22 font-size=%2210%22 fill=%22%23A9812E%22 text-anchor=%22middle%22 font-family=%22monospace%22>AV</text></svg>">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="av-auth-shell">
        <div style="width: 100%; max-width: 440px;">
            <a href="{{ route('home') }}" class="av-auth-card__brand">
                <span class="av-nav__brand-mark" aria-hidden="true">AV</span>
                Artevo
            </a>

            <div class="av-auth-card">
                @if (session('success'))
                    <x-alert type="success">{{ session('success') }}</x-alert>
                @endif
                @if (session('status'))
                    <x-alert type="success">{{ session('status') }}</x-alert>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </div>
    </div>

    @if (session('success') || session('status'))
        <div data-flash="{{ session('success') ?? session('status') }}" data-flash-type="success" hidden></div>
    @endif
</body>
</html>
