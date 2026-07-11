@extends('layouts.app')

@section('title', 'Access Restricted — Artevo')

@section('content')
<div class="av-error-page">
    <div>
        <span class="av-error-page__code">ERROR 403</span>
        <h1>This exhibit is restricted</h1>
        <p>You don't have permission to view this page. If you believe this is a mistake, contact us.</p>
        <x-button href="{{ route('home') }}" variant="primary" class="mt-6">Return home</x-button>
    </div>
</div>
@endsection
