@php use Illuminate\Support\Str; @endphp

@extends('layouts.app')

@section('title', 'QR Code Analytics — Admin — Artevo')
@section('meta_description', 'Monitor QR code scan activity for all artifacts on Artevo.')

@section('content')

<section class="av-section av-section--white" style="padding-top: var(--space-10);">
    <div class="container">
        <x-tag>Administrator · Phase 15</x-tag>
        <h1 style="margin-top: var(--space-4);">QR Code Analytics</h1>
        <p style="color:var(--color-muted); max-width:540px;">
            Track QR scan activity across all artifacts. Regenerating a QR invalidates previously printed labels.
        </p>

        @if(session('success'))
            <x-alert type="success" class="mt-4">{{ session('success') }}</x-alert>
        @endif

        {{-- ── Summary stats ───────────────────────────────────────────────── --}}
        <div class="grid grid-4" style="gap:var(--space-4); margin-top:var(--space-8);">
            <x-card>
                <span class="av-card__eyebrow">Total scans</span>
                <div style="font-size:2.2rem; font-weight:800; color:var(--brass-700); margin-top:var(--space-1);">{{ number_format($totalScans) }}</div>
            </x-card>
            <x-card>
                <span class="av-card__eyebrow">QR-enabled artifacts</span>
                <div style="font-size:2.2rem; font-weight:800; color:var(--brass-700); margin-top:var(--space-1);">{{ number_format($totalWithCode) }}</div>
            </x-card>
            <x-card>
                <span class="av-card__eyebrow">Total artifacts</span>
                <div style="font-size:2.2rem; font-weight:800; color:var(--color-muted); margin-top:var(--space-1);">{{ number_format($totalArtifacts) }}</div>
            </x-card>
            <x-card>
                <span class="av-card__eyebrow">Without QR</span>
                <div style="font-size:2.2rem; font-weight:800; color:var(--color-muted); margin-top:var(--space-1);">{{ number_format($totalArtifacts - $totalWithCode) }}</div>
            </x-card>
        </div>

        {{-- ── Scan leaderboard ─────────────────────────────────────────────── --}}
        <div style="margin-top:var(--space-10);">
            <h2 style="font-size:1.1rem; margin-bottom:var(--space-4);">Scan Leaderboard</h2>

            @if($qrCodes->isEmpty())
                <div style="text-align:center; padding:var(--space-12) 0; color:var(--color-muted);">
                    <div style="font-size:3rem; margin-bottom:var(--space-3);">📱</div>
                    <p>No QR codes have been generated yet. Codes are created automatically when an artifact page is first visited by the owner.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:.88rem;">
                        <thead>
                            <tr style="border-bottom:2px solid var(--color-border); text-align:left;">
                                <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Rank</th>
                                <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Artifact</th>
                                <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Scans</th>
                                <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Generation</th>
                                <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Last scanned</th>
                                <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Token</th>
                                <th style="padding:var(--space-3) var(--space-4);"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($qrCodes as $qr)
                            @php
                                $rank = $loop->iteration + (($qrCodes->currentPage() - 1) * $qrCodes->perPage());
                            @endphp
                            <tr style="border-bottom:1px solid var(--color-border);">
                                <td style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-weight:700; font-size:.9rem;">
                                    #{{ $rank }}
                                </td>
                                <td style="padding:var(--space-3) var(--space-4);">
                                    <div class="flex" style="align-items:center; gap:var(--space-3);">
                                        @if($qr->artifact?->primaryImage())
                                            <img src="{{ $qr->artifact->primaryImage()->url() }}"
                                                 alt="{{ $qr->artifact->name }}"
                                                 style="width:38px; height:38px; object-fit:cover; border-radius:var(--radius-sm); flex-shrink:0;">
                                        @else
                                            <div style="width:38px; height:38px; background:var(--color-surface-2); border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0;">🏺</div>
                                        @endif
                                        <div>
                                            <a href="{{ route('artifacts.show', $qr->artifact) }}"
                                               style="font-weight:600; color:var(--color-heading); text-decoration:none; font-size:.88rem;">
                                                {{ $qr->artifact?->name ?? '(deleted)' }}
                                            </a>
                                            <p style="font-size:.72rem; color:var(--color-muted); margin:0; font-family:var(--font-mono);">{{ $qr->artifact?->artifact_code }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:var(--space-3) var(--space-4);">
                                    <span style="font-size:1.1rem; font-weight:800; color:{{ $qr->scan_count > 0 ? 'var(--brass-700)' : 'var(--color-muted)' }};">
                                        {{ number_format($qr->scan_count) }}
                                    </span>
                                </td>
                                <td style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.85rem;">
                                    Gen {{ $qr->generation }}
                                </td>
                                <td style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.82rem; white-space:nowrap;">
                                    {{ $qr->last_scanned_at?->diffForHumans() ?? '—' }}
                                </td>
                                <td style="padding:var(--space-3) var(--space-4);">
                                    <code style="font-size:.68rem; color:var(--color-muted); font-family:var(--font-mono);">
                                        {{ Str::limit($qr->token, 16) }}…
                                    </code>
                                </td>
                                <td style="padding:var(--space-3) var(--space-4); white-space:nowrap;">
                                    @if($qr->artifact)
                                        <form method="POST"
                                              action="{{ route('admin.artifacts.qr.regenerate', $qr->artifact) }}"
                                              onsubmit="return confirm('Regenerate QR for {{ addslashes($qr->artifact->name) }}? Old printed labels will stop working.');">
                                            @csrf
                                            <button type="submit"
                                                    style="background:none; border:1px solid var(--color-border); border-radius:var(--radius-sm); cursor:pointer; font-size:.75rem; color:var(--color-danger); padding:3px 10px; font-weight:600;">
                                                ↺ Regenerate
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:var(--space-8);">
                    {{ $qrCodes->links() }}
                </div>
            @endif
        </div>
    </div>
</section>

@endsection
