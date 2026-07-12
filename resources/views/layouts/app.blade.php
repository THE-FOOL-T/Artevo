<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Artevo — Digital Museum, Exhibition & Auction Platform')</title>
    <meta name="description" content="@yield('meta_description', 'Artevo is a digital museum ecosystem where museums, collectors, historians and researchers preserve, verify, exhibit and auction historical artifacts.')">

    {{-- Open Graph / social sharing --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Artevo">
    <meta property="og:title" content="@yield('title', 'Artevo — Digital Museum, Exhibition & Auction Platform')">
    <meta property="og:description" content="@yield('meta_description', 'A digital museum ecosystem for preserving, verifying and exhibiting historical artifacts.')">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22><rect width=%2224%22 height=%2224%22 rx=%224%22 fill=%22%23F8F5EF%22/><text x=%2212%22 y=%2216%22 font-size=%2210%22 fill=%22%23A9812E%22 text-anchor=%22middle%22 font-family=%22monospace%22>AV</text></svg>">

    @stack('meta')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{-- Keyboard/screen-reader users can jump straight past navigation --}}
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <x-navbar />

    <main id="main-content">
        @yield('content')
    </main>

    <x-footer />

    {{-- Session flash message handed to toast.js as a data attribute so
         redirect-based flows (e.g. the contact form) surface a toast the
         same way a future AJAX call would. --}}
    @if (session('success') || session('error'))
        <div data-flash="{{ session('success') ?? session('error') }}"
             data-flash-type="{{ session('success') ? 'success' : 'error' }}"
             hidden></div>
    @endif

    @stack('scripts')
</body>
</html>
