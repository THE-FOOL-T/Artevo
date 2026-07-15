@extends('layouts.guest')

@section('title', 'Set a New Password — Artevo')

@section('content')

    <h1 style="font-size: var(--text-2xl); text-align: center;">Set a new password</h1>

    <form method="POST" action="{{ route('password.store') }}" novalidate>
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="av-field">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            @error('email') <span class="av-field__error">{{ $message }}</span> @enderror
        </div>

        <div class="av-field">
            <label for="password">New password</label>
            <div class="av-field__input-group">
                <input type="password" id="password" name="password" required autocomplete="new-password" data-password-toggle-input>
                <button type="button" class="av-field__input-action" data-password-toggle aria-label="Show password">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            @error('password') <span class="av-field__error">{{ $message }}</span> @enderror
        </div>

        <div class="av-field">
            <label for="password_confirmation">Confirm new password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
            @error('password_confirmation') <span class="av-field__error">{{ $message }}</span> @enderror
        </div>

        <x-button type="submit" variant="primary" block>Reset password</x-button>
    </form>

@endsection
