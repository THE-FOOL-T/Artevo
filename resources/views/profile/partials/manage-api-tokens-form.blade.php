<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">API Tokens</h2>
        <p class="mt-1 text-sm text-gray-600">
            Personal access tokens allow third-party services to authenticate with our API on your behalf.
        </p>
    </header>

    @if (session('apiToken'))
        <div class="mt-6" style="padding: var(--space-4); background-color: var(--color-green-100); border: 1px solid var(--color-green-300); border-radius: var(--radius-md);">
            <p style="font-weight: 500; color: var(--color-green-800); margin-bottom: var(--space-2);">Your new token</p>
            <p style="font-size: var(--text-sm); margin-bottom: var(--space-2);">Please copy this token now. For your security, it won't be shown again.</p>
            <code style="word-break: break-all; font-family: monospace; font-size: var(--text-sm); background: rgba(255,255,255,0.7); padding: var(--space-2); border-radius: var(--radius-sm); display: block;">{{ session('apiToken') }}</code>
        </div>
    @endif

    <form method="post" action="{{ route('profile.api-tokens.store') }}" class="mt-6" style="display: flex; flex-direction: column; gap: var(--space-4);">
        @csrf

        <div style="display: flex; flex-direction: column; gap: var(--space-2);">
            <label for="token_name" style="font-weight: 500; font-size: var(--text-sm);">Token Name</label>
            <input type="text" id="token_name" name="token_name" required autofocus autocomplete="off" style="padding: var(--space-2); border: 1px solid var(--border-color); border-radius: var(--radius-sm);" placeholder="e.g. Mobile App">
            @error('token_name')
                <span style="color: var(--color-red-500); font-size: var(--text-sm);">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <x-button type="submit">Create Token</x-button>
        </div>
    </form>

    @if (auth()->user()->tokens->isNotEmpty())
        <div class="mt-10">
            <h3 class="text-lg font-medium text-gray-900" style="margin-bottom: var(--space-4);">Active Tokens</h3>
            <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                @foreach (auth()->user()->tokens as $token)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-4); border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                        <div>
                            <div style="font-weight: 500;">{{ $token->name }}</div>
                            <div style="font-size: var(--text-sm); color: var(--text-muted); margin-top: var(--space-1);">
                                Last used: {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Never' }}
                            </div>
                        </div>
                        <form method="post" action="{{ route('profile.api-tokens.destroy', $token->id) }}">
                            @csrf
                            @method('delete')
                            <button type="submit" style="color: var(--color-red-500); font-size: var(--text-sm); font-weight: 500; background: none; border: none; cursor: pointer;">Revoke</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
