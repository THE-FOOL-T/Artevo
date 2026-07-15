@extends('layouts.guest')

@section('title', 'Reset Your Password — Artevo')

@section('content')

    <h1 style="font-size: var(--text-2xl); text-align: center;">Forgot your password?</h1>
    <p style="text-align: center;">Enter your email and we'll send you a link to reset it.</p>

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="av-field">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email') <span class="av-field__error">{{ $message }}</span> @enderror
        </div>

        <x-button type="submit" variant="primary" block>Email password reset link</x-button>
    </form>

    <p class="av-auth-card__footer">
        <a href="{{ route('login') }}" style="color: var(--brass-700); font-weight: 600;">Back to sign in</a>
    </p>

@endsection
