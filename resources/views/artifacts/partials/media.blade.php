<x-card class="mt-6">
    <h3>Gallery</h3>
    <p>The first image uploaded becomes the cover automatically — click "Make cover" on any other image to change it.</p>

    @if ($artifact->images->isNotEmpty())
        <div class="grid grid-4" style="gap: var(--space-3); margin-bottom: var(--space-5);">
            @foreach ($artifact->images as $image)
                <div style="position: relative;">
                    <img src="{{ $image->url() }}" alt="{{ $image->caption }}" style="width: 100%; height: 110px; object-fit: cover; border-radius: var(--radius-sm); border: 2px solid {{ $image->is_primary ? 'var(--brass-600)' : 'var(--color-border)' }};">
                    @if ($image->is_primary)
                        <span class="av-tag av-tag--pill" style="position: absolute; top: 4px; left: 4px; background: var(--brass-600); color: var(--ink-900); font-size: 10px; padding: 2px 6px;">Cover</span>
                    @endif
                    <form method="POST" action="{{ route('artifacts.images.destroy', [$artifact, $image]) }}" style="position: absolute; top: 4px; right: 4px;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" aria-label="Remove image" style="background: rgba(21,18,13,0.75); border: none; color: #fff; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; line-height: 1;">&times;</button>
                    </form>
                    @unless ($image->is_primary)
                        <form method="POST" action="{{ route('artifacts.images.primary', [$artifact, $image]) }}" style="position: absolute; bottom: 4px; left: 4px; right: 4px;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" style="width: 100%; font-size: 10px; background: rgba(21,18,13,0.75); color: #fff; border: none; border-radius: 4px; padding: 3px; cursor: pointer;">Make cover</button>
                        </form>
                    @endunless
                </div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('artifacts.images.store', $artifact) }}" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="av-field">
            <label for="images">Add images</label>
            <input type="file" id="images" name="images[]" accept="image/png, image/jpeg, image/webp" multiple>
            @error('images') <span class="av-field__error">{{ $message }}</span> @enderror
            @error('images.*') <span class="av-field__error">{{ $message }}</span> @enderror
        </div>
        <x-button type="submit" variant="outline-dark">Upload</x-button>
    </form>
</x-card>

<x-card class="mt-6">
    <h3>Documents</h3>
    <p>Certificates, manuscripts, or research papers — PDF, JPG, or PNG.</p>

    @if ($artifact->documents->isNotEmpty())
        <div style="margin-bottom: var(--space-5); display: flex; flex-direction: column; gap: var(--space-3);">
            @foreach ($artifact->documents as $document)
                <div class="flex-between" style="padding: var(--space-3); border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                    <div>
                        <a href="{{ $document->url() }}" target="_blank" rel="noopener" style="color: var(--brass-700); font-weight: 600;">{{ $document->title }}</a>
                        @if ($document->document_type)
                            <div style="font-size: var(--text-xs); color: var(--stone-600);">{{ $document->document_type }}</div>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('artifacts.documents.destroy', [$artifact, $document]) }}">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" variant="outline-dark" style="padding: 0.4rem 0.8rem; font-size: var(--text-xs);">Remove</x-button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('artifacts.documents.store', $artifact) }}" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="grid grid-3" style="gap: var(--space-4);">
            <div class="av-field">
                <label for="doc_title">Title</label>
                <input type="text" id="doc_title" name="title" required>
                @error('title') <span class="av-field__error">{{ $message }}</span> @enderror
            </div>
            <div class="av-field">
                <label for="document_type">Type <span class="text-muted">(optional)</span></label>
                <input type="text" id="document_type" name="document_type" placeholder="e.g. Certificate">
            </div>
            <div class="av-field">
                <label for="document">File</label>
                <input type="file" id="document" name="document" accept="application/pdf,image/png,image/jpeg" required>
                @error('document') <span class="av-field__error">{{ $message }}</span> @enderror
            </div>
        </div>
        <x-button type="submit" variant="outline-dark">Upload document</x-button>
    </form>
</x-card>
