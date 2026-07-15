<h3>Update password</h3>
<p>Use a long, random password to keep your account secure.</p>

<form method="POST" action="{{ route('password.update') }}" novalidate>
    @csrf
    @method('PUT')

    <div class="av-field">
        <label for="current_password">Current password</label>
        <input type="password" id="current_password" name="current_password" autocomplete="current-password">
        @error('current_password', 'updatePassword') <span class="av-field__error">{{ $message }}</span> @enderror
    </div>

    <div class="av-field">
        <label for="update_password_password">New password</label>
        <input type="password" id="update_password_password" name="password" autocomplete="new-password">
        @error('password', 'updatePassword') <span class="av-field__error">{{ $message }}</span> @enderror
    </div>

    <div class="av-field">
        <label for="update_password_password_confirmation">Confirm new password</label>
        <input type="password" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password">
    </div>

    <x-button type="submit" variant="primary">Update password</x-button>
</form>
