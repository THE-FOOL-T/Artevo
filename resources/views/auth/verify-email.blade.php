@extends('layouts.guest')

@section('title', 'Verify Your Email — Artevo')

@section('content')

    <h1 style="font-size: var(--text-2xl); text-align: center;">Verify your email</h1>
    <p style="text-align: center;">
        Thanks for signing up! Before getting started, please confirm your email address by clicking the
        link we just sent you. Didn't get it? We'll gladly send another.
    </p>

    @if (session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <x-button type="submit" variant="primary" block>Resend verification email</x-button>
    </form>

    <form method="POST" action="{{ route('logout') }}" style="margin-top: var(--space-4);">
        @csrf
        <x-button type="submit" variant="outline-dark" block>Log out</x-button>
    </form>

@endsection
