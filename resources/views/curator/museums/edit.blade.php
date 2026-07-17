@extends('layouts.app')

@section('title', "Edit {$museum->name} — Artevo")

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container max-w-content">
            <x-tag>Editing</x-tag>
            <h1 style="margin-top: var(--space-4); margin-bottom: var(--space-2);">{{ $museum->name }}</h1>
            <x-museum-verification-badge :museum="$museum" />
            <p class="mt-4">
                <a href="{{ route('museums.show', $museum) }}" style="color: var(--brass-700); font-weight: 600;">View public profile &rarr;</a>
                &middot;
                <a href="{{ route('curator.museums.dashboard', $museum) }}" style="color: var(--brass-700); font-weight: 600;">View dashboard &rarr;</a>
                &middot;
                <a href="{{ route('curator.museums.artifacts.index', $museum) }}" style="color: var(--brass-700); font-weight: 600;">Manage artifacts &rarr;</a>
            </p>

            @if (auth()->user()->isAdmin())
                <x-card class="mt-6" style="border-color: var(--brass-600);">
                    <span class="av-card__eyebrow">Admin</span>
                    <h3>Verification status</h3>
                    <p>Verifying confirms this is a legitimate institution — it doesn't verify any individual artifact.</p>
                    <form method="POST" action="{{ route('admin.museums.verification.update', $museum) }}" class="flex gap-3">
                        @csrf
                        @method('PATCH')
                        <select name="verification_status" style="font-size: var(--text-sm); padding: 0.6rem 0.8rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                            @foreach (['pending' => 'Pending', 'verified' => 'Verified', 'rejected' => 'Rejected'] as $value => $label)
                                <option value="{{ $value }}" @selected($museum->verification_status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-button type="submit" variant="dark">Update status</x-button>
                    </form>
                </x-card>
            @endif

            <x-card class="mt-6">
                <h3>Profile</h3>
                <form method="POST" action="{{ route('curator.museums.update', $museum) }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')
                    @include('curator.museums.partials.form')
                    <x-button type="submit" variant="primary" class="mt-4">Save changes</x-button>
                </form>
            </x-card>

            <x-card class="mt-6">
                <h3>Gallery</h3>
                <p>Up to 10 images at a time.</p>

                @if ($museum->images->isNotEmpty())
                    <div class="grid grid-4" style="gap: var(--space-3); margin-bottom: var(--space-5);">
                        @foreach ($museum->images as $image)
                            <div style="position: relative;">
                                <img src="{{ $image->url() }}" alt="{{ $image->caption }}" style="width: 100%; height: 110px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--color-border);">
                                <form method="POST" action="{{ route('curator.museums.images.destroy', [$museum, $image]) }}" style="position: absolute; top: 4px; right: 4px;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" aria-label="Remove image" style="background: rgba(21,18,13,0.75); border: none; color: #fff; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; line-height: 1;">&times;</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('curator.museums.images.store', $museum) }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="av-field">
                        <label for="images">Add images</label>
                        <input type="file" id="images" name="images[]" accept="image/png, image/jpeg, image/webp" multiple>
                        @error('images') <span class="av-field__error">{{ $message }}</span> @enderror
                        @error('images.*') <span class="av-field__error">{{ $message }}</span> @enderror
                    </div>
                    <div class="av-field">
                        <label for="caption">Caption <span class="text-muted">(applied to all uploaded images)</span></label>
                        <input type="text" id="caption" name="caption" maxlength="160">
                    </div>
                    <x-button type="submit" variant="outline-dark">Upload</x-button>
                </form>
            </x-card>

            <x-card class="mt-6">
                <h3>Contacts</h3>

                @if ($museum->contacts->isNotEmpty())
                    <div style="margin-bottom: var(--space-5); display: flex; flex-direction: column; gap: var(--space-3);">
                        @foreach ($museum->contacts as $contact)
                            <div class="flex-between" style="padding: var(--space-3); border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                                <div>
                                    <strong>{{ $contact->label }}</strong>
                                    <div style="font-size: var(--text-sm); color: var(--ink-700);">
                                        {{ $contact->email }} @if($contact->email && $contact->phone) &middot; @endif {{ $contact->phone }}
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('curator.museums.contacts.destroy', [$museum, $contact]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="outline-dark" style="padding: 0.4rem 0.8rem; font-size: var(--text-xs);">Remove</x-button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('curator.museums.contacts.store', $museum) }}" novalidate>
                    @csrf
                    <div class="grid grid-3" style="gap: var(--space-4);">
                        <div class="av-field">
                            <label for="label">Label</label>
                            <input type="text" id="label" name="label" placeholder="e.g. Group Bookings" required>
                            @error('label') <span class="av-field__error">{{ $message }}</span> @enderror
                        </div>
                        <div class="av-field">
                            <label for="contact_email">Email</label>
                            <input type="email" id="contact_email" name="email">
                            @error('email') <span class="av-field__error">{{ $message }}</span> @enderror
                        </div>
                        <div class="av-field">
                            <label for="contact_phone">Phone</label>
                            <input type="text" id="contact_phone" name="phone">
                        </div>
                    </div>
                    <x-button type="submit" variant="outline-dark">Add contact</x-button>
                </form>
            </x-card>

            <x-card class="mt-6">
                <h3 style="color: var(--red-600);">Delete this museum</h3>
                <p>This removes the profile, its gallery, and its contacts. This cannot be undone.</p>
                <x-button type="button" variant="outline-dark" data-modal-open="delete-museum" style="border-color: var(--red-600); color: var(--red-600);">Delete museum</x-button>

                <x-modal id="delete-museum" title="Delete this museum profile?">
                    <p>This permanently removes "{{ $museum->name }}" and everything attached to it.</p>
                    <form method="POST" action="{{ route('curator.museums.destroy', $museum) }}">
                        @csrf
                        @method('DELETE')
                        <div class="av-modal__actions">
                            <x-button type="button" variant="outline-dark" data-modal-close>Cancel</x-button>
                            <x-button type="submit" variant="dark" style="background: var(--red-600);">Delete museum</x-button>
                        </div>
                    </form>
                </x-modal>
            </x-card>
        </div>
    </section>
@endsection
