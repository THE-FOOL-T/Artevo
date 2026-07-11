@extends('layouts.app')

@section('title', 'Something Went Wrong — Artevo')

@section('content')
<div class="av-error-page">
    <div>
        <span class="av-error-page__code">ERROR 500</span>
        <h1>Something went wrong on our end</h1>
        <p>Our team has been notified. Please try again shortly, or contact us if the problem continues.</p>
        <x-button href="{{ route('home') }}" variant="primary" class="mt-6">Return home</x-button>
    </div>
</div>
@endsection
