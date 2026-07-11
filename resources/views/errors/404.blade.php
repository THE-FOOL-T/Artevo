@extends('layouts.app')

@section('title', 'Page Not Found — Artevo')

@section('content')
<div class="av-error-page">
    <div>
        <span class="av-error-page__code">ERROR 404</span>
        <h1>This piece isn't in the collection</h1>
        <p>The page you're looking for may have been moved, archived elsewhere, or never existed.</p>
        <x-button href="{{ route('home') }}" variant="primary" class="mt-6">Return home</x-button>
    </div>
</div>
@endsection
