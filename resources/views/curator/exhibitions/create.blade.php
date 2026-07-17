@extends('layouts.app')

@section('title', 'New Exhibition — ' . $museum->name . ' — Artevo')

@section('content')
<div class="av-section">
    <div class="container" style="max-width: 680px;">
        <nav aria-label="Breadcrumb" style="margin-bottom: var(--space-4); font-size:.875rem;">
            <a href="{{ route('curator.museums.index') }}" style="color:var(--color-muted);">My Museums</a>
            <span style="color:var(--color-muted); margin: 0 var(--space-2);">/</span>
            <a href="{{ route('curator.exhibitions.index', $museum) }}" style="color:var(--color-muted);">Exhibitions</a>
            <span style="color:var(--color-muted); margin: 0 var(--space-2);">/</span>
            <span>New Exhibition</span>
        </nav>

        <h1 class="av-page-title" style="margin-bottom: var(--space-6);">Create Exhibition</h1>

        <div style="background:rgba(99,102,241,.08); border:1px solid rgba(99,102,241,.2); border-radius:var(--radius-sm); padding:var(--space-3) var(--space-4); color:#6366f1; margin-bottom:var(--space-6); font-size:.9rem;">
            You can add sections and artifacts after creating the exhibition.
        </div>

        <form method="POST" action="{{ route('curator.exhibitions.store', $museum) }}" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="av-card" style="padding: var(--space-6); display: flex; flex-direction: column; gap: var(--space-5);">

                {{-- Name --}}
                <div class="av-form-group">
                    <label class="av-label" for="name">Exhibition Title <span style="color:var(--color-danger)">*</span></label>
                    <input id="name" name="name" type="text" class="av-input @error('name') is-error @enderror"
                        value="{{ old('name') }}" placeholder="e.g. The Bronze Age Revisited" required maxlength="180">
                    @error('name')<p class="av-error">{{ $message }}</p>@enderror
                </div>

                {{-- Tagline --}}
                <div class="av-form-group">
                    <label class="av-label" for="tagline">Tagline</label>
                    <input id="tagline" name="tagline" type="text" class="av-input @error('tagline') is-error @enderror"
                        value="{{ old('tagline') }}" placeholder="A brief captivating subtitle" maxlength="255">
                    @error('tagline')<p class="av-error">{{ $message }}</p>@enderror
                </div>

                {{-- Description --}}
                <div class="av-form-group">
                    <label class="av-label" for="description">Description</label>
                    <textarea id="description" name="description" class="av-input @error('description') is-error @enderror"
                        rows="5" placeholder="What is this exhibition about?">{{ old('description') }}</textarea>
                    @error('description')<p class="av-error">{{ $message }}</p>@enderror
                </div>

                {{-- Dates --}}
                <div class="grid grid-2" style="gap: var(--space-4);">
                    <div class="av-form-group">
                        <label class="av-label" for="starts_at">Start Date</label>
                        <input id="starts_at" name="starts_at" type="date" class="av-input @error('starts_at') is-error @enderror"
                            value="{{ old('starts_at') }}">
                        @error('starts_at')<p class="av-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="av-form-group">
                        <label class="av-label" for="ends_at">End Date</label>
                        <input id="ends_at" name="ends_at" type="date" class="av-input @error('ends_at') is-error @enderror"
                            value="{{ old('ends_at') }}">
                        @error('ends_at')<p class="av-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Location + Admission --}}
                <div class="grid grid-2" style="gap: var(--space-4);">
                    <div class="av-form-group">
                        <label class="av-label" for="location">Location</label>
                        <input id="location" name="location" type="text" class="av-input @error('location') is-error @enderror"
                            value="{{ old('location') }}" placeholder="Gallery 3 / Virtual" maxlength="255">
                        @error('location')<p class="av-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="av-form-group">
                        <label class="av-label" for="admission_fee">Admission Fee (USD)</label>
                        <input id="admission_fee" name="admission_fee" type="number" step="0.01" min="0"
                            class="av-input @error('admission_fee') is-error @enderror"
                            value="{{ old('admission_fee') }}" placeholder="Leave blank for free">
                        @error('admission_fee')<p class="av-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Status --}}
                <div class="av-form-group">
                    <label class="av-label" for="status">Status</label>
                    <select id="status" name="status" class="av-input @error('status') is-error @enderror">
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                    @error('status')<p class="av-error">{{ $message }}</p>@enderror
                </div>

                {{-- Cover image --}}
                <div class="av-form-group">
                    <label class="av-label" for="cover_image">Cover Image</label>
                    <input id="cover_image" name="cover_image" type="file" class="av-input @error('cover_image') is-error @enderror"
                        accept="image/*">
                    <p style="font-size:.8rem; color:var(--color-muted); margin-top:var(--space-1);">JPEG, PNG or WebP — max 5 MB.</p>
                    @error('cover_image')<p class="av-error">{{ $message }}</p>@enderror
                </div>

            </div>

            <div class="flex" style="justify-content: flex-end; gap: var(--space-3); margin-top: var(--space-6);">
                <a href="{{ route('curator.exhibitions.index', $museum) }}" class="av-btn av-btn--ghost">Cancel</a>
                <button type="submit" class="av-btn av-btn--primary">Create &amp; Add Sections →</button>
            </div>
        </form>
    </div>
</div>
@endsection
