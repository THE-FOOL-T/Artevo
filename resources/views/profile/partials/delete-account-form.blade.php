<h3 style="color: var(--red-600);">Delete account</h3>
<p>Once your account is deleted, all of its data will be permanently removed. Please download any
    information you wish to keep before proceeding.</p>

<x-button type="button" variant="outline-dark" data-modal-open="delete-account" style="border-color: var(--red-600); color: var(--red-600);">
    Delete account
</x-button>

<x-modal id="delete-account" title="Are you sure you want to delete your account?" :open="$errors->userDeletion->any()">
    <p>This action cannot be undone. Enter your password to confirm.</p>

    <form method="POST" action="{{ route('profile.destroy') }}">
        @csrf
        @method('DELETE')

        <div class="av-field">
            <label for="delete_password">Password</label>
            <input type="password" id="delete_password" name="password" required>
            @error('password', 'userDeletion') <span class="av-field__error">{{ $message }}</span> @enderror
        </div>

        <div class="av-modal__actions">
            <x-button type="button" variant="outline-dark" data-modal-close>Cancel</x-button>
            <x-button type="submit" variant="dark" style="background: var(--red-600);">Delete account</x-button>
        </div>
    </form>
</x-modal>
