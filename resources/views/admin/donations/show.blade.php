@extends('layouts.app')

@section('title', "Review Donation — {$donation->artifact?->name} — Admin — Artevo")

@section('content')

<section class="av-section av-section--white" style="padding-top: var(--space-10);">
    <div class="container" style="max-width: 900px;">
        <p><a href="{{ route('admin.donations.index') }}" style="color: var(--brass-700); font-weight: 600;">← Back to donations</a></p>

        @if(session('success'))
            <x-alert type="success" class="mt-4">{{ session('success') }}</x-alert>
        @endif
        @if(session('error'))
            <x-alert type="error" class="mt-4">{{ session('error') }}</x-alert>
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

        {{-- ── Page header ─────────────────────────────────────────────────── --}}
        <div class="flex" style="align-items:center; gap:var(--space-4); margin-top:var(--space-6); flex-wrap:wrap;">
            <div>
                <x-tag>Admin · Donation Review</x-tag>
                <h1 style="margin-top:var(--space-2); font-size:clamp(1.4rem,4vw,2rem);">
                    {{ $donation->artifact?->name ?? '(Deleted Artifact)' }}
                </h1>
                <p style="color:var(--color-muted); margin:0; font-size:.85rem;">
                    Submitted {{ $donation->created_at->format('F j, Y') }} by
                    <strong>{{ $donation->donor?->name }}</strong>
                    · To: <strong>{{ $donation->museum?->name }}</strong>
                </p>
            </div>
            <span style="display:inline-flex; padding:6px 14px; border-radius:999px; font-size:.82rem; font-weight:700; background:{{ $c['bg'] }}; color:{{ $c['color'] }}; border:1px solid {{ $c['border'] }}; margin-left:auto;">
                {{ $donation->statusLabel() }}
            </span>
        </div>

        <div class="grid grid-2" style="gap:var(--space-6); margin-top:var(--space-8); align-items:start;">

            {{-- ── Left: artifact + donor + museum info ─────────────────── --}}
            <div>
                {{-- Artifact --}}
                <x-card>
                    <span class="av-card__eyebrow">Artifact Being Donated</span>
                    @if($donation->artifact)
                        <div class="flex" style="gap:var(--space-4); align-items:flex-start; margin-top:var(--space-3);">
                            @if($donation->artifact->primaryImage())
                                <img src="{{ $donation->artifact->primaryImage()->url() }}"
                                     alt="{{ $donation->artifact->name }}"
                                     style="width:100px; height:100px; object-fit:cover; border-radius:var(--radius-sm); flex-shrink:0;">
                            @else
                                <div style="width:100px; height:100px; background:var(--color-surface-2); border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; font-size:2.5rem;">🏺</div>
                            @endif
                            <div>
                                <h3 style="margin:0; font-size:1rem;">{{ $donation->artifact->name }}</h3>
                                <p style="font-size:.78rem; color:var(--color-muted); margin:4px 0 0; font-family:var(--font-mono);">{{ $donation->artifact->artifact_code }}</p>
                                @if($donation->artifact->category)
                                    <p style="font-size:.75rem; color:var(--color-gold); font-weight:600; margin:4px 0 0; text-transform:uppercase; letter-spacing:.04em;">{{ $donation->artifact->category->name }}</p>
                                @endif
                                @if($donation->artifact->museum)
                                    <p style="font-size:.8rem; color:var(--color-muted); margin:6px 0 0;">Currently in: <strong>{{ $donation->artifact->museum->name }}</strong></p>
                                @endif
                                <a href="{{ route('artifacts.show', $donation->artifact) }}"
                                   target="_blank"
                                   style="font-size:.8rem; color:var(--brass-700); font-weight:600; display:inline-block; margin-top:var(--space-2);">View artifact →</a>
                            </div>
                        </div>
                    @else
                        <p style="color:var(--color-danger); margin-top:var(--space-3);">The artifact has been deleted.</p>
                    @endif
                </x-card>

                {{-- Donor --}}
                <x-card class="mt-4">
                    <span class="av-card__eyebrow">Donor</span>
                    <div style="margin-top:var(--space-3);">
                        <p style="font-weight:700; margin:0; font-size:.95rem;">{{ $donation->donor?->name }}</p>
                        <p style="font-size:.82rem; color:var(--color-muted); margin:4px 0 0;">{{ $donation->donor?->email }}</p>
                        <p style="font-size:.78rem; color:var(--color-muted); margin:4px 0 0; text-transform:capitalize;">Role: {{ $donation->donor?->role }}</p>
                    </div>
                </x-card>

                {{-- Target Museum --}}
                <x-card class="mt-4">
                    <span class="av-card__eyebrow">Recipient Museum</span>
                    <div style="margin-top:var(--space-3);">
                        <p style="font-weight:700; margin:0; font-size:.95rem;">{{ $donation->museum?->name }}</p>
                        @if($donation->museum?->city)
                            <p style="font-size:.82rem; color:var(--color-muted); margin:4px 0 0;">{{ $donation->museum->city }}{{ $donation->museum->country ? ', '.$donation->museum->country : '' }}</p>
                        @endif
                        @if($donation->museum?->curator)
                            <p style="font-size:.8rem; color:var(--color-muted); margin:6px 0 0;">Curator: <strong>{{ $donation->museum->curator->name }}</strong></p>
                        @endif
                        <a href="{{ route('museums.show', $donation->museum) }}"
                           target="_blank"
                           style="font-size:.8rem; color:var(--brass-700); font-weight:600; display:inline-block; margin-top:var(--space-2);">View museum →</a>
                    </div>
                </x-card>

                {{-- Donor message --}}
                @if($donation->message)
                    <x-card class="mt-4">
                        <span class="av-card__eyebrow">Donor Message</span>
                        <p style="font-size:.88rem; line-height:1.7; color:var(--color-muted); margin:var(--space-3) 0 0; white-space:pre-line;">{{ $donation->message }}</p>
                    </x-card>
                @endif

                {{-- Certificate (if transferred) --}}
                @if($donation->isTransferred() && $donation->certificate_number)
                    <x-card class="mt-4" style="border-color:rgba(16,185,129,.3); background:rgba(16,185,129,.05);">
                        <span class="av-card__eyebrow" style="color:#10b981;">Certificate Issued</span>
                        <p style="font-family:var(--font-mono); font-size:1.2rem; font-weight:700; color:#10b981; margin:var(--space-2) 0;">{{ $donation->certificate_number }}</p>
                        <p style="font-size:.78rem; color:var(--color-muted); margin:0;">Transferred {{ $donation->transferred_at?->format('F j, Y') }}</p>
                    </x-card>
                @endif
            </div>

            {{-- ── Right: actions + history ──────────────────────────────── --}}
            <div>

                {{-- ── Review action (pending only) ─────────────────────── --}}
                @if($donation->isPending())
                    <x-card>
                        <span class="av-card__eyebrow">Review Decision</span>
                        <p style="font-size:.85rem; color:var(--color-muted); margin:var(--space-3) 0 var(--space-4);">
                            Choose whether to approve or reject this donation request.
                            The donor will be notified of your decision immediately.
                        </p>

                        <form method="POST" action="{{ route('admin.donations.review', $donation) }}" id="review-form">
                            @csrf

                            {{-- Provenance note (shown for approve) --}}
                            <div id="approve-fields" style="margin-bottom:var(--space-4);">
                                <label for="provenance_note" style="display:block; font-weight:600; font-size:.85rem; margin-bottom:var(--space-2);">
                                    Provenance note <span style="color:var(--color-muted); font-weight:400;">(optional — appears in the artifact's history)</span>
                                </label>
                                <textarea name="provenance_note" id="provenance_note" rows="3"
                                          class="av-input" style="width:100%; resize:vertical;"
                                          placeholder="e.g. Donated by John Smith in recognition of the museum's conservation work.">{{ old('provenance_note') }}</textarea>
                            </div>

                            {{-- Rejection reason (hidden by default) --}}
                            <div id="reject-fields" style="display:none; margin-bottom:var(--space-4);">
                                <label for="rejection_reason" style="display:block; font-weight:600; font-size:.85rem; margin-bottom:var(--space-2);">
                                    Rejection reason <span style="color:var(--color-danger);">*</span>
                                </label>
                                <textarea name="rejection_reason" id="rejection_reason" rows="3"
                                          class="av-input" style="width:100%; resize:vertical;"
                                          placeholder="Explain why this donation cannot be accepted…">{{ old('rejection_reason') }}</textarea>
                                @error('rejection_reason')
                                    <p style="font-size:.8rem; color:var(--color-danger); margin-top:4px;">{{ $message }}</p>
                                @enderror
                            </div>

                            <input type="hidden" name="action" id="action-field" value="approve">

                            <div class="flex" style="gap:var(--space-3); flex-wrap:wrap;">
                                <button type="button" id="btn-approve"
                                        class="av-btn av-btn--primary"
                                        style="background:rgba(16,185,129,.9); border-color:rgba(16,185,129,.9);"
                                        onclick="setAction('approve')">
                                    ✓ Approve Donation
                                </button>
                                <button type="button" id="btn-reject"
                                        class="av-btn av-btn--outline"
                                        style="color:var(--color-danger); border-color:rgba(239,68,68,.4);"
                                        onclick="setAction('reject')">
                                    ✗ Reject Donation
                                </button>
                            </div>
                        </form>
                    </x-card>
                @endif

                {{-- ── Transfer action (approved only) ──────────────────── --}}
                @if($donation->isApproved())
                    <x-card style="border-color:rgba(99,102,241,.3); background:rgba(99,102,241,.05);">
                        <span class="av-card__eyebrow" style="color:#818cf8;">Transfer Ownership</span>
                        <p style="font-size:.85rem; color:var(--color-muted); margin:var(--space-3) 0 var(--space-4); line-height:1.7;">
                            The donation has been approved. Click below to complete the ownership transfer.
                            This will:
                        </p>
                        <ul style="font-size:.85rem; color:var(--color-muted); padding-left:var(--space-5); margin:0 0 var(--space-4); line-height:1.9;">
                            <li>Re-assign the artifact to <strong>{{ $donation->museum?->name }}</strong></li>
                            <li>Create a provenance record (type: Donation)</li>
                            <li>Issue a donation certificate to the donor</li>
                            <li>Notify both parties</li>
                        </ul>
                        <form method="POST" action="{{ route('admin.donations.transfer', $donation) }}"
                              onsubmit="return confirm('Complete ownership transfer? This cannot be undone.');">
                            @csrf
                            <button type="submit" class="av-btn av-btn--primary">
                                Complete Ownership Transfer
                            </button>
                        </form>
                    </x-card>
                @endif

                {{-- ── Timeline ─────────────────────────────────────────── --}}
                <x-card class="{{ $donation->isPending() || $donation->isApproved() ? 'mt-4' : '' }}">
                    <span class="av-card__eyebrow">Status Timeline</span>
                    <ol style="list-style:none; margin:var(--space-4) 0 0; padding:0; display:flex; flex-direction:column; gap:0; border-left:2px solid var(--color-border); padding-left:var(--space-4);">
                        <li style="position:relative; padding-bottom:var(--space-4);">
                            <span style="position:absolute; left:calc(-1*var(--space-4) - 5px); top:4px; width:10px; height:10px; border-radius:50%; background:var(--brass-700); border:2px solid var(--color-surface);"></span>
                            <p style="font-weight:600; margin:0; font-size:.85rem;">Submitted</p>
                            <p style="font-size:.75rem; color:var(--color-muted); margin:2px 0 0;">{{ $donation->created_at->format('M j, Y g:i A') }}</p>
                        </li>
                        @if($donation->reviewed_by)
                            <li style="position:relative; {{ $donation->isTransferred() ? 'padding-bottom:var(--space-4);' : '' }}">
                                <span style="position:absolute; left:calc(-1*var(--space-4) - 5px); top:4px; width:10px; height:10px; border-radius:50%; background:{{ $donation->isRejected() ? '#ef4444' : '#818cf8' }}; border:2px solid var(--color-surface);"></span>
                                <p style="font-weight:600; margin:0; font-size:.85rem;">{{ $donation->isRejected() ? 'Rejected' : 'Approved' }}</p>
                                <p style="font-size:.75rem; color:var(--color-muted); margin:2px 0 0;">by {{ $donation->reviewer?->name }}</p>
                                @if($donation->isRejected() && $donation->rejection_reason)
                                    <p style="font-size:.8rem; color:var(--color-danger); margin:var(--space-2) 0 0; padding:var(--space-2) var(--space-3); background:rgba(239,68,68,.08); border-radius:var(--radius-sm);">
                                        {{ $donation->rejection_reason }}
                                    </p>
                                @endif
                            </li>
                        @endif
                        @if($donation->isTransferred())
                            <li style="position:relative;">
                                <span style="position:absolute; left:calc(-1*var(--space-4) - 5px); top:4px; width:10px; height:10px; border-radius:50%; background:#10b981; border:2px solid var(--color-surface);"></span>
                                <p style="font-weight:600; margin:0; font-size:.85rem;">Transferred</p>
                                <p style="font-size:.75rem; color:var(--color-muted); margin:2px 0 0;">{{ $donation->transferred_at?->format('M j, Y g:i A') }}</p>
                            </li>
                        @endif
                    </ol>
                </x-card>

            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function setAction(action) {
    document.getElementById('action-field').value = action;
    const approveFields = document.getElementById('approve-fields');
    const rejectFields  = document.getElementById('reject-fields');
    const rejReason     = document.getElementById('rejection_reason');

    if (action === 'reject') {
        approveFields.style.display = 'none';
        rejectFields.style.display  = 'block';
        rejReason.required = true;
    } else {
        approveFields.style.display = 'block';
        rejectFields.style.display  = 'none';
        rejReason.required = false;
    }

    const msg = action === 'approve'
        ? 'Approve this donation request? The donor will be notified.'
        : 'Reject this donation request? The donor will be notified.';

    if (confirm(msg)) {
        document.getElementById('review-form').submit();
    }
}
</script>
@endpush

@endsection
