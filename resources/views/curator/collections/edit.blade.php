@php use Illuminate\Support\Str; @endphp

@extends('layouts.app')

@section('title', "Edit "{$collection->name}" — Artevo")

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            <x-tag>{{ $museum->name }}</x-tag>
            <div class="flex-between" style="margin-top: var(--space-4);">
                <h1 style="margin-bottom: 0;">Edit collection</h1>
                <div class="flex gap-3">
                    @if($collection->is_public)
                        <a href="{{ route('collections.show', $collection) }}" class="av-btn" style="background: transparent; border: 1px solid var(--color-border);" target="_blank">
                            View public ↗
                        </a>
                    @endif
                    <form action="{{ route('curator.collections.destroy', [$museum, $collection]) }}" method="POST"
                          onsubmit="return confirm('Delete this collection? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="av-btn" style="background: var(--red-100); color: var(--red-600); border: none;">Delete</button>
                    </form>
                </div>
            </div>
            <p><a href="{{ route('curator.collections.index', $museum) }}" style="color: var(--brass-700); font-weight: 600;">&larr; All collections</a></p>

            <x-alert :message="session('success')" type="success" />
            <x-alert :message="session('error')" type="error" />

            {{-- Two-column layout: details left, artifact picker right --}}
            <div class="grid grid-2" style="gap: var(--space-8); align-items: start; margin-top: var(--space-6);">

                {{-- LEFT: collection details form --}}
                <div>
                    <form action="{{ route('curator.collections.update', [$museum, $collection]) }}" method="POST"
                          enctype="multipart/form-data" id="collection-details-form">
                        @csrf @method('PUT')

                        <x-card>
                            <span class="av-card__eyebrow">Collection details</span>

                            <div class="av-field mt-4">
                                <label for="name">Collection name <span style="color: var(--terracotta-600);">*</span></label>
                                <input type="text" id="name" name="name"
                                       value="{{ old('name', $collection->name) }}" required maxlength="180">
                                @error('name')<p class="av-field__error">{{ $message }}</p>@enderror
                            </div>

                            <div class="av-field mt-4">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="4">{{ old('description', $collection->description) }}</textarea>
                                @error('description')<p class="av-field__error">{{ $message }}</p>@enderror
                            </div>

                            <div class="av-field mt-4">
                                <label>Cover image</label>
                                @if($collection->coverImageUrl())
                                    <div style="margin-bottom: var(--space-3); position: relative; display: inline-block;">
                                        <img src="{{ $collection->coverImageUrl() }}" alt="Current cover"
                                             style="height: 120px; border-radius: var(--radius-sm); object-fit: cover;">
                                        <label class="flex gap-2 mt-2" style="align-items: center; cursor: pointer; font-size: var(--text-sm); color: var(--red-600);">
                                            <input type="checkbox" name="remove_cover_image" value="1">
                                            Remove current cover image
                                        </label>
                                    </div>
                                @endif
                                <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp">
                                <p style="margin: var(--space-1) 0 0; font-size: var(--text-xs); color: var(--stone-600);">Upload a new image to replace the current one. Max 5 MB.</p>
                                @error('cover_image')<p class="av-field__error">{{ $message }}</p>@enderror
                            </div>
                        </x-card>

                        <x-card class="mt-6">
                            <span class="av-card__eyebrow">Visibility</span>
                            <div style="margin-top: var(--space-4);">
                                <label class="flex gap-3" style="align-items: center; cursor: pointer;">
                                    <input type="hidden" name="is_public" value="0">
                                    <input type="checkbox" name="is_public" value="1"
                                           {{ old('is_public', $collection->is_public) ? 'checked' : '' }}
                                           style="width: 18px; height: 18px; accent-color: var(--brass-600);">
                                    <span>
                                        <strong>Make this collection public</strong><br>
                                        <span style="font-size: var(--text-sm); color: var(--stone-600);">When unchecked, only you and administrators can see it.</span>
                                    </span>
                                </label>
                            </div>
                        </x-card>

                        <div class="flex gap-4 mt-6">
                            <x-button type="submit" variant="primary">Save changes</x-button>
                        </div>
                    </form>
                </div>

                {{-- RIGHT: artifact picker + drag-to-reorder --}}
                <div>
                    <x-card>
                        <span class="av-card__eyebrow">Artifacts in this collection ({{ $collectionArtifacts->count() }})</span>
                        <p style="font-size: var(--text-sm); color: var(--stone-600); margin: var(--space-2) 0 var(--space-4);">
                            Drag to reorder. Click × to remove.
                        </p>

                        <div id="sortable-artifacts" style="min-height: 60px;">
                            @forelse($collectionArtifacts as $artifact)
                                <div class="av-sortable-item" data-artifact-id="{{ $artifact->id }}"
                                     style="display: flex; align-items: center; gap: var(--space-3); padding: var(--space-3); margin-bottom: var(--space-2); background: var(--porcelain-50); border: 1px solid var(--color-border); border-radius: var(--radius-sm); cursor: grab;">
                                    <span style="color: var(--stone-400); cursor: grab;">⠿</span>
                                    <img src="{{ $artifact->primaryImageUrl() }}"
                                         alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: var(--radius-sm); flex-shrink: 0;">
                                    <span style="flex: 1; font-size: var(--text-sm); font-weight: 600;">{{ $artifact->name }}</span>
                                    <button type="button"
                                            data-remove-artifact="{{ $artifact->id }}"
                                            data-remove-url="{{ route('collections.artifacts.destroy', [$collection, $artifact]) }}"
                                            style="background: none; border: none; cursor: pointer; color: var(--red-600); font-size: 1.1rem; padding: 4px 8px;"
                                            title="Remove from collection">×</button>
                                </div>
                            @empty
                                <p id="empty-msg" style="font-size: var(--text-sm); color: var(--stone-600); margin: 0;">No artifacts yet. Add them below.</p>
                            @endforelse
                        </div>
                    </x-card>

                    {{-- Add artifact picker --}}
                    <x-card class="mt-6">
                        <span class="av-card__eyebrow">Add artifact from this museum</span>
                        <div class="av-field mt-4">
                            <label for="artifact-search-picker">Search artifacts</label>
                            <input type="search" id="artifact-search-picker" placeholder="Type to filter…"
                                   style="margin-bottom: var(--space-3);">
                        </div>
                        <div id="artifact-picker-list" style="max-height: 320px; overflow-y: auto;">
                            @foreach($artifacts as $artifact)
                                @php $alreadyIn = $collectionArtifacts->contains('id', $artifact->id); @endphp
                                <div class="av-picker-item {{ $alreadyIn ? 'av-picker-item--added' : '' }}"
                                     data-name="{{ Str::lower($artifact->name) }}"
                                     style="display: flex; align-items: center; gap: var(--space-3); padding: var(--space-2) var(--space-3); border-radius: var(--radius-sm); margin-bottom: var(--space-1); background: {{ $alreadyIn ? 'var(--green-100)' : 'var(--porcelain-50)' }};">
                                    <img src="{{ $artifact->primaryImageUrl() }}"
                                         alt="" style="width: 36px; height: 36px; object-fit: cover; border-radius: var(--radius-sm); flex-shrink: 0;">
                                    <span style="flex: 1; font-size: var(--text-sm);">{{ $artifact->name }}</span>
                                    @if(!$alreadyIn)
                                        <button type="button"
                                                data-add-artifact="{{ $artifact->id }}"
                                                data-add-url="{{ route('collections.artifacts.store', $collection) }}"
                                                class="av-btn av-btn--sm av-btn--primary">+ Add</button>
                                    @else
                                        <span style="font-size: var(--text-xs); color: var(--green-600); font-weight: 600;">In collection</span>
                                    @endif
                                </div>
                            @endforeach

                            @if($artifacts->isEmpty())
                                <p style="font-size: var(--text-sm); color: var(--stone-600);">This museum has no public artifacts yet.</p>
                            @endif
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
{{-- Sortable.js from CDN for drag-and-drop reordering --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const sortableEl = document.getElementById('sortable-artifacts');
    const reorderUrl = @json(route('collections.artifacts.reorder', $collection));

    // Drag-and-drop reorder
    if (sortableEl) {
        Sortable.create(sortableEl, {
            animation: 180,
            ghostClass: 'av-sortable-ghost',
            handle: '[data-artifact-id]',
            onEnd: async function () {
                const ids = [...sortableEl.querySelectorAll('[data-artifact-id]')]
                    .map(el => parseInt(el.dataset.artifactId));

                await fetch(reorderUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ artifact_ids: ids }),
                });
            },
        });
    }

    // Remove artifact from collection
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('[data-remove-artifact]');
        if (!btn) return;

        if (!confirm('Remove this artifact from the collection?')) return;

        const row = btn.closest('[data-artifact-id]');
        const res = await fetch(btn.dataset.removeUrl, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });

        if (res.ok) {
            row.remove();
            // Update counter in eyebrow
            const badge = document.querySelector('[data-artifacts-count]');
            if (badge) badge.textContent = parseInt(badge.textContent) - 1;
        }
    });

    // Add artifact from picker
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('[data-add-artifact]');
        if (!btn) return;

        btn.disabled = true;

        const res = await fetch(btn.dataset.addUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ artifact_id: parseInt(btn.dataset.addArtifact) }),
        });

        if (res.ok) {
            // Reload page to refresh both lists accurately
            window.location.reload();
        } else {
            btn.disabled = false;
        }
    });

    // Live search filter for the picker
    const searchInput = document.getElementById('artifact-search-picker');
    const pickerItems = document.querySelectorAll('.av-picker-item');

    searchInput?.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        pickerItems.forEach(item => {
            item.style.display = !q || item.dataset.name.includes(q) ? '' : 'none';
        });
    });
})();
</script>
<style>
.av-sortable-ghost { opacity: 0.4; background: var(--brass-100); }
</style>
@endpush
