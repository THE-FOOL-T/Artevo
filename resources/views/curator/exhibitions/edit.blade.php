@php use Illuminate\Support\Str; @endphp

@extends('layouts.app')

@section('title', 'Edit "' . $exhibition->name . '" — Artevo')

@section('content')
<div class="av-section" style="padding-bottom: var(--space-16);">
<div class="container">

{{-- Breadcrumb + header --}}
<nav aria-label="Breadcrumb" style="margin-bottom: var(--space-4); font-size:.875rem;">
    <a href="{{ route('curator.museums.index') }}" style="color:var(--color-muted);">My Museums</a>
    <span style="color:var(--color-muted); margin: 0 var(--space-2);">/</span>
    <a href="{{ route('curator.exhibitions.index', $museum) }}" style="color:var(--color-muted);">Exhibitions</a>
    <span style="color:var(--color-muted); margin: 0 var(--space-2);">/</span>
    <span>{{ Str::limit($exhibition->name, 40) }}</span>
</nav>

<div class="flex" style="align-items:center; justify-content:space-between; margin-bottom:var(--space-6); flex-wrap:wrap; gap:var(--space-3);">
    <div>
        <h1 class="av-page-title" style="margin:0;">{{ $exhibition->name }}</h1>
        <span class="av-tag av-tag--{{ $exhibition->isDraft() ? 'warning' : ($exhibition->isPublished() ? 'success' : 'secondary') }}" style="margin-top:var(--space-2);">
            {{ $exhibition->statusLabel() }}
        </span>
    </div>
    <div class="flex" style="gap:var(--space-2); flex-wrap:wrap;">
        @if($exhibition->isDraft())
            <form method="POST" action="{{ route('curator.exhibitions.publish', [$museum, $exhibition]) }}">
                @csrf @method('PATCH')
                <button class="av-btn av-btn--primary">Publish Exhibition</button>
            </form>
        @elseif($exhibition->isPublished())
            <a href="{{ route('exhibitions.show', $exhibition) }}" class="av-btn av-btn--outline" target="_blank">View Live →</a>
            <form method="POST" action="{{ route('curator.exhibitions.archive', [$museum, $exhibition]) }}">
                @csrf @method('PATCH')
                <button class="av-btn av-btn--ghost">Archive</button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
    <x-alert type="success" :message="session('success')" style="margin-bottom:var(--space-5);" />
@endif

{{-- ═══════════════════════════ TWO-COLUMN LAYOUT ═══════════════════════════ --}}
<div style="display:grid; grid-template-columns:380px 1fr; gap:var(--space-8); align-items:start;">

