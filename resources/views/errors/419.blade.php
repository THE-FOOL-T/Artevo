@extends('layouts.app')

@section('title', 'Page Expired — Artevo')

@section('content')
<div class="av-error-page">
    <div>
        <span class="av-error-page__code">ERROR 419</span>
        <h1>This page expired</h1>
        <p>Your session timed out for security reasons. Please go back and try again.</p>
        <x-button href="{{ route('home') }}" variant="primary" class="mt-6">Return home</x-button>
    </div>
</div>
@endsection
