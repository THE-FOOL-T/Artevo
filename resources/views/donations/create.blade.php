@extends('layouts.app')

@section('title', 'Donate an Artifact — Artevo')
@section('meta_description', 'Submit a donation request to transfer an artifact from your collection to a verified museum on Artevo.')

@section('content')

<section class="av-section av-section--white" style="padding-top: var(--space-10);">
    <div class="container" style="max-width: 680px;">
        <p><a href="{{ route('donations.index') }}" style="color: var(--brass-700); font-weight: 600;">← Back to my donations</a></p>

        <x-tag class="mt-6">Artifact Donation</x-tag>
        <h1 style="margin-top: var(--space-3);">Donate an Artifact</h1>
        <p style="max-width: 560px; color: var(--color-muted); line-height: 1.7;">
            Transfer an artifact from your collection to a verified museum. The museum's curator will be notified
            and an administrator will review the request before ownership is transferred.
        </p>

        @if($errors->any())
            <x-alert type="error" class="mt-4">
                <ul style="margin:0; padding-left:var(--space-4);">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <x-card class="mt-8">
            <form method="POST" action="{{ route('donations.store') }}">
                @csrf

                {{-- Hidden slug — resolves to Artifact inside the controller --}}
                @if($artifact)
                    <input type="hidden" name="artifact_slug" value="{{ $artifact->slug }}">
                @else
                    {{-- If no artifact pre-selected, show a text prompt --}}
                    <div style="margin-bottom: var(--space-5);">
                        <label for="artifact_slug" style="display:block; font-weight:600; margin-bottom:var(--space-2);">
                            Artifact slug <span style="color:var(--color-danger);">*</span>
                        </label>
                        <input type="text" name="artifact_slug" id="artifact_slug"
                               value="{{ old('artifact_slug') }}"
                               placeholder="e.g. bronze-statuette"
                               class="av-input" style="width:100%;" required>
                        <p style="font-size:.78rem; color:var(--color-muted); margin-top:var(--space-1);">
                            You can find the artifact slug in its URL. To donate from its detail page, use the "Donate" button there.
                        </p>
                    </div>
                @endif

                {{-- Artifact preview card --}}
                @if($artifact)
                    <div style="display:flex; align-items:center; gap:var(--space-4); padding:var(--space-4); border-radius:var(--radius-md); background:var(--color-surface-2); border:1px solid var(--color-border); margin-bottom:var(--space-6);">
                        @if($artifact->primaryImage())
                            <img src="{{ $artifact->primaryImage()->url() }}" alt="{{ $artifact->name }}"
                                 style="width:64px; height:64px; object-fit:cover; border-radius:var(--radius-sm); flex-shrink:0;">
                        @else
                            <div style="width:64px; height:64px; background:var(--color-surface); border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; font-size:1.8rem;">🏺</div>
                        @endif
                        <div>
                            <p style="font-weight:700; margin:0; font-size:1rem;">{{ $artifact->name }}</p>
                            <p style="font-size:.78rem; color:var(--color-muted); margin:4px 0 0;">{{ $artifact->artifact_code }}</p>
                            @if($artifact->category)
                                <p style="font-size:.75rem; color:var(--color-gold); font-weight:600; margin:2px 0 0; text-transform:uppercase; letter-spacing:.04em;">{{ $artifact->category->name }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Recipient museum --}}
                <div style="margin-bottom: var(--space-5);">
                    <label for="museum_id" style="display:block; font-weight:600; margin-bottom:var(--space-2);">
                        Recipient Museum <span style="color:var(--color-danger);">*</span>
                    </label>
                    <select name="museum_id" id="museum_id" class="av-input" style="width:100%;" required>
                        <option value="">— Select a verified museum —</option>
                        @foreach($museums as $museum)
                            <option value="{{ $museum->id }}" {{ old('museum_id') == $museum->id ? 'selected' : '' }}>
                                {{ $museum->name }}{{ $museum->city ? ' — ' . $museum->city : '' }}{{ $museum->country ? ', ' . $museum->country : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('museum_id')
                        <p style="font-size:.8rem; color:var(--color-danger); margin-top:var(--space-1);">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Message --}}
                <div style="margin-bottom: var(--space-6);">
                    <label for="message" style="display:block; font-weight:600; margin-bottom:var(--space-2);">
                        Message <span style="color:var(--color-muted); font-weight:400;">(optional)</span>
                    </label>
                    <textarea name="message" id="message" rows="4"
                              class="av-input" style="width:100%; resize:vertical;"
                              maxlength="1000"
                              placeholder="Tell the museum about the artifact's history, why you are donating, or any conditions you'd like noted…">{{ old('message') }}</textarea>
                    <p style="font-size:.75rem; color:var(--color-muted); margin-top:var(--space-1);">Max 1,000 characters.</p>
                    @error('message')
                        <p style="font-size:.8rem; color:var(--color-danger); margin-top:var(--space-1);">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Disclaimer --}}
                <div style="background:rgba(212,175,55,.07); border:1px solid rgba(212,175,55,.2); border-radius:var(--radius-md); padding:var(--space-4); margin-bottom:var(--space-6);">
                    <p style="font-size:.82rem; color:var(--color-muted); margin:0; line-height:1.6;">
                        ⚠️ <strong style="color:var(--color-heading);">Please read before submitting:</strong>
                        Submitting this request does not immediately transfer ownership.
                        An Artevo administrator will review the request and contact you if any additional documentation is needed.
                        Ownership is only transferred after admin approval and explicit confirmation.
                    </p>
                </div>

                <div class="flex" style="gap:var(--space-3); flex-wrap:wrap;">
                    <button type="submit" class="av-btn av-btn--primary">Submit Donation Request</button>
                    <a href="{{ route('donations.index') }}" class="av-btn av-btn--outline">Cancel</a>
                </div>
            </form>
        </x-card>
    </div>
</section>

@endsection
