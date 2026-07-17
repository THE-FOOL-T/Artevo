@extends('layouts.app')

@section('title', 'My Donation Requests — Artevo')
@section('meta_description', 'Manage your artifact donation requests on Artevo.')

@section('content')

{{-- ─── Hero ───────────────────────────────────────────────────────────────── --}}
<div style="background: linear-gradient(135deg, #0c0a10 0%, #16102a 60%, #0e0c14 100%); padding: var(--space-14) 0 var(--space-10); position: relative; overflow: hidden;">
    <div style="position:absolute; top:-30%; left:50%; transform:translateX(-50%); width:600px; height:600px; background:radial-gradient(circle, rgba(212,175,55,.06) 0%, transparent 70%); pointer-events:none;"></div>
    <div class="container" style="position:relative;">
        <p class="av-overline" style="color: var(--color-gold); margin:0 0 var(--space-3);">Phase 14 · Donations</p>
        <h1 style="font-size: clamp(1.8rem, 4vw, 3rem); color: var(--color-parchment); margin-bottom: var(--space-4);">
            My <em style="color: var(--color-gold);">Donation Requests</em>
        </h1>
        <p style="color: rgba(248,245,239,.7); max-width: 500px; line-height: 1.7;">
            Track all artifact donations you have submitted to partner museums.
        </p>
        <a href="{{ route('donations.create') }}" class="av-btn av-btn--primary" style="margin-top: var(--space-5);">
            + New Donation Request
        </a>
    </div>
</div>

{{-- ─── Table ──────────────────────────────────────────────────────────────── --}}
<div class="container" style="padding-top: var(--space-10); padding-bottom: var(--space-16);">

    @if(session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif

    @if($donations->isEmpty())
        <div style="text-align:center; padding: var(--space-16) 0; color: var(--color-muted);">
            <div style="font-size:3rem; margin-bottom: var(--space-4);">🎁</div>
            <h3 style="font-size:1.1rem; margin-bottom:var(--space-2);">No donation requests yet</h3>
            <p style="max-width:380px; margin: 0 auto var(--space-6);">Donate an artifact from your collection to a partner museum to preserve cultural heritage.</p>
            <a href="{{ route('donations.create') }}" class="av-btn av-btn--primary">Make a donation request</a>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:.9rem;">
                <thead>
                    <tr style="border-bottom:2px solid var(--color-border); text-align:left;">
                        <th style="padding: var(--space-3) var(--space-4); color:var(--color-muted); font-weight:600; font-size:.78rem; text-transform:uppercase; letter-spacing:.05em;">Artifact</th>
                        <th style="padding: var(--space-3) var(--space-4); color:var(--color-muted); font-weight:600; font-size:.78rem; text-transform:uppercase; letter-spacing:.05em;">Recipient Museum</th>
                        <th style="padding: var(--space-3) var(--space-4); color:var(--color-muted); font-weight:600; font-size:.78rem; text-transform:uppercase; letter-spacing:.05em;">Status</th>
                        <th style="padding: var(--space-3) var(--space-4); color:var(--color-muted); font-weight:600; font-size:.78rem; text-transform:uppercase; letter-spacing:.05em;">Submitted</th>
                        <th style="padding: var(--space-3) var(--space-4); color:var(--color-muted); font-weight:600; font-size:.78rem; text-transform:uppercase; letter-spacing:.05em;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donations as $donation)
                    @php
                        $colors = [
                            'warning' => ['bg'=>'rgba(245,158,11,.12)', 'color'=>'#d97706', 'border'=>'rgba(245,158,11,.3)'],
                            'info'    => ['bg'=>'rgba(99,102,241,.12)', 'color'=>'#818cf8', 'border'=>'rgba(99,102,241,.3)'],
                            'success' => ['bg'=>'rgba(16,185,129,.12)', 'color'=>'#10b981', 'border'=>'rgba(16,185,129,.3)'],
                            'danger'  => ['bg'=>'rgba(239,68,68,.1)',   'color'=>'#ef4444', 'border'=>'rgba(239,68,68,.25)'],
                            'muted'   => ['bg'=>'var(--color-surface-2)','color'=>'var(--color-muted)','border'=>'var(--color-border)'],
                        ];
                        $c = $colors[$donation->statusColor()] ?? $colors['muted'];
                    @endphp
                    <tr style="border-bottom:1px solid var(--color-border);">
                        <td style="padding: var(--space-4);">
                            <div class="flex" style="align-items:center; gap:var(--space-3);">
                                @if($donation->artifact?->primaryImage())
                                    <img src="{{ $donation->artifact->primaryImage()->url() }}"
                                         alt="{{ $donation->artifact->name }}"
                                         style="width:44px; height:44px; object-fit:cover; border-radius:var(--radius-sm); flex-shrink:0;">
                                @else
                                    <div style="width:44px; height:44px; background:var(--color-surface-2); border-radius:var(--radius-sm); flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">🏺</div>
                                @endif
                                <div>
                                    <p style="font-weight:600; margin:0; font-size:.9rem;">{{ $donation->artifact?->name ?? '(deleted)' }}</p>
                                    <p style="font-size:.75rem; color:var(--color-muted); margin:0;">{{ $donation->artifact?->artifact_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td style="padding: var(--space-4);">
                            <p style="font-weight:600; margin:0;">{{ $donation->museum?->name }}</p>
                            <p style="font-size:.78rem; color:var(--color-muted); margin:0;">{{ $donation->museum?->city }}, {{ $donation->museum?->country }}</p>
                        </td>
                        <td style="padding: var(--space-4);">
                            <span style="display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font-size:.75rem; font-weight:600; background:{{ $c['bg'] }}; color:{{ $c['color'] }}; border:1px solid {{ $c['border'] }};">
                                {{ $donation->statusLabel() }}
                            </span>
                            @if($donation->isTransferred() && $donation->certificate_number)
                                <p style="font-size:.7rem; color:var(--color-muted); margin:4px 0 0;">Cert: {{ $donation->certificate_number }}</p>
                            @endif
                        </td>
                        <td style="padding: var(--space-4); color:var(--color-muted); font-size:.85rem;">
                            {{ $donation->created_at->format('M j, Y') }}
                        </td>
                        <td style="padding: var(--space-4); white-space:nowrap;">
                            <a href="{{ route('donations.show', $donation) }}"
                               style="color:var(--brass-700); font-weight:600; font-size:.82rem;">View →</a>
                            @if($donation->isCancellable())
                                <form method="POST" action="{{ route('donations.destroy', $donation) }}" style="display:inline; margin-left:var(--space-3);"
                                      onsubmit="return confirm('Cancel this donation request?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background:none; border:none; cursor:pointer; color:var(--color-danger); font-size:.82rem; font-weight:600; padding:0;">Cancel</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: var(--space-8);">
            {{ $donations->links() }}
        </div>
    @endif
</div>

@endsection
