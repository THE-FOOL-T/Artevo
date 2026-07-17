@php
    $artifact = $artifact ?? null;
    $selectedTags = old('tags', $artifact?->tags->pluck('name')->all() ?? []);
@endphp

{{-- ─── Met Museum Import Panel ─────────────────────────────────────────── --}}
<div id="met-import-panel" style="margin-bottom:var(--space-6); border:1px solid var(--brass-400); border-radius:var(--radius-md); overflow:hidden;">
    <button type="button" id="met-toggle"
        style="width:100%; display:flex; align-items:center; justify-content:space-between; padding:var(--space-4) var(--space-5); background:linear-gradient(135deg,#faf7f0,#f5efe0); border:none; cursor:pointer; font-size:var(--text-sm); font-weight:700; color:var(--ink-900); letter-spacing:.03em;">
        <span style="display:flex;align-items:center;gap:0.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--brass-700)" stroke-width="2"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 10v11M12 10v11M16 10v11"/></svg>
            🏛 Import from The Metropolitan Museum of Art (Free API)
        </span>
        <svg id="met-chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="transition:transform .2s;"><polyline points="6 9 12 15 18 9"/></svg>
    </button>

    <div id="met-body" style="display:none; padding:var(--space-5); background:#fff; border-top:1px solid var(--brass-300);">
        <p style="font-size:var(--text-sm); color:var(--ink-600); margin-bottom:var(--space-4);">
            Search 500,000+ artworks from the Met's open collection. Click any result to auto-fill the form below.
        </p>

        <div style="display:flex; gap:var(--space-3); margin-bottom:var(--space-4);">
            <input type="text" id="met-search-input" placeholder="e.g. Rosetta Stone, Greek vase, Egyptian mummy…"
                style="flex:1; padding:0.55rem 0.8rem; border:1px solid var(--color-border); border-radius:var(--radius-sm); font-size:var(--text-sm);">
            <button type="button" id="met-search-btn"
                data-url="{{ url('/api/met/search') }}"
                style="padding:0.55rem 1.1rem; background:var(--brass-700); color:#fff; border:none; border-radius:var(--radius-sm); cursor:pointer; font-size:var(--text-sm); font-weight:600; white-space:nowrap;">
                Search
            </button>
        </div>

        <div id="met-status" style="font-size:var(--text-sm); color:var(--ink-600); margin-bottom:var(--space-3);"></div>

        <div id="met-results" style="display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:var(--space-3); max-height:420px; overflow-y:auto;"></div>
    </div>
</div>

