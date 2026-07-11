@extends('layouts.app')

@section('title', 'Too Many Requests — Artevo')

@section('content')
<div class="av-error-page">
    <div>
        <span class="av-error-page__code">ERROR 429</span>
        <h1>Slow down a moment</h1>
        <p>You've made too many requests in a short time. Please wait a minute and try again.</p>
        <x-button href="{{ route('home') }}" variant="primary" class="mt-6">Return home</x-button>
    </div>
</div>
@endsection
