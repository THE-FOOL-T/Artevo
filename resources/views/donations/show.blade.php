@extends('layouts.app')

@section('title', "Donation — {$donation->artifact?->name} — Artevo")
@section('meta_description', 'View the details and status of this artifact donation request.')

@section('content')

<section class="av-section av-section--white" style="padding-top: var(--space-10);">
    <div class="container" style="max-width: 800px;">
        <p><a href="{{ route('donations.index') }}" style="color: var(--brass-700); font-weight: 600;">← Back to my donations</a></p>

        @if(session('success'))
            <x-alert type="success" class="mt-4">{{ session('success') }}</x-alert>
        @endif

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

        {{-- ── Header ────────────────────────────────────────────────────── --}}
        <div class="flex" style="align-items:center; gap:var(--space-4); margin-top:var(--space-6); flex-wrap:wrap;">
            <div>
                <x-tag>Donation Request</x-tag>
                <h1 style="margin-top:var(--space-2); font-size:clamp(1.5rem,4vw,2rem);">
                    {{ $donation->artifact?->name ?? '(deleted artifact)' }}
                </h1>
                <p style="color:var(--color-muted); margin:0; font-size:.85rem;">
                    Submitted {{ $donation->created_at->format('F j, Y') }}
                    · To: <strong>{{ $donation->museum?->name }}</strong>
                </p>
            </div>
            <span style="display:inline-flex; align-items:center; padding:6px 14px; border-radius:999px; font-size:.82rem; font-weight:700; background:{{ $c['bg'] }}; color:{{ $c['color'] }}; border:1px solid {{ $c['border'] }}; margin-left:auto;">
                {{ $donation->statusLabel() }}
            </span>
        </div>

        <div class="grid grid-2" style="gap:var(--space-6); margin-top:var(--space-8); align-items:start;">

            {{-- ── Left column ─────────────────────────────────────────── --}}
            <div>
                {{-- Artifact card --}}
                <x-card>
                    <span class="av-card__eyebrow">Artifact</span>
                    <div class="flex" style="gap:var(--space-4); align-items:flex-start; margin-top:var(--space-3);">
                        @if($donation->artifact?->primaryImage())
                            <img src="{{ $donation->artifact->primaryImage()->url() }}"
                                 alt="{{ $donation->artifact->name }}"
                                 style="width:80px; height:80px; object-fit:cover; border-radius:var(--radius-sm); flex-shrink:0;">
                        @else
                            <div style="width:80px; height:80px; background:var(--color-surface-2); border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; font-size:2rem;">🏺</div>
                        @endif
                        <div>
                            <h3 style="margin:0; font-size:1rem;">{{ $donation->artifact?->name }}</h3>
                            <p style="font-size:.78rem; color:var(--color-muted); margin:4px 0;">{{ $donation->artifact?->artifact_code }}</p>
                            @if($donation->artifact)
                                <a href="{{ route('artifacts.show', $donation->artifact) }}"
                                   style="font-size:.8rem; color:var(--brass-700); font-weight:600;">View artifact →</a>
                            @endif
                        </div>
                    </div>
                </x-card>

                {{-- Museum card --}}
                <x-card class="mt-4">
                    <span class="av-card__eyebrow">Recipient Museum</span>
                    <h3 style="margin-top:var(--space-2); font-size:1rem;">{{ $donation->museum?->name }}</h3>
                    @if($donation->museum?->city)
                        <p style="font-size:.85rem; color:var(--color-muted); margin:0;">{{ $donation->museum->city }}{{ $donation->museum->country ? ', '.$donation->museum->country : '' }}</p>
                    @endif
                    @if($donation->museum)
                        <a href="{{ route('museums.show', $donation->museum) }}"
                           style="font-size:.8rem; color:var(--brass-700); font-weight:600; display:inline-block; margin-top:var(--space-2);">View museum →</a>
                    @endif
                </x-card>

                {{-- Certificate --}}
                @if($donation->isTransferred() && $donation->certificate_number)
                    <x-card class="mt-4" style="border-color: rgba(16,185,129,.3); background: rgba(16,185,129,.05);">
                        <span class="av-card__eyebrow" style="color:#10b981;">Donation Certificate</span>
                        <p style="font-size:1.2rem; font-weight:700; font-family:var(--font-mono); color:#10b981; margin:var(--space-2) 0;">
                            {{ $donation->certificate_number }}
                        </p>
                        <p style="font-size:.8rem; color:var(--color-muted); margin:0;">
                            Transferred on {{ $donation->transferred_at?->format('F j, Y') }}
                        </p>
                    </x-card>
                @endif
            </div>

            {{-- ── Right column ─────────────────────────────────────────── --}}
            <div>
                {{-- Status timeline --}}
                <x-card>
                    <span class="av-card__eyebrow">Status Timeline</span>
                    <ol style="list-style:none; margin:var(--space-4) 0 0; padding:0; display:flex; flex-direction:column; gap:0; border-left:2px solid var(--color-border); padding-left:var(--space-4);">

                        {{-- Submitted --}}
                        <li style="position:relative; padding-bottom:var(--space-4);">
                            <span style="position:absolute; left:calc(-1*var(--space-4) - 5px); top:4px; width:10px; height:10px; border-radius:50%; background:var(--brass-700); border:2px solid var(--color-surface);"></span>
                            <p style="font-weight:600; margin:0; font-size:.88rem;">Submitted</p>
                            <p style="font-size:.78rem; color:var(--color-muted); margin:2px 0 0;">{{ $donation->created_at->format('F j, Y \a\t g:i A') }}</p>
                            <p style="font-size:.8rem; color:var(--color-muted); margin:2px 0 0;">by {{ $donation->donor?->name }}</p>
                        </li>

                        {{-- Review --}}
                        @if($donation->reviewed_by)
                            <li style="position:relative; padding-bottom:var(--space-4);">
                                <span style="position:absolute; left:calc(-1*var(--space-4) - 5px); top:4px; width:10px; height:10px; border-radius:50%; background:{{ $donation->isRejected() ? '#ef4444' : '#818cf8' }}; border:2px solid var(--color-surface);"></span>
                                <p style="font-weight:600; margin:0; font-size:.88rem;">{{ $donation->isRejected() ? 'Rejected' : 'Approved' }}</p>
                                <p style="font-size:.78rem; color:var(--color-muted); margin:2px 0 0;">by {{ $donation->reviewer?->name }}</p>
                                @if($donation->isRejected() && $donation->rejection_reason)
                                    <p style="font-size:.82rem; color:var(--color-danger); margin:var(--space-2) 0 0; padding:var(--space-2) var(--space-3); background:rgba(239,68,68,.08); border-radius:var(--radius-sm);">
                                        {{ $donation->rejection_reason }}
                                    </p>
                                @endif
                            </li>
                        @endif

                        {{-- Transferred --}}
                        @if($donation->isTransferred())
                            <li style="position:relative;">
                                <span style="position:absolute; left:calc(-1*var(--space-4) - 5px); top:4px; width:10px; height:10px; border-radius:50%; background:#10b981; border:2px solid var(--color-surface);"></span>
                                <p style="font-weight:600; margin:0; font-size:.88rem;">Ownership Transferred</p>
                                <p style="font-size:.78rem; color:var(--color-muted); margin:2px 0 0;">
                                    {{ $donation->transferred_at?->format('F j, Y \a\t g:i A') }}
                                </p>
                            </li>
                        @endif
                    </ol>
                </x-card>

                {{-- Donor message --}}
                @if($donation->message)
                    <x-card class="mt-4">
                        <span class="av-card__eyebrow">Donor Message</span>
                        <p style="font-size:.9rem; line-height:1.7; color:var(--color-muted); margin:var(--space-3) 0 0; white-space:pre-line;">{{ $donation->message }}</p>
                    </x-card>
                @endif

                {{-- Cancel action --}}
                @can('cancel', $donation)
                    <div class="mt-4" style="padding:var(--space-4); background:rgba(239,68,68,.06); border:1px solid rgba(239,68,68,.2); border-radius:var(--radius-md);">
                        <p style="font-size:.85rem; color:var(--color-muted); margin:0 0 var(--space-3);">Changed your mind? Cancel this request while it's still pending.</p>
                        <form method="POST" action="{{ route('donations.destroy', $donation) }}"
                              onsubmit="return confirm('Are you sure you want to cancel this donation request?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="av-btn av-btn--outline" style="color:var(--color-danger); border-color:rgba(239,68,68,.4);">
                                Cancel Donation Request
                            </button>
                        </form>
                    </div>
                @endcan
            </div>

        </div>
    </div>
</section>

@endsection