<script>
(function () {
    /* ── Toggle ── */
    const toggle  = document.getElementById('met-toggle');
    const body    = document.getElementById('met-body');
    const chevron = document.getElementById('met-chevron');
    toggle?.addEventListener('click', () => {
        const open = body.style.display === 'block';
        body.style.display   = open ? 'none' : 'block';
        chevron.style.transform = open ? '' : 'rotate(180deg)';
    });

    /* ── Search ── */
    const searchInput = document.getElementById('met-search-input');
    const searchBtn   = document.getElementById('met-search-btn');
    const statusEl    = document.getElementById('met-status');
    const resultsEl   = document.getElementById('met-results');

    function doSearch () {
        const q = searchInput.value.trim();
        if (!q) return;

        searchBtn.disabled = true;
        statusEl.textContent = 'Searching the Met collection…';
        resultsEl.innerHTML  = '';

        const metUrl = searchBtn.dataset.url;
        fetch(`${metUrl}?q=${encodeURIComponent(q)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            searchBtn.disabled = false;
            if (!data.results || data.results.length === 0) {
                statusEl.textContent = 'No results found. Try different keywords.';
                return;
            }
            statusEl.textContent = `Found ${data.count} matching artworks — showing first ${data.results.length}:`;
            renderResults(data.results);
        })
        .catch(() => {
            searchBtn.disabled = false;
            statusEl.textContent = '✗ Network error. Please try again.';
        });
    }

    searchBtn?.addEventListener('click', doSearch);
    searchInput?.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); doSearch(); } });

    /* ── Render result cards ── */
    function renderResults (items) {
        resultsEl.innerHTML = '';
        items.forEach(item => {
            const card = document.createElement('div');
            card.style.cssText = 'border:1px solid #e5e0d5; border-radius:8px; overflow:hidden; cursor:pointer; transition:box-shadow .15s, transform .15s; background:#fff;';
            card.innerHTML = `
                <div style="height:110px; overflow:hidden; background:#f0ebe0;">
                    <img src="${item.image_thumb || ''}" alt="${item.title}" loading="lazy"
                        style="width:100%; height:100%; object-fit:cover;" onerror="this.parentNode.style.background='#ddd'">
                </div>
                <div style="padding:8px;">
                    <p style="font-size:.75rem; font-weight:700; margin:0 0 2px; line-height:1.3; color:#1a1006;">${item.title.slice(0, 60)}${item.title.length > 60 ? '…' : ''}</p>
                    <p style="font-size:.7rem; color:#7a6a50; margin:0;">${[item.date_label, item.civilization].filter(Boolean).join(' · ')}</p>
                    ${item.department ? `<p style="font-size:.65rem; color:#a0905a; margin:2px 0 0;">${item.department}</p>` : ''}
                </div>`;

            card.addEventListener('mouseenter', () => { card.style.boxShadow = '0 4px 16px rgba(0,0,0,.12)'; card.style.transform = 'translateY(-2px)'; });
            card.addEventListener('mouseleave', () => { card.style.boxShadow = ''; card.style.transform = ''; });
            card.addEventListener('click', () => fillForm(item));

            resultsEl.appendChild(card);
        });
    }

    /* ── Fill form fields from selected Met object ── */
    function fillForm (item) {
        const set = (id, val) => { const el = document.getElementById(id); if (el && val) el.value = val; };

        set('name',             item.title);
        set('short_description', [item.date_label, item.civilization, item.medium].filter(Boolean).join(' · ').slice(0, 255));
        set('description',      item.description);
        set('civilization',     item.civilization);
        set('era',              item.era);
        set('country_of_origin', item.country_of_origin);
        set('dimensions',       item.dimensions);

        // Tags — append to existing
        const tagsInput = document.getElementById('tags');
        if (tagsInput && item.tags) {
            const existing = tagsInput.value;
            tagsInput.value = existing ? existing + ', ' + item.tags : item.tags;
        }

        // Flash success
        statusEl.innerHTML = `✓ Filled from: <strong>${item.title}</strong> &nbsp;<a href="${item.met_url}" target="_blank" style="color:var(--brass-700);font-size:.75rem;">View on Met website →</a>`;
        statusEl.style.color = 'var(--green-700)';

        // Scroll to form
        document.getElementById('name')?.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Highlight filled fields
        ['name','description','civilization','era','country_of_origin','dimensions'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.style.borderColor = 'var(--brass-500)';
                el.style.background  = '#fffbef';
                setTimeout(() => { el.style.borderColor = ''; el.style.background = ''; }, 2500);
            }
        });
    }
})();
</script>

<div class="grid grid-2" style="gap: var(--space-4);">
    <div class="av-field">
        <label for="name">Artifact name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $artifact->name ?? '') }}" required>
        @error('name') <span class="av-field__error">{{ $message }}</span> @enderror
    </div>
    <div class="av-field">
        <label for="status">Visibility</label>
        <select id="status" name="status" required>
            @foreach (['private' => 'Private (only you)', 'public' => 'Public (in the directory)', 'archived' => 'Archived'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $artifact->status ?? 'private') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <span class="av-field__error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="av-field">
    <label for="short_description">Short description <span class="text-muted">(shown on cards)</span></label>
    <input type="text" id="short_description" name="short_description" value="{{ old('short_description', $artifact->short_description ?? '') }}" maxlength="255">
    @error('short_description') <span class="av-field__error">{{ $message }}</span> @enderror
</div>

<div class="av-field">
    <label for="description">Full historical description</label>
    <textarea id="description" name="description" style="min-height: 180px;">{{ old('description', $artifact->description ?? '') }}</textarea>
    @error('description') <span class="av-field__error">{{ $message }}</span> @enderror
</div>

<div class="grid grid-2" style="gap: var(--space-4);">
    <div class="av-field">
        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">Select a category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('category_id', $artifact->category_id ?? 0) === $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id') <span class="av-field__error">{{ $message }}</span> @enderror
    </div>
    <div class="av-field">
        <label for="material_id">Primary material</label>
        <select id="material_id" name="material_id">
            <option value="">Select a material</option>
            @foreach ($materials as $material)
                <option value="{{ $material->id }}" @selected((int) old('material_id', $artifact->material_id ?? 0) === $material->id)>{{ $material->name }}</option>
            @endforeach
        </select>
        @error('material_id') <span class="av-field__error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="av-field">
    <label for="tags">Tags <span class="text-muted">(comma-separated, e.g. "Ancient Egypt, Bronze Age")</span></label>
    <input type="text" id="tags" name="tags_input" value="{{ implode(', ', $selectedTags) }}" placeholder="Ancient Egypt, Bronze Age">
    {{-- Split into tags[] on submit so the server always receives a clean array. --}}
    <div id="tags-hidden-inputs"></div>
    @error('tags') <span class="av-field__error">{{ $message }}</span> @enderror
</div>

<h3 style="font-size: var(--text-lg); margin-top: var(--space-6);">Provenance basics</h3>
<div class="grid grid-3" style="gap: var(--space-4);">
    <div class="av-field">
        <label for="civilization">Civilization</label>
        <input type="text" id="civilization" name="civilization" value="{{ old('civilization', $artifact->civilization ?? '') }}">
    </div>
    <div class="av-field">
        <label for="era">Era</label>
        <input type="text" id="era" name="era" value="{{ old('era', $artifact->era ?? '') }}">
    </div>
    <div class="av-field">
        <label for="century">Century</label>
        <input type="text" id="century" name="century" value="{{ old('century', $artifact->century ?? '') }}" placeholder="e.g. 5th century BCE">
    </div>
</div>
<div class="grid grid-3" style="gap: var(--space-4);">
    <div class="av-field">
        <label for="country_of_origin">Country of origin</label>
        <input type="text" id="country_of_origin" name="country_of_origin" value="{{ old('country_of_origin', $artifact->country_of_origin ?? '') }}">
    </div>
    <div class="av-field">
        <label for="region">Region</label>
        <input type="text" id="region" name="region" value="{{ old('region', $artifact->region ?? '') }}">
    </div>
    <div class="av-field">
        <label for="discovery_location">Discovery location</label>
        <input type="text" id="discovery_location" name="discovery_location" value="{{ old('discovery_location', $artifact->discovery_location ?? '') }}">
    </div>
</div>
<div class="av-field">
    <label for="language">Language / script</label>
    <input type="text" id="language" name="language" value="{{ old('language', $artifact->language ?? '') }}">
</div>

<h3 style="font-size: var(--text-lg); margin-top: var(--space-6);">Physical details</h3>
<div class="grid grid-3" style="gap: var(--space-4);">
    <div class="av-field">
        <label for="dimensions">Dimensions</label>
        <input type="text" id="dimensions" name="dimensions" value="{{ old('dimensions', $artifact->dimensions ?? '') }}" placeholder="e.g. 30cm x 20cm x 10cm">
    </div>
    <div class="av-field">
        <label for="weight">Weight</label>
        <input type="text" id="weight" name="weight" value="{{ old('weight', $artifact->weight ?? '') }}" placeholder="e.g. 2.5kg">
    </div>
    <div class="av-field">
        <label for="condition">Condition</label>
        <select id="condition" name="condition">
            <option value="">Select condition</option>
            @foreach (['Excellent', 'Good', 'Fair', 'Poor'] as $option)
                <option value="{{ $option }}" @selected(old('condition', $artifact->condition ?? '') === $option)>{{ $option }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="av-field">
    <label for="estimated_value">Estimated value (USD) <span class="text-muted">(optional, private unless you choose otherwise)</span></label>
    <input type="number" id="estimated_value" name="estimated_value" value="{{ old('estimated_value', $artifact->estimated_value ?? '') }}" min="0" step="0.01">
    @error('estimated_value') <span class="av-field__error">{{ $message }}</span> @enderror
</div>

<script>
    // Converts the comma-separated tags field into tags[] entries right
    // before submit, so the Form Request receives a clean array without
    // needing a JS tag-picker library.
    document.currentScript.closest('form')?.addEventListener('submit', function () {
        const input = document.getElementById('tags');
        const container = document.getElementById('tags-hidden-inputs');
        container.innerHTML = '';
        input.value.split(',').map(t => t.trim()).filter(Boolean).forEach(function (tag) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'tags[]';
            hidden.value = tag;
            container.appendChild(hidden);
        });
    });
</script>
