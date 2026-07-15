@extends('layouts.guest')

@section('title', 'Sign In — Artevo')

@section('content')

    <h1 style="font-size: var(--text-2xl); text-align: center;">Welcome back</h1>
    <p style="text-align: center;">Sign in to your Artevo account.</p>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="av-field">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email') <span class="av-field__error">{{ $message }}</span> @enderror
        </div>

        <div class="av-field">
            <div class="flex-between">
                <label for="password" style="margin-bottom: 0;">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="font-size: var(--text-sm); color: var(--brass-700);">Forgot password?</a>
                @endif
            </div>
            <div class="av-field__input-group" style="margin-top: var(--space-2);">
                <input type="password" id="password" name="password" required autocomplete="current-password" data-password-toggle-input>
                <button type="button" class="av-field__input-action" data-password-toggle aria-label="Show password">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            @error('password') <span class="av-field__error">{{ $message }}</span> @enderror
        </div>

        <div class="av-field" style="display: flex; align-items: center; gap: var(--space-2);">
            <input type="checkbox" id="remember" name="remember" style="width: auto;">
            <label for="remember" style="margin-bottom: 0; font-weight: 400;">Remember me</label>
        </div>

        <x-button type="submit" variant="primary" block>Sign in</x-button>
    </form>

    <p class="av-auth-card__footer">
        Don't have an account? <a href="{{ route('register') }}" style="color: var(--brass-700); font-weight: 600;">Create one</a>
    </p>

@endsection
