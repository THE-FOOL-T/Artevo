@extends('layouts.guest')

@section('title', 'Create an Account — Artevo')

@section('content')

    <h1 style="font-size: var(--text-2xl); text-align: center;">Create your archive</h1>
    <p style="text-align: center;">Join Artevo as a museum, collector, or researcher.</p>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="av-field">
            <label for="name">Full name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name') <span class="av-field__error">{{ $message }}</span> @enderror
        </div>

        <div class="av-field">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            @error('email') <span class="av-field__error">{{ $message }}</span> @enderror
        </div>

        <div class="av-field">
            <label for="password">Password</label>
            <div class="av-field__input-group">
                <input type="password" id="password" name="password" required autocomplete="new-password" data-password-toggle-input>
                <button type="button" class="av-field__input-action" data-password-toggle aria-label="Show password">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <span class="av-field__hint">At least 8 characters, with upper &amp; lowercase letters and a number.</span>
            @error('password') <span class="av-field__error">{{ $message }}</span> @enderror
        </div>

        <div class="av-field">
            <label for="password_confirmation">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
            @error('password_confirmation') <span class="av-field__error">{{ $message }}</span> @enderror
        </div>

        <x-button type="submit" variant="primary" block>Create account</x-button>
    </form>

    <p class="av-auth-card__footer">
        Already have an account? <a href="{{ route('login') }}" style="color: var(--brass-700); font-weight: 600;">Sign in</a>
    </p>

    <p style="text-align: center; font-size: var(--text-xs); color: var(--stone-600); margin-top: var(--space-4);">
        By creating an account you agree to Artevo's <a href="{{ route('terms') }}" style="text-decoration: underline;">Terms</a>
        and <a href="{{ route('privacy') }}" style="text-decoration: underline;">Privacy Policy</a>.
    </p>

@endsection
