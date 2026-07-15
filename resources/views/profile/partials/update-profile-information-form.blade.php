<h3>Profile information</h3>
<p>Update your name, email address, and avatar.</p>

<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PATCH')

    <div class="flex gap-4" style="align-items: center; margin-bottom: var(--space-6);">
        <x-avatar :user="$user" size="lg" />
        <div>
            <label for="avatar" style="display: inline-block; font-size: var(--text-sm); font-weight: 600; color: var(--brass-700); cursor: pointer;">
                Change avatar
                <input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg, image/webp" style="display: none;" onchange="this.form.querySelector('button[type=submit]').focus()">
            </label>
            <p style="font-size: var(--text-xs); color: var(--stone-600); margin: 0;">JPG, PNG or WEBP, up to 2MB.</p>
            @error('avatar') <span class="av-field__error">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="av-field">
        <label for="name">Full name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        @error('name') <span class="av-field__error">{{ $message }}</span> @enderror
    </div>

    <div class="av-field">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        @error('email') <span class="av-field__error">{{ $message }}</span> @enderror

        @unless ($user->hasVerifiedEmail())
            <span class="av-field__hint">Your email is unverified.</span>
        @endunless
    </div>

    <x-button type="submit" variant="primary">Save changes</x-button>
</form>
