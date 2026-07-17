@extends('layouts.app')

@section('title', "Edit {$artifact->name} — Artevo")

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container max-w-content">
            <x-tag>{{ $museum->name }}</x-tag>
            <h1 style="margin-top: var(--space-4); margin-bottom: var(--space-2);">{{ $artifact->name }}</h1>
            <p style="font-family: var(--font-mono); font-size: var(--text-sm); color: var(--stone-600);">{{ $artifact->artifact_code }}</p>
            <p>
                <a href="{{ route('curator.museums.artifacts.index', $museum) }}" style="color: var(--brass-700); font-weight: 600;">&larr; Back to artifacts</a>
                @if ($artifact->isPublic())
                    &middot; <a href="{{ route('artifacts.show', $artifact) }}" style="color: var(--brass-700); font-weight: 600;">View public page &rarr;</a>
                @endif
            </p>

            <x-card class="mt-6">
                <h3>Profile</h3>
                <form method="POST" action="{{ route('curator.museums.artifacts.update', [$museum, $artifact]) }}" novalidate>
                    @csrf
                    @method('PUT')
                    @include('artifacts.partials.form')
                    <x-button type="submit" variant="primary" class="mt-4">Save changes</x-button>
                </form>
            </x-card>

            @include('artifacts.partials.media')

            {{-- ═══ Phase 11: Verification status ═══ --}}
            <x-card class="mt-6">
                <span class="av-card__eyebrow">Verification</span>
                @php
                    $badgeColors = [
                        'success' => ['bg'=>'rgba(16,185,129,.12)','color'=>'#10b981','border'=>'rgba(16,185,129,.3)'],
                        'warning' => ['bg'=>'rgba(245,158,11,.12)', 'color'=>'#d97706','border'=>'rgba(245,158,11,.3)'],
                        'danger'  => ['bg'=>'rgba(239,68,68,.1)',   'color'=>'#ef4444','border'=>'rgba(239,68,68,.25)'],
                        'muted'   => ['bg'=>'var(--color-surface-2)','color'=>'var(--color-muted)','border'=>'var(--color-border)'],
                    ];
                    $b = $badgeColors[$artifact->verificationBadgeColor()];
                @endphp
                <div style="display:flex; align-items:center; gap:var(--space-4); flex-wrap:wrap; margin-top:var(--space-3);">
                    <span style="display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:999px; font-size:.85rem; font-weight:600; background:{{ $b['bg'] }}; color:{{ $b['color'] }}; border:1px solid {{ $b['border'] }};">
                        @if($artifact->isVerified()) ✅
                        @elseif($artifact->isPendingVerification()) 🕐
                        @elseif($artifact->isVerificationRejected()) ✗
                        @else ○
                        @endif
                        {{ $artifact->verificationLabel() }}
                    </span>
                    @if($artifact->verification_note)
                        <p style="font-size:.85rem; color:var(--color-muted); margin:0;"><strong>Admin note:</strong> {{ $artifact->verification_note }}</p>
                    @endif
                    @can('submitForVerification', $artifact)
                        <form method="POST" action="{{ route('artifacts.verify-request', $artifact) }}">
                            @csrf
                            <button class="av-btn av-btn--outline" style="font-size:.85rem;">Submit for Verification</button>
                        </form>
                    @endcan
                </div>
            </x-card>

            {{-- ═══ Phase 11: Provenance records ═══ --}}
            <x-card class="mt-6" id="provenance-panel">
                <div class="flex" style="align-items:center; justify-content:space-between; margin-bottom:var(--space-4);">
                    <span class="av-card__eyebrow">Provenance</span>
                    <button type="button" class="av-btn av-btn--outline" style="font-size:.82rem;" id="add-provenance-btn">+ Add Record</button>
                </div>

                <div id="new-provenance-form" style="display:none; background:var(--color-surface-2); border-radius:var(--radius-sm); padding:var(--space-4); margin-bottom:var(--space-4); border:1px solid var(--color-border);">
                    <div class="grid grid-2" style="gap:var(--space-3); margin-bottom:var(--space-3);">
                        <div class="av-form-group">
                            <label class="av-label" for="np-type">Type *</label>
                            <select id="np-type" class="av-input">
                                @foreach(\App\Models\ArtifactProvenance::SUGGESTED_TYPES as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="av-form-group">
                            <label class="av-label" for="np-date">Date</label>
                            <input id="np-date" type="date" class="av-input">
                        </div>
                    </div>
                    <div class="av-form-group" style="margin-bottom:var(--space-3);">
                        <label class="av-label" for="np-title">Title *</label>
                        <input id="np-title" type="text" class="av-input" placeholder="e.g. Purchased at Sotheby's London" maxlength="180">
                    </div>
                    <div class="grid grid-2" style="gap:var(--space-3); margin-bottom:var(--space-3);">
                        <div class="av-form-group">
                            <label class="av-label" for="np-location">Location</label>
                            <input id="np-location" type="text" class="av-input" placeholder="City, Country" maxlength="180">
                        </div>
                        <div class="av-form-group">
                            <label class="av-label" for="np-source">Source URL</label>
                            <input id="np-source" type="url" class="av-input" placeholder="https://…">
                        </div>
                    </div>
                    <div class="av-form-group" style="margin-bottom:var(--space-3);">
                        <label class="av-label" for="np-description">Description</label>
                        <textarea id="np-description" class="av-input" rows="3" placeholder="Further details…"></textarea>
                    </div>
                    <div class="flex" style="justify-content:flex-end; gap:var(--space-2);">
                        <button type="button" class="av-btn av-btn--ghost" id="cancel-provenance-btn">Cancel</button>
                        <button type="button" class="av-btn av-btn--primary" id="save-provenance-btn">Add Record</button>
                    </div>
                </div>

                <div id="provenance-list">
                    @forelse($artifact->provenance as $record)
                    <div class="av-provenance-item" data-record-id="{{ $record->id }}"
                        style="display:flex; gap:var(--space-3); align-items:flex-start; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-sm); margin-bottom:var(--space-2); background:var(--color-surface); cursor:grab;">
                        <div style="flex-shrink:0; font-size:1.3rem;">{{ $record->typeIcon() }}</div>
                        <div style="flex:1;">
                            <div class="flex" style="align-items:center; gap:var(--space-2); flex-wrap:wrap; margin-bottom:2px;">
                                <span style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-gold);">{{ $record->typeLabel() }}</span>
                                @if($record->date)<span style="font-size:.75rem; color:var(--color-muted);">{{ $record->date->format('M j, Y') }}</span>@endif
                                @if($record->location)<span style="font-size:.75rem; color:var(--color-muted);">· {{ $record->location }}</span>@endif
                            </div>
                            <p class="prov-title-display" style="font-size:.9rem; font-weight:600; margin:0;">{{ $record->title }}</p>
                            @if($record->description)<p style="font-size:.8rem; color:var(--color-muted); margin:2px 0 0; line-height:1.5;">{{ $record->description }}</p>@endif
                        </div>
                        <div class="flex" style="gap:var(--space-1); align-items:center; flex-shrink:0;">
                            <button type="button" class="av-btn av-btn--ghost" style="font-size:.78rem; padding:2px 8px;" data-edit-provenance="{{ $record->id }}">Edit</button>
                            <button type="button" class="av-btn av-btn--ghost" style="font-size:.78rem; padding:2px 8px; color:var(--color-danger);" data-delete-provenance="{{ $record->id }}">✕</button>
                        </div>
                    </div>
                    @empty
                        <p id="no-provenance-msg" style="font-size:.875rem; color:var(--color-muted); text-align:center; padding:var(--space-6) 0;">
                            No provenance records yet. Click "Add Record" to document this artifact's ownership history.
                        </p>
                    @endforelse
                </div>
            </x-card>

            {{-- ═══ Phase 12: Curator Notes (private) ═══ --}}
            <x-card class="mt-6" id="curator-notes-panel">
                <div class="flex" style="align-items:center; justify-content:space-between; margin-bottom:var(--space-4);">
                    <div>
                        <span class="av-card__eyebrow">Curator Notes</span>
                        <p style="font-size:.78rem; color:var(--color-muted); margin:0;">Private — never shown publicly.</p>
                    </div>
                    <button type="button" class="av-btn av-btn--outline" style="font-size:.82rem;" id="add-note-btn">+ Add Note</button>
                </div>

                <div id="new-note-form" style="display:none; background:var(--color-surface-2); border-radius:var(--radius-sm); padding:var(--space-4); margin-bottom:var(--space-4); border:1px solid var(--color-border);">
                    <div class="av-form-group" style="margin-bottom:var(--space-3);">
                        <label class="av-label" for="nn-type">Note Type *</label>
                        <select id="nn-type" class="av-input">
                            @foreach(\App\Models\CuratorNote::TYPES as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="av-form-group" style="margin-bottom:var(--space-3);">
                        <label class="av-label" for="nn-body">Note *</label>
                        <textarea id="nn-body" class="av-input" rows="4" placeholder="Write your observation, recommendation, or note here…"></textarea>
                    </div>
                    <div class="flex" style="justify-content:flex-end; gap:var(--space-2);">
                        <button type="button" class="av-btn av-btn--ghost" id="cancel-note-btn">Cancel</button>
                        <button type="button" class="av-btn av-btn--primary" id="save-note-btn">Save Note</button>
                    </div>
                </div>

                <div id="notes-list">
                    @forelse($artifact->curatorNotes as $note)
                    <div class="av-note-item" data-note-id="{{ $note->id }}"
                        style="border-left: 3px solid {{ $note->typeBorderColor() }}; padding: var(--space-3); background:var(--color-surface); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; margin-bottom:var(--space-3); position:relative;">
                        <div class="flex" style="align-items:center; justify-content:space-between; margin-bottom:var(--space-2);">
                            <div class="flex" style="align-items:center; gap:var(--space-2);">
                                @if($note->is_pinned)<span title="Pinned">📌</span>@endif
                                <span style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em;">{{ $note->typeLabel() }}</span>
                                <span style="font-size:.75rem; color:var(--color-muted);">· {{ $note->author?->name ?? 'Unknown' }} · {{ $note->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex" style="gap:var(--space-1);">
                                <button type="button" class="av-btn av-btn--ghost" style="font-size:.75rem; padding:2px 6px;" data-pin-note="{{ $note->id }}">{{ $note->is_pinned ? 'Unpin' : 'Pin' }}</button>
                                <button type="button" class="av-btn av-btn--ghost" style="font-size:.75rem; padding:2px 6px; color:var(--color-danger);" data-delete-note="{{ $note->id }}">✕</button>
                            </div>
                        </div>
                        <p class="note-body-display" style="font-size:.875rem; margin:0; line-height:1.6;">{{ $note->body }}</p>
                    </div>
                    @empty
                        <p id="no-notes-msg" style="font-size:.875rem; color:var(--color-muted); text-align:center; padding:var(--space-6) 0;">
                            No notes yet. Add your first observation or condition check.
                        </p>
                    @endforelse
                </div>
            </x-card>

            {{-- ═══ Phase 12: Restoration Records (public) ═══ --}}
            <x-card class="mt-6" id="restoration-panel">
                <div class="flex" style="align-items:center; justify-content:space-between; margin-bottom:var(--space-4);">
                    <div>
                        <span class="av-card__eyebrow">Restoration Records</span>
                        <p style="font-size:.78rem; color:var(--color-muted); margin:0;">Publicly visible conservation history.</p>
                    </div>
                    <button type="button" class="av-btn av-btn--outline" style="font-size:.82rem;" id="add-restoration-btn">+ Add Record</button>
                </div>

                <div id="new-restoration-form" style="display:none; background:var(--color-surface-2); border-radius:var(--radius-sm); padding:var(--space-4); margin-bottom:var(--space-4); border:1px solid var(--color-border);">
                    <div class="grid grid-2" style="gap:var(--space-3); margin-bottom:var(--space-3);">
                        <div class="av-form-group">
                            <label class="av-label" for="nr-category">Category *</label>
                            <select id="nr-category" class="av-input">
                                @foreach(\App\Models\RestorationRecord::CATEGORIES as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="av-form-group">
                            <label class="av-label" for="nr-conservator">Lead Conservator</label>
                            <input id="nr-conservator" type="text" class="av-input" placeholder="Dr. Jane Smith" maxlength="120">
                        </div>
                    </div>
                    <div class="av-form-group" style="margin-bottom:var(--space-3);">
                        <label class="av-label" for="nr-title">Title *</label>
                        <input id="nr-title" type="text" class="av-input" placeholder="e.g. Surface cleaning and consolidation" maxlength="180">
                    </div>
                    <div class="grid grid-2" style="gap:var(--space-3); margin-bottom:var(--space-3);">
                        <div class="av-form-group">
                            <label class="av-label" for="nr-institution">Institution</label>
                            <input id="nr-institution" type="text" class="av-input" placeholder="Conservation lab or museum" maxlength="180">
                        </div>
                        <div class="av-form-group">
                            <label class="av-label" for="nr-started">Started</label>
                            <input id="nr-started" type="date" class="av-input">
                        </div>
                    </div>
                    <div class="grid grid-2" style="gap:var(--space-3); margin-bottom:var(--space-3);">
                        <div class="av-form-group">
                            <label class="av-label" for="nr-completed">Completed</label>
                            <input id="nr-completed" type="date" class="av-input">
                        </div>
                    </div>
                    <div class="av-form-group" style="margin-bottom:var(--space-3);">
                        <label class="av-label" for="nr-description">Description</label>
                        <textarea id="nr-description" class="av-input" rows="3" placeholder="What was done, methods used, materials applied…"></textarea>
                    </div>
                    <div class="flex" style="justify-content:flex-end; gap:var(--space-2);">
                        <button type="button" class="av-btn av-btn--ghost" id="cancel-restoration-btn">Cancel</button>
                        <button type="button" class="av-btn av-btn--primary" id="save-restoration-btn">Add Record</button>
                    </div>
                </div>

                <div id="restoration-list">
                    @forelse($artifact->restorationRecords as $record)
                    <div class="av-restoration-item" data-record-id="{{ $record->id }}"
                        style="display:flex; gap:var(--space-3); align-items:flex-start; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-sm); margin-bottom:var(--space-2); background:var(--color-surface); cursor:grab;">
                        <div style="flex-shrink:0; font-size:1.3rem;">{{ $record->categoryIcon() }}</div>
                        <div style="flex:1;">
                            <div class="flex" style="align-items:center; gap:var(--space-2); flex-wrap:wrap; margin-bottom:2px;">
                                <span style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-gold);">{{ $record->categoryLabel() }}</span>
                                @if($record->durationLabel())<span style="font-size:.75rem; color:var(--color-muted);">{{ $record->durationLabel() }}</span>@endif
                                @if($record->institution)<span style="font-size:.75rem; color:var(--color-muted);">· {{ $record->institution }}</span>@endif
                            </div>
                            <p class="restoration-title-display" style="font-size:.9rem; font-weight:600; margin:0;">{{ $record->title }}</p>
                            @if($record->conservator_name)<p style="font-size:.8rem; color:var(--color-muted); margin:2px 0 0;">Lead: {{ $record->conservator_name }}</p>@endif
                        </div>
                        <div class="flex" style="gap:var(--space-1); align-items:center; flex-shrink:0;">
                            <button type="button" class="av-btn av-btn--ghost" style="font-size:.78rem; padding:2px 8px; color:var(--color-danger);" data-delete-restoration="{{ $record->id }}">✕</button>
                        </div>
                    </div>
                    @empty
                        <p id="no-restoration-msg" style="font-size:.875rem; color:var(--color-muted); text-align:center; padding:var(--space-6) 0;">
                            No restoration records yet.
                        </p>
                    @endforelse
                </div>
            </x-card>

            <x-card class="mt-6">
                <h3 style="color: var(--red-600);">Delete this artifact</h3>
                <p>This removes the artifact, its gallery, and its documents. This cannot be undone.</p>
                <x-button type="button" variant="outline-dark" data-modal-open="delete-artifact" style="border-color: var(--red-600); color: var(--red-600);">Delete artifact</x-button>

                <x-modal id="delete-artifact" title="Delete this artifact?">
                    <p>This permanently removes "{{ $artifact->name }}" and everything attached to it.</p>
                    <form method="POST" action="{{ route('curator.museums.artifacts.destroy', [$museum, $artifact]) }}">
                        @csrf
                        @method('DELETE')
                        <div class="av-modal__actions">
                            <x-button type="button" variant="outline-dark" data-modal-close>Cancel</x-button>
                            <x-button type="submit" variant="dark" style="background: var(--red-600);">Delete artifact</x-button>
                        </div>
                    </form>
                </x-modal>
            </x-card>
        </div>
    </section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const ART  = '{{ $artifact->slug }}';

    const req = (method, url, body = null) => fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: body ? JSON.stringify(body) : undefined,
    }).then(r => r.json());

    document.getElementById('add-provenance-btn').addEventListener('click', () => {
        document.getElementById('new-provenance-form').style.display = 'block';
        document.getElementById('np-title').focus();
    });
    document.getElementById('cancel-provenance-btn').addEventListener('click', () => {
        document.getElementById('new-provenance-form').style.display = 'none';
    });
    document.getElementById('save-provenance-btn').addEventListener('click', async () => {
        const title = document.getElementById('np-title').value.trim();
        if (!title) { alert('Title is required.'); return; }
        const data = await req('POST', `/artifacts/${ART}/provenance`, {
            type: document.getElementById('np-type').value,
            title,
            description: document.getElementById('np-description').value.trim() || null,
            date: document.getElementById('np-date').value || null,
            location: document.getElementById('np-location').value.trim() || null,
            source_url: document.getElementById('np-source').value.trim() || null,
        });
        if (data.success) {
            document.getElementById('no-provenance-msg')?.remove();
            const r = data.record;
            const list = document.getElementById('provenance-list');
            const div  = document.createElement('div');
            div.className = 'av-provenance-item';
            div.dataset.recordId = r.id;
            div.style.cssText = 'display:flex;gap:var(--space-3);align-items:flex-start;padding:var(--space-3);border:1px solid var(--color-border);border-radius:var(--radius-sm);margin-bottom:var(--space-2);background:var(--color-surface);cursor:grab;';
            div.innerHTML = `<div style="flex-shrink:0;font-size:1.3rem;">${r.type_icon}</div><div style="flex:1;"><div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:2px;"><span style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-gold);">${r.type_label}</span>${r.date_human?`<span style="font-size:.75rem;color:var(--color-muted);">${r.date_human}</span>`:''}</div><p class="prov-title-display" style="font-size:.9rem;font-weight:600;margin:0;">${r.title}</p></div><div style="display:flex;gap:4px;align-items:center;flex-shrink:0;"><button type="button" class="av-btn av-btn--ghost" style="font-size:.78rem;padding:2px 8px;" data-edit-provenance="${r.id}">Edit</button><button type="button" class="av-btn av-btn--ghost" style="font-size:.78rem;padding:2px 8px;color:var(--color-danger);" data-delete-provenance="${r.id}">✕</button></div>`;
            list.appendChild(div);
            ['np-title','np-description','np-date','np-location','np-source'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('new-provenance-form').style.display = 'none';
        }
    });

    document.getElementById('provenance-list').addEventListener('click', async (e) => {
        const editBtn = e.target.closest('[data-edit-provenance]');
        if (editBtn) {
            const id    = editBtn.dataset.editProvenance;
            const item  = editBtn.closest('.av-provenance-item');
            const title = prompt('Edit title:', item.querySelector('.prov-title-display').textContent);
            if (!title) return;
            const data = await req('PUT', `/artifacts/${ART}/provenance/${id}`, { type:'other', title });
            if (data.success) item.querySelector('.prov-title-display').textContent = data.record.title;
            return;
        }
        const delBtn = e.target.closest('[data-delete-provenance]');
        if (delBtn) {
            if (!confirm('Delete this provenance record?')) return;
            const data = await req('DELETE', `/artifacts/${ART}/provenance/${delBtn.dataset.deleteProvenance}`);
            if (data.success) delBtn.closest('.av-provenance-item').remove();
        }
    });

    Sortable.create(document.getElementById('provenance-list'), {
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: async () => {
            const ids = [...document.querySelectorAll('.av-provenance-item')].map(el => parseInt(el.dataset.recordId));
            await req('POST', `/artifacts/${ART}/provenance/reorder`, { ids });
        },
    });

    // ─── Phase 12: Curator Notes ───────────────────────────────────────────────
    document.getElementById('add-note-btn').addEventListener('click', () => {
        document.getElementById('new-note-form').style.display = 'block';
        document.getElementById('nn-body').focus();
    });
    document.getElementById('cancel-note-btn').addEventListener('click', () => {
        document.getElementById('new-note-form').style.display = 'none';
    });
    document.getElementById('save-note-btn').addEventListener('click', async () => {
        const body = document.getElementById('nn-body').value.trim();
        if (!body) { alert('Note content is required.'); return; }
        const data = await req('POST', `/artifacts/${ART}/notes`, {
            note_type: document.getElementById('nn-type').value,
            body
        });
        if (data.success) {
            window.location.reload(); // Simple reload for notes to preserve pin ordering
        }
    });

    document.getElementById('notes-list').addEventListener('click', async (e) => {
        const pinBtn = e.target.closest('[data-pin-note]');
        if (pinBtn) {
            const data = await req('POST', `/artifacts/${ART}/notes/${pinBtn.dataset.pinNote}/pin`);
            if (data.success) window.location.reload();
            return;
        }
        const delBtn = e.target.closest('[data-delete-note]');
        if (delBtn) {
            if (!confirm('Delete this note?')) return;
            const data = await req('DELETE', `/artifacts/${ART}/notes/${delBtn.dataset.deleteNote}`);
            if (data.success) delBtn.closest('.av-note-item').remove();
        }
    });

    // ─── Phase 12: Restoration Records ──────────────────────────────────────────
    document.getElementById('add-restoration-btn').addEventListener('click', () => {
        document.getElementById('new-restoration-form').style.display = 'block';
        document.getElementById('nr-title').focus();
    });
    document.getElementById('cancel-restoration-btn').addEventListener('click', () => {
        document.getElementById('new-restoration-form').style.display = 'none';
    });
    document.getElementById('save-restoration-btn').addEventListener('click', async () => {
        const title = document.getElementById('nr-title').value.trim();
        if (!title) { alert('Title is required.'); return; }
        const data = await req('POST', `/artifacts/${ART}/restoration`, {
            category: document.getElementById('nr-category').value,
            title,
            description: document.getElementById('nr-description').value.trim() || null,
            conservator_name: document.getElementById('nr-conservator').value.trim() || null,
            institution: document.getElementById('nr-institution').value.trim() || null,
            started_at: document.getElementById('nr-started').value || null,
            completed_at: document.getElementById('nr-completed').value || null,
        });
        if (data.success) {
            document.getElementById('no-restoration-msg')?.remove();
            const r = data.record;
            const list = document.getElementById('restoration-list');
            const div  = document.createElement('div');
            div.className = 'av-restoration-item';
            div.dataset.recordId = r.id;
            div.style.cssText = 'display:flex;gap:var(--space-3);align-items:flex-start;padding:var(--space-3);border:1px solid var(--color-border);border-radius:var(--radius-sm);margin-bottom:var(--space-2);background:var(--color-surface);cursor:grab;';
            div.innerHTML = `<div style="flex-shrink:0;font-size:1.3rem;">${r.category_icon}</div><div style="flex:1;"><div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:2px;"><span style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--color-gold);">${r.category_label}</span>${r.duration_label?`<span style="font-size:.75rem;color:var(--color-muted);">${r.duration_label}</span>`:''}${r.institution?`<span style="font-size:.75rem;color:var(--color-muted);">· ${r.institution}</span>`:''}</div><p class="restoration-title-display" style="font-size:.9rem;font-weight:600;margin:0;">${r.title}</p>${r.conservator_name?`<p style="font-size:.8rem;color:var(--color-muted);margin:2px 0 0;">Lead: ${r.conservator_name}</p>`:''}</div><div style="display:flex;gap:4px;align-items:center;flex-shrink:0;"><button type="button" class="av-btn av-btn--ghost" style="font-size:.78rem;padding:2px 8px;color:var(--color-danger);" data-delete-restoration="${r.id}">✕</button></div>`;
            list.appendChild(div);
            ['nr-title','nr-description','nr-conservator','nr-institution','nr-started','nr-completed'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('new-restoration-form').style.display = 'none';
        } else {
            alert(data.message || 'Error saving record');
        }
    });

    document.getElementById('restoration-list').addEventListener('click', async (e) => {
        const delBtn = e.target.closest('[data-delete-restoration]');
        if (delBtn) {
            if (!confirm('Delete this restoration record?')) return;
            const data = await req('DELETE', `/artifacts/${ART}/restoration/${delBtn.dataset.deleteRestoration}`);
            if (data.success) delBtn.closest('.av-restoration-item').remove();
        }
    });

    Sortable.create(document.getElementById('restoration-list'), {
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: async () => {
            const ids = [...document.querySelectorAll('.av-restoration-item')].map(el => parseInt(el.dataset.recordId));
            await req('POST', `/artifacts/${ART}/restoration/reorder`, { ids });
        },
    });
})();
</script>
@endpush
