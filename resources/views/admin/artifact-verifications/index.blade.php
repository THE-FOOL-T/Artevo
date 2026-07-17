@php use Illuminate\Support\Str; @endphp

@extends('layouts.app')

@section('title', 'Artifact Verification Queue — Admin — Artevo')

@section('content')
<div class="av-section">
<div class="container">

    <div class="flex" style="align-items:center; justify-content:space-between; margin-bottom:var(--space-6); flex-wrap:wrap; gap:var(--space-4);">
        <div>
            <x-tag>Admin</x-tag>
            <h1 class="av-page-title" style="margin-top:var(--space-2);">Artifact Verification Queue</h1>
        </div>
        <form method="GET" action="{{ route('admin.artifact-verifications.index') }}" style="display:flex; gap:var(--space-2);">
            <input type="search" name="search" placeholder="Search artifact name or code…"
                value="{{ request('search') }}" class="av-input" style="width:240px;">
            <button type="submit" class="av-btn av-btn--outline">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.artifact-verifications.index') }}" class="av-btn av-btn--ghost">Clear</a>
            @endif
        </form>
    </div>

    {{-- Stats bar --}}
    <div class="grid grid-3" style="gap:var(--space-4); margin-bottom:var(--space-8);">
        <div class="av-card" style="padding:var(--space-4); text-align:center;">
            <div style="font-size:2rem; font-weight:700; color:#d97706;">{{ $stats['pending'] }}</div>
            <p style="font-size:.85rem; color:var(--color-muted); margin:var(--space-1) 0 0;">Pending Review</p>
        </div>
        <div class="av-card" style="padding:var(--space-4); text-align:center;">
            <div style="font-size:2rem; font-weight:700; color:#10b981;">{{ $stats['verified'] }}</div>
            <p style="font-size:.85rem; color:var(--color-muted); margin:var(--space-1) 0 0;">Verified All-Time</p>
        </div>
        <div class="av-card" style="padding:var(--space-4); text-align:center;">
            <div style="font-size:2rem; font-weight:700; color:var(--color-danger);">{{ $stats['rejected'] }}</div>
            <p style="font-size:.85rem; color:var(--color-muted); margin:var(--space-1) 0 0;">Rejected All-Time</p>
        </div>
    </div>

    @if(session('success'))
        <div style="background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.3); border-radius:var(--radius-sm); padding:var(--space-3) var(--space-4); color:#10b981; margin-bottom:var(--space-4);">
            {{ session('success') }}
        </div>
    @endif

    @if($artifacts->isEmpty())
        <div style="text-align:center; padding:var(--space-16) 0; color:var(--color-muted);">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin-bottom:var(--space-4); opacity:.35;"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 style="font-size:1.1rem; margin-bottom:var(--space-2);">All clear!</h3>
            <p>{{ request('search') ? 'No pending artifacts match your search.' : 'No artifacts are currently awaiting verification.' }}</p>
        </div>
    @else
        <div style="display:flex; flex-direction:column; gap:var(--space-4);">
            @foreach($artifacts as $artifact)
            @php
                $owner = $artifact->isMuseumArtifact()
                    ? ($artifact->museum->name ?? '—')
                    : ($artifact->collector->name ?? '—');
                $ownerType = $artifact->isMuseumArtifact() ? 'Museum' : 'Collector';
                $img = $artifact->primaryImage();
            @endphp

            <div class="av-card" style="display:flex; gap:var(--space-4); overflow:hidden; align-items:stretch;" id="artifact-{{ $artifact->id }}">

                {{-- Thumbnail --}}
                <div style="width:100px; flex-shrink:0; background:var(--color-surface-2); overflow:hidden;">
                    @if($img)
                        <img src="{{ $img->url }}" alt="{{ $artifact->name }}" style="width:100%; height:100%; object-fit:cover;">
                    @else
                        <div style="width:100%; height:100%; min-height:90px; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#1e1428,#2d1f3d);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgba(248,245,239,.3)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>
                        </div>
                    @endif
                </div>

                {{-- Details --}}
                <div style="flex:1; padding:var(--space-4);">
                    <div class="flex" style="gap:var(--space-2); margin-bottom:var(--space-2); align-items:center; flex-wrap:wrap;">
                        <code style="font-size:.78rem; background:var(--color-surface-2); padding:2px 6px; border-radius:3px;">{{ $artifact->artifact_code }}</code>
                        <span style="font-size:.78rem; color:var(--color-muted);">{{ $ownerType }}: <strong>{{ $owner }}</strong></span>
                        <span style="font-size:.78rem; color:var(--color-muted);">Submitted {{ $artifact->updated_at->diffForHumans() }}</span>
                    </div>
                    <h2 style="font-size:1rem; margin:0 0 var(--space-1);">
                        <a href="{{ route('artifacts.show', $artifact) }}" target="_blank" style="color:var(--color-heading);">{{ $artifact->name }}</a>
                    </h2>
                    @if($artifact->short_description)
                        <p style="font-size:.875rem; color:var(--color-muted);">{{ Str::limit($artifact->short_description, 120) }}</p>
                    @endif
                    <div style="display:flex; gap:var(--space-4); font-size:.8rem; color:var(--color-muted); margin-top:var(--space-2); flex-wrap:wrap;">
                        @if($artifact->civilization) <span>{{ $artifact->civilization }}</span> @endif
                        @if($artifact->era) <span>{{ $artifact->era }}</span> @endif
                        @if($artifact->country_of_origin) <span>📍 {{ $artifact->country_of_origin }}</span> @endif
                    </div>
                </div>

                {{-- Action panel --}}
                <div style="padding:var(--space-4); border-left:1px solid var(--color-border); display:flex; flex-direction:column; gap:var(--space-3); min-width:220px; justify-content:center;">

                    {{-- Verify --}}
                    <form method="POST" action="{{ route('admin.artifact-verifications.verify', $artifact) }}">
                        @csrf
                        <div class="av-form-group" style="margin-bottom:var(--space-2);">
                            <input type="text" name="note" class="av-input" style="font-size:.82rem;"
                                placeholder="Optional approval note…">
                        </div>
                        <button class="av-btn av-btn--primary" style="width:100%; font-size:.85rem;">
                            ✅ Verify
                        </button>
                    </form>

                    {{-- Reject --}}
                    <form method="POST" action="{{ route('admin.artifact-verifications.reject', $artifact) }}">
                        @csrf
                        <div class="av-form-group" style="margin-bottom:var(--space-2);">
                            <textarea name="note" class="av-input" style="font-size:.82rem;" rows="2"
                                placeholder="Rejection reason (required, min 10 chars)…" required minlength="10"></textarea>
                        </div>
                        <button class="av-btn av-btn--outline" style="width:100%; font-size:.85rem; color:var(--color-danger); border-color:var(--color-danger);">
                            ✕ Reject
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <div style="margin-top:var(--space-8);">{{ $artifacts->links() }}</div>
    @endif

</div>
</div>
@endsection
