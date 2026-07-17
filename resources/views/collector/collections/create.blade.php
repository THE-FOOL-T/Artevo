@extends('layouts.app')

@section('title', 'New Collection — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container" style="max-width: 720px;">
            <x-tag>My collections</x-tag>
            <h1 style="margin-top: var(--space-4);">New collection</h1>
            <p><a href="{{ route('collector.collections.index') }}" style="color: var(--brass-700); font-weight: 600;">&larr; Back to collections</a></p>

            <x-alert :message="session('error')" type="error" />

            <form action="{{ route('collector.collections.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <x-card class="mt-6">
                    <span class="av-card__eyebrow">Collection details</span>

                    <div class="av-field mt-4">
                        <label for="name">Collection name <span style="color: var(--terracotta-600);">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               placeholder="e.g. Roman Bronze — Personal Acquisitions" required maxlength="180">
                        @error('name')<p class="av-field__error">{{ $message }}</p>@enderror
                    </div>

                    <div class="av-field mt-4">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  placeholder="Describe the theme or story behind this collection…">{{ old('description') }}</textarea>
                        @error('description')<p class="av-field__error">{{ $message }}</p>@enderror
                    </div>

                    <div class="av-field mt-4">
                        <label for="cover_image">Cover image</label>
                        <input type="file" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/webp">
                        <p style="margin: var(--space-1) 0 0; font-size: var(--text-xs); color: var(--stone-600);">JPG, PNG, or WebP. Max 5 MB.</p>
                        @error('cover_image')<p class="av-field__error">{{ $message }}</p>@enderror
                    </div>
                </x-card>

                <x-card class="mt-6">
                    <span class="av-card__eyebrow">Visibility</span>
                    <div style="margin-top: var(--space-4);">
                        <label class="flex gap-3" style="align-items: center; cursor: pointer;">
                            <input type="hidden" name="is_public" value="0">
                            <input type="checkbox" id="is_public" name="is_public" value="1"
                                   {{ old('is_public', '1') ? 'checked' : '' }}
                                   style="width: 18px; height: 18px; accent-color: var(--brass-600);">
                            <span>
                                <strong>Make this collection public</strong><br>
                                <span style="font-size: var(--text-sm); color: var(--stone-600);">Public collections appear on <code>/collections</code> and can be favorited by other users.</span>
                            </span>
                        </label>
                    </div>
                </x-card>

                <div class="flex gap-4 mt-6">
                    <x-button type="submit" variant="primary">Create collection</x-button>
                    <x-button href="{{ route('collector.collections.index') }}" variant="outline-dark">Cancel</x-button>
                </div>
            </form>
        </div>
    </section>
@endsection
