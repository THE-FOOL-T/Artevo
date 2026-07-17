@php use Illuminate\Support\Str; @endphp

@extends('layouts.app')

@section('title', 'Exhibitions — ' . $museum->name . ' — Artevo')

@section('content')
<div class="av-section">
    <div class="container">
        {{-- Breadcrumb --}}
        <nav aria-label="Breadcrumb" style="margin-bottom: var(--space-4); font-size:.875rem;">
            <a href="{{ route('curator.museums.index') }}" style="color:var(--color-muted);">My Museums</a>
            <span style="color:var(--color-muted); margin: 0 var(--space-2);">/</span>
            <a href="{{ route('curator.museums.show', $museum) }}" style="color:var(--color-muted);">{{ $museum->name }}</a>
            <span style="color:var(--color-muted); margin: 0 var(--space-2);">/</span>
            <span>Exhibitions</span>
        </nav>

        <div class="flex" style="align-items: center; justify-content: space-between; margin-bottom: var(--space-6); flex-wrap: wrap; gap: var(--space-3);">
            <div>
                <h1 class="av-page-title">Exhibitions</h1>
                <p style="color: var(--color-muted);">{{ $museum->name }}</p>
            </div>
            <a href="{{ route('curator.exhibitions.create', $museum) }}" class="av-btn av-btn--primary">
                + New Exhibition
            </a>
        </div>

        @if(session('success'))
            <div style="background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.3); border-radius:var(--radius-sm); padding:var(--space-3) var(--space-4); color:#10b981; margin-bottom:var(--space-4);">{{ session('success') }}</div>
        @endif

        @if($exhibitions->isEmpty())
            <div style="text-align:center; padding:var(--space-12) 0; color:var(--color-muted);">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin-bottom:var(--space-4); opacity:.4;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="m3 9 9-6 9 6"/></svg>
                <h3 style="font-size:1.1rem; margin-bottom:var(--space-2);">No exhibitions yet</h3>
                <p style="margin-bottom:var(--space-4);">Create your first exhibition to present a curated story with artifacts.</p>
                <a href="{{ route('curator.exhibitions.create', $museum) }}" class="av-btn av-btn--primary">Create Exhibition</a>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap: var(--space-4);">
                @foreach($exhibitions as $exhibition)
                <div class="av-card" style="display:flex; gap: var(--space-4); align-items:stretch; overflow:hidden;">
                    {{-- Cover thumbnail --}}
                    <div style="width: 120px; flex-shrink:0; background:var(--color-surface-2); overflow:hidden;">
                        @if($exhibition->coverImageUrl())
                            <img src="{{ $exhibition->coverImageUrl() }}" alt="" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <div style="width:100%; height:100%; min-height:90px; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#1e1428,#2d1f3d);">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(248,245,239,.3)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="m3 9 9-6 9 6"/></svg>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div style="flex:1; padding: var(--space-4); display:flex; flex-direction:column; gap: var(--space-1);">
                        <div class="flex" style="gap: var(--space-2); align-items:center; flex-wrap:wrap; margin-bottom: var(--space-1);">
                            <span class="av-tag av-tag--{{ $exhibition->isDraft() ? 'warning' : ($exhibition->isPublished() ? 'success' : 'secondary') }}">
                                {{ $exhibition->statusLabel() }}
                            </span>
                            @if($exhibition->is_featured)
                                <span class="av-tag av-tag--gold">Featured</span>
                            @endif
                        </div>
                        <h2 style="font-size:1.05rem; margin:0;">{{ $exhibition->name }}</h2>
                        @if($exhibition->tagline)
                            <p style="font-size:.875rem; color:var(--color-muted);">{{ Str::limit($exhibition->tagline, 80) }}</p>
                        @endif
                        <div class="flex" style="gap: var(--space-4); font-size:.8rem; color:var(--color-muted); flex-wrap:wrap; margin-top:auto; padding-top: var(--space-2);">
                            <span>{{ $exhibition->sections_count }} {{ Str::plural('section', $exhibition->sections_count) }}</span>
                            @if($exhibition->starts_at)
                                <span>📅 {{ $exhibition->starts_at->format('M j, Y') }}</span>
                            @endif
                            <span>{{ $exhibition->admissionLabel() }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div style="padding: var(--space-4); display:flex; flex-direction:column; gap: var(--space-2); align-items:flex-end; justify-content:center; border-left: 1px solid var(--color-border);">
                        <a href="{{ route('curator.exhibitions.edit', [$museum, $exhibition]) }}" class="av-btn av-btn--outline" style="white-space:nowrap; font-size:.85rem;">Edit</a>

                        @if($exhibition->isDraft())
                            <form method="POST" action="{{ route('curator.exhibitions.publish', [$museum, $exhibition]) }}">
                                @csrf @method('PATCH')
                                <button class="av-btn av-btn--primary" style="font-size:.85rem;">Publish</button>
                            </form>
                        @elseif($exhibition->isPublished())
                            <a href="{{ route('exhibitions.show', $exhibition) }}" class="av-btn av-btn--ghost" style="font-size:.85rem;" target="_blank">View →</a>
                            <form method="POST" action="{{ route('curator.exhibitions.archive', [$museum, $exhibition]) }}">
                                @csrf @method('PATCH')
                                <button class="av-btn av-btn--outline" style="font-size:.85rem; color:var(--color-muted);">Archive</button>
                            </form>
                        @else
                            <span style="font-size:.8rem; color:var(--color-muted);">Archived</span>
                        @endif

                        <form method="POST" action="{{ route('curator.exhibitions.destroy', [$museum, $exhibition]) }}"
                            onsubmit="return confirm('Delete this exhibition? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button class="av-btn av-btn--ghost" style="font-size:.8rem; color:var(--color-danger);">Delete</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            <div style="margin-top: var(--space-8);">{{ $exhibitions->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.av-tag--warning  { background: rgba(245,158,11,.12); color:#d97706; border:1px solid rgba(245,158,11,.25); }
.av-tag--success  { background: rgba(16,185,129,.12); color:#10b981; border:1px solid rgba(16,185,129,.25); }
.av-tag--secondary{ background: var(--color-surface-2); color:var(--color-muted); border:1px solid var(--color-border); }
</style>
@endpush
