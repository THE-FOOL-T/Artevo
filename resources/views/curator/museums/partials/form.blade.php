@php
    $museum = $museum ?? null;
    $hours = old('opening_hours', $museum->opening_hours ?? []);
    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
@endphp

<div class="av-field">
    <label for="name">Museum name</label>
    <input type="text" id="name" name="name" value="{{ old('name', $museum->name ?? '') }}" required>
    @error('name') <span class="av-field__error">{{ $message }}</span> @enderror
</div>

<div class="av-field">
    <label for="tagline">Tagline <span class="text-muted">(shown on museum cards)</span></label>
    <input type="text" id="tagline" name="tagline" value="{{ old('tagline', $museum->tagline ?? '') }}" maxlength="160">
    @error('tagline') <span class="av-field__error">{{ $message }}</span> @enderror
</div>

<div class="av-field">
    <label for="description">Description</label>
    <textarea id="description" name="description" style="min-height: 180px;">{{ old('description', $museum->description ?? '') }}</textarea>
    @error('description') <span class="av-field__error">{{ $message }}</span> @enderror
</div>

<div class="grid grid-2" style="gap: var(--space-4);">
    <div class="av-field">
        <label for="logo">Logo</label>
        @if ($museum?->logoUrl())
            <img src="{{ $museum->logoUrl() }}" alt="Current logo" style="width: 64px; height: 64px; object-fit: cover; border-radius: var(--radius-sm); margin-bottom: var(--space-2);">
        @endif
        <input type="file" id="logo" name="logo" accept="image/png, image/jpeg, image/webp">
        <span class="av-field__hint">Square image recommended. JPG, PNG or WEBP, up to 2MB.</span>
        @error('logo') <span class="av-field__error">{{ $message }}</span> @enderror
    </div>
    <div class="av-field">
        <label for="cover_image">Cover image</label>
        @if ($museum?->coverImageUrl())
            <img src="{{ $museum->coverImageUrl() }}" alt="Current cover" style="width: 100%; height: 64px; object-fit: cover; border-radius: var(--radius-sm); margin-bottom: var(--space-2);">
        @endif
        <input type="file" id="cover_image" name="cover_image" accept="image/png, image/jpeg, image/webp">
        <span class="av-field__hint">Wide banner image. JPG, PNG or WEBP, up to 4MB.</span>
        @error('cover_image') <span class="av-field__error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-2" style="gap: var(--space-4);">
    <div class="av-field">
        <label for="foundation_year">Foundation year</label>
        <input type="number" id="foundation_year" name="foundation_year" value="{{ old('foundation_year', $museum->foundation_year ?? '') }}" min="1000" max="{{ now()->year }}">
        @error('foundation_year') <span class="av-field__error">{{ $message }}</span> @enderror
    </div>
    <div class="av-field">
        <label for="website">Website</label>
        <input type="url" id="website" name="website" value="{{ old('website', $museum->website ?? '') }}" placeholder="https://">
        @error('website') <span class="av-field__error">{{ $message }}</span> @enderror
    </div>
</div>

<h3 style="font-size: var(--text-lg); margin-top: var(--space-6);">Social links</h3>
<div class="grid grid-3" style="gap: var(--space-4);">
    @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'twitter' => 'X / Twitter'] as $key => $label)
        <div class="av-field">
            <label for="social_{{ $key }}">{{ $label }}</label>
            <input type="url" id="social_{{ $key }}" name="social_links[{{ $key }}]" value="{{ old('social_links.' . $key, $museum->social_links[$key] ?? '') }}" placeholder="https://">
            @error('social_links.' . $key) <span class="av-field__error">{{ $message }}</span> @enderror
        </div>
    @endforeach
</div>

<h3 style="font-size: var(--text-lg); margin-top: var(--space-6);">Opening hours</h3>
<div class="grid grid-3" style="gap: var(--space-4);">
    @foreach ($days as $day)
        <div class="av-field">
            <label for="hours_{{ $day }}">{{ ucfirst($day) }}</label>
            <input type="text" id="hours_{{ $day }}" name="opening_hours[{{ $day }}]" value="{{ $hours[$day] ?? '' }}" placeholder="e.g. 10:00–18:00 or Closed">
        </div>
    @endforeach
</div>

<h3 style="font-size: var(--text-lg); margin-top: var(--space-6);">Location</h3>
<div class="av-field">
    <label for="address">Street address</label>
    <input type="text" id="address" name="address" value="{{ old('address', $museum->address ?? '') }}">
</div>
<div class="grid grid-2" style="gap: var(--space-4);">
    <div class="av-field">
        <label for="city">City</label>
        <input type="text" id="city" name="city" value="{{ old('city', $museum->city ?? '') }}">
    </div>
    <div class="av-field">
        <label for="country">Country</label>
        <input type="text" id="country" name="country" value="{{ old('country', $museum->country ?? '') }}">
    </div>
</div>
<div class="grid grid-2" style="gap: var(--space-4);">
    <div class="av-field">
        <label for="latitude">Latitude <span class="text-muted">(optional — powers the map in a later phase)</span></label>
        <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $museum->latitude ?? '') }}">
        @error('latitude') <span class="av-field__error">{{ $message }}</span> @enderror
    </div>
    <div class="av-field">
        <label for="longitude">Longitude</label>
        <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $museum->longitude ?? '') }}">
        @error('longitude') <span class="av-field__error">{{ $message }}</span> @enderror
    </div>
</div>

@if (auth()->user()->isAdmin())
    <div class="av-field" style="display: flex; align-items: center; gap: var(--space-2);">
        <input type="checkbox" id="featured" name="featured" value="1" style="width: auto;" @checked(old('featured', $museum->featured ?? false))>
        <label for="featured" style="margin-bottom: 0; font-weight: 400;">Feature this museum on the public directory</label>
    </div>
@endif