{{-- ── LEFT: Details form ──────────────────────────────────────── --}}
<div>
<div class="av-card" style="padding:var(--space-5);">
    <h2 style="font-size:1rem; margin:0 0 var(--space-4);">Exhibition Details</h2>

    <form id="exhibition-details-form"
        method="POST"
        action="{{ route('curator.exhibitions.update', [$museum, $exhibition]) }}"
        enctype="multipart/form-data" novalidate>
        @csrf @method('PUT')

        <div style="display:flex; flex-direction:column; gap:var(--space-4);">

            <div class="av-form-group">
                <label class="av-label" for="name">Title *</label>
                <input id="name" name="name" type="text" class="av-input @error('name') is-error @enderror"
                    value="{{ old('name', $exhibition->name) }}" required maxlength="180">
                @error('name')<p class="av-error">{{ $message }}</p>@enderror
            </div>

            <div class="av-form-group">
                <label class="av-label" for="tagline">Tagline</label>
                <input id="tagline" name="tagline" type="text" class="av-input"
                    value="{{ old('tagline', $exhibition->tagline) }}" maxlength="255">
            </div>

            <div class="av-form-group">
                <label class="av-label" for="description">Description</label>
                <textarea id="description" name="description" class="av-input" rows="4">{{ old('description', $exhibition->description) }}</textarea>
            </div>

            <div class="grid grid-2" style="gap:var(--space-3);">
                <div class="av-form-group">
                    <label class="av-label" for="starts_at">Start Date</label>
                    <input id="starts_at" name="starts_at" type="date" class="av-input"
                        value="{{ old('starts_at', $exhibition->starts_at?->format('Y-m-d')) }}">
                </div>
                <div class="av-form-group">
                    <label class="av-label" for="ends_at">End Date</label>
                    <input id="ends_at" name="ends_at" type="date" class="av-input"
                        value="{{ old('ends_at', $exhibition->ends_at?->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="av-form-group">
                <label class="av-label" for="location">Location</label>
                <input id="location" name="location" type="text" class="av-input"
                    value="{{ old('location', $exhibition->location) }}" placeholder="Gallery / Virtual" maxlength="255">
            </div>

            <div class="av-form-group">
                <label class="av-label" for="admission_fee">Admission Fee (USD)</label>
                <input id="admission_fee" name="admission_fee" type="number" step="0.01" min="0" class="av-input"
                    value="{{ old('admission_fee', $exhibition->admission_fee) }}" placeholder="Leave blank = free">
            </div>

            <div class="av-form-group">
                <label class="av-label" for="status">Status</label>
                <select id="status" name="status" class="av-input">
                    @foreach(\App\Models\Exhibition::STATUSES as $s)
                        <option value="{{ $s }}" {{ old('status', $exhibition->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Cover image --}}
            <div class="av-form-group">
                <label class="av-label">Cover Image</label>
                @if($exhibition->coverImageUrl())
                    <div style="position:relative; display:inline-block; margin-bottom:var(--space-3);">
                        <img src="{{ $exhibition->coverImageUrl() }}" alt="Cover" style="height:120px; border-radius:var(--radius-sm); object-fit:cover; display:block;">
                    </div>
                    <label style="display:flex; align-items:center; gap:var(--space-2); font-size:.85rem; cursor:pointer; margin-bottom:var(--space-2);">
                        <input type="checkbox" name="remove_cover_image" value="1"> Remove cover image
                    </label>
                @endif
                <input type="file" name="cover_image" class="av-input" accept="image/*">
                <p style="font-size:.78rem; color:var(--color-muted); margin-top:var(--space-1);">Max 5 MB. Replaces existing.</p>
            </div>

        </div>

        <button type="submit" class="av-btn av-btn--primary" style="width:100%; margin-top:var(--space-4);">Save Details</button>
    </form>
</div>
</div>

{{-- ── RIGHT: Sections builder ─────────────────────────────────── --}}
<div>
<div class="av-card" style="padding:var(--space-5);">
    <div class="flex" style="align-items:center; justify-content:space-between; margin-bottom:var(--space-4);">
        <h2 style="font-size:1rem; margin:0;">Sections</h2>
        <button type="button" class="av-btn av-btn--primary" id="add-section-btn" style="font-size:.85rem;">+ Add Section</button>
    </div>

    {{-- Add section form (hidden by default) --}}
    <div id="new-section-form" style="display:none; background:var(--color-surface-2); border-radius:var(--radius-sm); padding:var(--space-4); margin-bottom:var(--space-4); border:1px solid var(--color-border);">
        <div class="av-form-group" style="margin-bottom:var(--space-3);">
            <label class="av-label" for="new-section-title">Section Title *</label>
            <input id="new-section-title" type="text" class="av-input" placeholder="e.g. The Early Bronze Age" maxlength="180">
        </div>
        <div class="av-form-group" style="margin-bottom:var(--space-3);">
            <label class="av-label" for="new-section-body">Narrative Text</label>
            <textarea id="new-section-body" class="av-input" rows="4" placeholder="Describe this section of the exhibition…"></textarea>
        </div>
        <div class="flex" style="gap:var(--space-2); justify-content:flex-end;">
            <button type="button" class="av-btn av-btn--ghost" id="cancel-section-btn">Cancel</button>
            <button type="button" class="av-btn av-btn--primary" id="save-section-btn">Add Section</button>
        </div>
    </div>

    {{-- Sortable sections list --}}
    <div id="sections-list">
        @forelse($exhibition->sections as $section)
        <div class="av-section-item" data-section-id="{{ $section->id }}"
            style="border:1px solid var(--color-border); border-radius:var(--radius-sm); margin-bottom:var(--space-3); background:var(--color-surface);">

            {{-- Section header --}}
            <div class="flex" style="align-items:center; padding:var(--space-3); gap:var(--space-3); background:var(--color-surface-2); border-radius:var(--radius-sm) var(--radius-sm) 0 0; cursor:grab;" data-drag-handle>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-muted)" stroke-width="2" aria-hidden="true"><circle cx="9" cy="5" r="1" fill="currentColor"/><circle cx="9" cy="12" r="1" fill="currentColor"/><circle cx="9" cy="19" r="1" fill="currentColor"/><circle cx="15" cy="5" r="1" fill="currentColor"/><circle cx="15" cy="12" r="1" fill="currentColor"/><circle cx="15" cy="19" r="1" fill="currentColor"/></svg>
                <span class="section-title-display" style="flex:1; font-weight:600; font-size:.9rem;">{{ $section->title }}</span>
                <button type="button" class="av-btn av-btn--ghost" style="font-size:.78rem; padding:2px 10px;" data-edit-section="{{ $section->id }}">Edit</button>
                <button type="button" class="av-btn av-btn--ghost" style="font-size:.78rem; padding:2px 10px; color:var(--color-danger);" data-delete-section="{{ $section->id }}">✕</button>
            </div>

            {{-- Inline edit (hidden by default) --}}
            <div class="section-edit-form" data-for="{{ $section->id }}" style="display:none; padding:var(--space-3); border-bottom:1px solid var(--color-border);">
                <div class="av-form-group" style="margin-bottom:var(--space-3);">
                    <label class="av-label" for="sec-title-{{ $section->id }}">Title *</label>
                    <input id="sec-title-{{ $section->id }}" type="text" class="av-input section-edit-title" value="{{ $section->title }}" maxlength="180">
                </div>
                <div class="av-form-group" style="margin-bottom:var(--space-3);">
                    <label class="av-label" for="sec-body-{{ $section->id }}">Body</label>
                    <textarea id="sec-body-{{ $section->id }}" class="av-input section-edit-body" rows="4">{{ $section->body }}</textarea>
                </div>
                <div class="flex" style="justify-content:flex-end; gap:var(--space-2);">
                    <button type="button" class="av-btn av-btn--ghost section-cancel-edit" data-for="{{ $section->id }}">Cancel</button>
                    <button type="button" class="av-btn av-btn--primary section-save-edit" data-for="{{ $section->id }}">Save Section</button>
                </div>
            </div>

            {{-- Artifacts in section --}}
            <div class="section-artifacts" style="padding:var(--space-3);">
                <p style="font-size:.78rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--color-muted); margin-bottom:var(--space-2);">Artifacts in section</p>

                <div class="artifact-list" data-section="{{ $section->id }}" style="display:flex; flex-wrap:wrap; gap:var(--space-2); min-height:40px; margin-bottom:var(--space-3);">
                    @foreach($section->artifacts as $artifact)
                    <div class="artifact-chip" data-artifact-id="{{ $artifact->id }}"
                        style="display:flex; align-items:center; gap:var(--space-2); background:var(--color-surface-2); border:1px solid var(--color-border); border-radius:var(--radius-sm); padding:4px 8px; font-size:.8rem; cursor:grab;">
                        @php $img = $artifact->primaryImage(); @endphp
                        @if($img)
                            <img src="{{ $img->url }}" alt="" style="width:28px; height:28px; object-fit:cover; border-radius:2px;">
                        @endif
                        <span>{{ Str::limit($artifact->name, 30) }}</span>
                        <button type="button" class="remove-artifact-btn" data-section="{{ $section->id }}" data-artifact="{{ $artifact->id }}"
                            style="background:none; border:none; cursor:pointer; color:var(--color-muted); font-size:.85rem; padding:0; line-height:1;">&times;</button>
                    </div>
                    @endforeach
                </div>

                {{-- Artifact picker for this section --}}
                <div style="display:flex; gap:var(--space-2);">
                    <select class="av-input artifact-picker" data-section="{{ $section->id }}" style="flex:1; font-size:.85rem;">
                        <option value="">— Add an artifact —</option>
                        @foreach($museumArtifacts as $art)
                            <option value="{{ $art->id }}">{{ $art->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="av-btn av-btn--outline add-artifact-btn" data-section="{{ $section->id }}" style="font-size:.85rem;">Add</button>
                </div>
            </div>
        </div>
        @empty
            <p id="no-sections-msg" style="color:var(--color-muted); font-size:.9rem; text-align:center; padding:var(--space-6) 0;">
                No sections yet. Click "Add Section" to start building the exhibition.
            </p>
        @endforelse
    </div>
</div>
</div>

</div>{{-- end grid --}}
</div>
</div>
@endsection

@push('styles')
<style>
.av-tag--warning  { background: rgba(245,158,11,.12); color:#d97706; border:1px solid rgba(245,158,11,.25); }
.av-tag--success  { background: rgba(16,185,129,.12); color:#10b981; border:1px solid rgba(16,185,129,.25); }
.av-tag--secondary{ background: var(--color-surface-2); color:var(--color-muted); border:1px solid var(--color-border); }
.av-section-item:has([data-drag-handle]:active) { opacity: .6; }
.sortable-ghost { opacity: .4; border: 2px dashed var(--color-gold) !important; }
.artifact-chip:active { opacity: .5; }
@media (max-width: 960px) {
    div[style*="grid-template-columns:380px"] { grid-template-columns: 1fr !important; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
    const EX_ID  = {{ $exhibition->id }};

    // ── Helpers ──────────────────────────────────────────────────────────────
    async function post(url, body = {}) {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        return res.json();
    }

    async function del(url) {
        const res = await fetch(url, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        return res.json();
    }

    async function put(url, body = {}) {
        const res = await fetch(url, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        return res.json();
    }

    // ── Section add ──────────────────────────────────────────────────────────
    document.getElementById('add-section-btn').addEventListener('click', () => {
        document.getElementById('new-section-form').style.display = 'block';
        document.getElementById('new-section-title').focus();
    });
    document.getElementById('cancel-section-btn').addEventListener('click', () => {
        document.getElementById('new-section-form').style.display = 'none';
    });

    document.getElementById('save-section-btn').addEventListener('click', async () => {
        const title = document.getElementById('new-section-title').value.trim();
        const body  = document.getElementById('new-section-body').value.trim();
        if (!title) { alert('Please enter a section title.'); return; }

        const data = await post(`/curator/exhibitions/${EX_ID}/sections`, { title, body });
        if (data.success) {
            document.getElementById('no-sections-msg')?.remove();
            appendSection(data.section);
            document.getElementById('new-section-title').value = '';
            document.getElementById('new-section-body').value  = '';
            document.getElementById('new-section-form').style.display = 'none';
        }
    });

    function appendSection(section) {
        const list = document.getElementById('sections-list');
        const artifactOptionsHtml = @json($museumArtifacts->map(fn($a) => ['id' => $a->id, 'name' => $a->name]))
            .map(a => `<option value="${a.id}">${a.name}</option>`).join('');

        const div = document.createElement('div');
        div.className = 'av-section-item';
        div.dataset.sectionId = section.id;
        div.style.cssText = 'border:1px solid var(--color-border); border-radius:var(--radius-sm); margin-bottom:var(--space-3); background:var(--color-surface);';
        div.innerHTML = `
            <div class="flex" style="align-items:center; padding:var(--space-3); gap:var(--space-3); background:var(--color-surface-2); border-radius:var(--radius-sm) var(--radius-sm) 0 0; cursor:grab;" data-drag-handle>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-muted)" stroke-width="2"><circle cx="9" cy="5" r="1" fill="currentColor"/><circle cx="9" cy="12" r="1" fill="currentColor"/><circle cx="9" cy="19" r="1" fill="currentColor"/><circle cx="15" cy="5" r="1" fill="currentColor"/><circle cx="15" cy="12" r="1" fill="currentColor"/><circle cx="15" cy="19" r="1" fill="currentColor"/></svg>
                <span class="section-title-display" style="flex:1; font-weight:600; font-size:.9rem;">${section.title}</span>
                <button type="button" class="av-btn av-btn--ghost" style="font-size:.78rem; padding:2px 10px;" data-edit-section="${section.id}">Edit</button>
                <button type="button" class="av-btn av-btn--ghost" style="font-size:.78rem; padding:2px 10px; color:var(--color-danger);" data-delete-section="${section.id}">✕</button>
            </div>
            <div class="section-edit-form" data-for="${section.id}" style="display:none; padding:var(--space-3); border-bottom:1px solid var(--color-border);">
                <div class="av-form-group" style="margin-bottom:var(--space-3);">
                    <label class="av-label">Title *</label>
                    <input type="text" class="av-input section-edit-title" value="${section.title}" maxlength="180">
                </div>
                <div class="av-form-group" style="margin-bottom:var(--space-3);">
                    <label class="av-label">Body</label>
                    <textarea class="av-input section-edit-body" rows="4">${section.body || ''}</textarea>
                </div>
                <div class="flex" style="justify-content:flex-end; gap:var(--space-2);">
                    <button type="button" class="av-btn av-btn--ghost section-cancel-edit" data-for="${section.id}">Cancel</button>
                    <button type="button" class="av-btn av-btn--primary section-save-edit" data-for="${section.id}">Save Section</button>
                </div>
            </div>
            <div class="section-artifacts" style="padding:var(--space-3);">
                <p style="font-size:.78rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--color-muted); margin-bottom:var(--space-2);">Artifacts in section</p>
                <div class="artifact-list" data-section="${section.id}" style="display:flex; flex-wrap:wrap; gap:var(--space-2); min-height:40px; margin-bottom:var(--space-3);"></div>
                <div style="display:flex; gap:var(--space-2);">
                    <select class="av-input artifact-picker" data-section="${section.id}" style="flex:1; font-size:.85rem;">
                        <option value="">— Add an artifact —</option>
                        ${artifactOptionsHtml}
                    </select>
                    <button type="button" class="av-btn av-btn--outline add-artifact-btn" data-section="${section.id}" style="font-size:.85rem;">Add</button>
                </div>
            </div>`;
        list.appendChild(div);
        initSortable(div.querySelector('.artifact-list'));
    }

    // ── Section edit/delete (event delegation) ────────────────────────────────
    document.getElementById('sections-list').addEventListener('click', async (e) => {

        // Edit toggle
        const editBtn = e.target.closest('[data-edit-section]');
        if (editBtn) {
            const id = editBtn.dataset.editSection;
            document.querySelector(`.section-edit-form[data-for="${id}"]`).style.display = 'block';
            return;
        }

        // Cancel edit
        const cancelEdit = e.target.closest('.section-cancel-edit');
        if (cancelEdit) {
            document.querySelector(`.section-edit-form[data-for="${cancelEdit.dataset.for}"]`).style.display = 'none';
            return;
        }

        // Save edit
        const saveEdit = e.target.closest('.section-save-edit');
        if (saveEdit) {
            const id = saveEdit.dataset.for;
            const form = document.querySelector(`.section-edit-form[data-for="${id}"]`);
            const title = form.querySelector('.section-edit-title').value.trim();
            const body  = form.querySelector('.section-edit-body').value.trim();
            if (!title) { alert('Title is required.'); return; }
            const data = await put(`/curator/exhibitions/${EX_ID}/sections/${id}`, { title, body });
            if (data.success) {
                const item = form.closest('.av-section-item');
                item.querySelector('.section-title-display').textContent = title;
                form.style.display = 'none';
            }
            return;
        }

        // Delete section
        const deleteBtn = e.target.closest('[data-delete-section]');
        if (deleteBtn) {
            if (!confirm('Delete this section? All artifact memberships in it will also be removed.')) return;
            const id = deleteBtn.dataset.deleteSection;
            const data = await del(`/curator/exhibitions/${EX_ID}/sections/${id}`);
            if (data.success) {
                deleteBtn.closest('.av-section-item').remove();
            }
            return;
        }

        // Add artifact
        const addArtBtn = e.target.closest('.add-artifact-btn');
        if (addArtBtn) {
            const secId = addArtBtn.dataset.section;
            const picker = document.querySelector(`.artifact-picker[data-section="${secId}"]`);
            const artId = picker.value;
            if (!artId) return;
            const data = await post(`/curator/exhibition-sections/${secId}/artifacts`, { artifact_id: parseInt(artId) });
            if (data.success) {
                appendArtifactChip(secId, data.artifact);
                picker.value = '';
            }
            return;
        }

        // Remove artifact
        const removeArtBtn = e.target.closest('.remove-artifact-btn');
        if (removeArtBtn) {
            const { section: secId, artifact: artId } = removeArtBtn.dataset;
            if (!confirm('Remove this artifact from the section?')) return;
            const data = await del(`/curator/exhibition-sections/${secId}/artifacts/${artId}`);
            if (data.success) removeArtBtn.closest('.artifact-chip').remove();
            return;
        }
    });

    function appendArtifactChip(secId, artifact) {
        const list = document.querySelector(`.artifact-list[data-section="${secId}"]`);
        const chip = document.createElement('div');
        chip.className = 'artifact-chip';
        chip.dataset.artifactId = artifact.id;
        chip.style.cssText = 'display:flex; align-items:center; gap:var(--space-2); background:var(--color-surface-2); border:1px solid var(--color-border); border-radius:var(--radius-sm); padding:4px 8px; font-size:.8rem; cursor:grab;';
        chip.innerHTML = (artifact.cover_image ? `<img src="${artifact.cover_image}" alt="" style="width:28px;height:28px;object-fit:cover;border-radius:2px;">` : '') +
            `<span>${artifact.name.substring(0, 30)}</span>` +
            `<button type="button" class="remove-artifact-btn" data-section="${secId}" data-artifact="${artifact.id}" style="background:none;border:none;cursor:pointer;color:var(--color-muted);font-size:.85rem;padding:0;line-height:1;">&times;</button>`;
        list.appendChild(chip);
        initSortable(list);
    }

    // ── Sortable sections ─────────────────────────────────────────────────────
    Sortable.create(document.getElementById('sections-list'), {
        animation: 150,
        ghostClass: 'sortable-ghost',
        handle: '[data-drag-handle]',
        onEnd: async () => {
            const ids = [...document.querySelectorAll('.av-section-item')].map(el => parseInt(el.dataset.sectionId));
            await post(`/curator/exhibitions/${EX_ID}/sections/reorder`, { ids });
        },
    });

    // ── Sortable artifacts per section ────────────────────────────────────────
    function initSortable(listEl) {
        if (!listEl || listEl._sortable) return;
        listEl._sortable = Sortable.create(listEl, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: async () => {
                const secId = listEl.dataset.section;
                const ids = [...listEl.querySelectorAll('.artifact-chip')].map(el => parseInt(el.dataset.artifactId));
                await post(`/curator/exhibition-sections/${secId}/artifacts/reorder`, { ids });
            },
        });
    }

    document.querySelectorAll('.artifact-list').forEach(initSortable);
})();
</script>
@endpush
