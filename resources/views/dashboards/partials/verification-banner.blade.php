@if (request('verified') === '1')
    <x-alert type="success">Your email has been verified — welcome to Artevo.</x-alert>
@endif

@unless ($user->hasVerifiedEmail())
    <x-alert type="error">
        Your email address isn't verified yet.
        <form method="POST" action="{{ route('verification.send') }}" style="display: inline;">
            @csrf
            <button type="submit" style="text-decoration: underline; background: none; border: none; padding: 0; font: inherit; color: inherit; cursor: pointer;">Resend the verification email.</button>
        </form>
    </x-alert>
@endunless
