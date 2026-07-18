@extends('layouts.app')

@section('title', 'Certificates — Admin — Artevo')
@section('meta_description', 'Manage and revoke Artevo Certificates of Authenticity.')

@section('content')

<section class="av-section av-section--white" style="padding-top:var(--space-10); padding-bottom:var(--space-16);">
    <div class="container">
        <x-tag>Administrator · Phase 16</x-tag>
        <h1 style="margin-top:var(--space-4);">Certificate Management</h1>

        @if(session('success'))
            <x-alert type="success" class="mt-4">{{ session('success') }}</x-alert>
        @endif

        {{-- Stats --}}
        <div class="grid grid-3" style="gap:var(--space-4); margin-top:var(--space-8);">
            <x-card>
                <span class="av-card__eyebrow">Total issued</span>
                <div style="font-size:2.2rem; font-weight:800; color:var(--brass-700); margin-top:var(--space-1);">{{ number_format($totalIssued) }}</div>
            </x-card>
            <x-card>
                <span class="av-card__eyebrow">Valid</span>
                <div style="font-size:2.2rem; font-weight:800; color:#10b981; margin-top:var(--space-1);">{{ number_format($totalValid) }}</div>
            </x-card>
            <x-card>
                <span class="av-card__eyebrow">Revoked</span>
                <div style="font-size:2.2rem; font-weight:800; color:#dc2626; margin-top:var(--space-1);">{{ number_format($totalRevoked) }}</div>
            </x-card>
        </div>

        {{-- Filters --}}
        <div class="flex" style="gap:var(--space-3); margin-top:var(--space-8); flex-wrap:wrap; align-items:center;">
            <a href="{{ route('admin.certificates.index') }}"
               style="padding:5px 14px; border-radius:var(--radius-sm); font-size:.82rem; font-weight:600; text-decoration:none;
                      {{ ! request()->hasAny(['type','revoked']) ? 'background:rgba(212,175,55,.15); color:var(--brass-700); border:1px solid rgba(212,175,55,.3);' : 'color:var(--color-muted); border:1px solid var(--color-border);' }}">
                All
            </a>
            <a href="{{ request()->fullUrlWithQuery(['type' => 'verification', 'revoked' => '']) }}"
               style="padding:5px 14px; border-radius:var(--radius-sm); font-size:.82rem; font-weight:600; text-decoration:none;
                      {{ request('type') === 'verification' ? 'background:rgba(212,175,55,.15); color:var(--brass-700); border:1px solid rgba(212,175,55,.3);' : 'color:var(--color-muted); border:1px solid var(--color-border);' }}">
                Verification
            </a>
            <a href="{{ request()->fullUrlWithQuery(['type' => 'donation_transfer', 'revoked' => '']) }}"
               style="padding:5px 14px; border-radius:var(--radius-sm); font-size:.82rem; font-weight:600; text-decoration:none;
                      {{ request('type') === 'donation_transfer' ? 'background:rgba(212,175,55,.15); color:var(--brass-700); border:1px solid rgba(212,175,55,.3);' : 'color:var(--color-muted); border:1px solid var(--color-border);' }}">
                Transfers
            </a>
            <a href="{{ request()->fullUrlWithQuery(['revoked' => '1', 'type' => '']) }}"
               style="padding:5px 14px; border-radius:var(--radius-sm); font-size:.82rem; font-weight:600; text-decoration:none;
                      {{ request('revoked') ? 'background:rgba(220,38,38,.1); color:#dc2626; border:1px solid rgba(220,38,38,.3);' : 'color:var(--color-muted); border:1px solid var(--color-border);' }}">
                Revoked only
            </a>
        </div>

        {{-- Table --}}
        @if($certificates->isEmpty())
            <div style="text-align:center; padding:var(--space-12) 0; color:var(--color-muted);">
                <div style="font-size:2.5rem; margin-bottom:var(--space-3);">🏛</div>
                <p>No certificates found matching these filters.</p>
            </div>
        @else
            <div style="overflow-x:auto; margin-top:var(--space-6);">
                <table style="width:100%; border-collapse:collapse; font-size:.86rem;">
                    <thead>
                        <tr style="border-bottom:2px solid var(--color-border); text-align:left;">
                            <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Artifact</th>
                            <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Serial</th>
                            <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Type</th>
                            <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Recipient</th>
                            <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Issued</th>
                            <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Status</th>
                            <th style="padding:var(--space-3) var(--space-4);"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($certificates as $cert)
                        <tr style="border-bottom:1px solid var(--color-border); {{ $cert->isRevoked() ? 'opacity:.6;' : '' }}">
                            <td style="padding:var(--space-3) var(--space-4);">
                                <div class="flex" style="align-items:center; gap:var(--space-3);">
                                    @if($cert->artifact?->primaryImage())
                                        <img src="{{ $cert->artifact->primaryImage()->url() }}"
                                             style="width:36px; height:36px; object-fit:cover; border-radius:var(--radius-sm); flex-shrink:0;"
                                             alt="">
                                    @else
                                        <div style="width:36px; height:36px; background:var(--color-surface-2); border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; font-size:.9rem; flex-shrink:0;">🏺</div>
                                    @endif
                                    <div>
                                        <a href="{{ route('artifacts.show', $cert->artifact) }}"
                                           style="font-weight:600; text-decoration:none; color:var(--color-heading); font-size:.85rem;">
                                            {{ $cert->artifact?->name ?? '—' }}
                                        </a>
                                        <p style="font-size:.68rem; color:var(--color-muted); margin:0; font-family:var(--font-mono);">{{ $cert->artifact?->artifact_code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:var(--space-3) var(--space-4);">
                                <a href="{{ $cert->verificationUrl() }}"
                                   style="font-family:var(--font-mono); font-size:.72rem; color:var(--brass-700); text-decoration:none;">
                                    {{ $cert->serial }}
                                </a>
                            </td>
                            <td style="padding:var(--space-3) var(--space-4); font-size:.8rem; color:var(--color-muted);">
                                {{ $cert->typeIcon() }} {{ $cert->typeLabel() }}
                            </td>
                            <td style="padding:var(--space-3) var(--space-4); font-size:.82rem;">
                                {{ $cert->recipient?->name ?? '—' }}
                            </td>
                            <td style="padding:var(--space-3) var(--space-4); font-size:.8rem; color:var(--color-muted); white-space:nowrap;">
                                {{ $cert->created_at->format('d M Y') }}
                            </td>
                            <td style="padding:var(--space-3) var(--space-4);">
                                <span style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:{{ $cert->isRevoked() ? '#dc2626' : '#10b981' }};">
                                    {{ $cert->isRevoked() ? 'Revoked' : 'Valid' }}
                                </span>
                            </td>
                            <td style="padding:var(--space-3) var(--space-4); text-align:right; white-space:nowrap;">
                                @if($cert->isValid())
                                    <form method="POST"
                                          action="{{ route('admin.certificates.revoke', $cert) }}"
                                          style="display:inline;"
                                          onsubmit="return handleRevoke(this, '{{ addslashes($cert->serial) }}');">
                                        @csrf
                                        <input type="hidden" name="reason" id="reason-{{ $cert->id }}" value="">
                                        <button type="submit"
                                                style="background:none; border:1px solid rgba(220,38,38,.4); border-radius:var(--radius-sm); cursor:pointer; font-size:.72rem; color:#dc2626; padding:3px 10px; font-weight:600;">
                                            Revoke
                                        </button>
                                    </form>
                                @else
                                    <span style="font-size:.72rem; color:var(--color-muted); font-style:italic;">{{ $cert->revoked_at->format('d M Y') }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:var(--space-8);">
                {{ $certificates->links() }}
            </div>
        @endif

    </div>
</section>

@push('scripts')
<script>
function handleRevoke(form, serial) {
    const reason = prompt('Reason for revoking certificate ' + serial + ':\n(Required — min 10 characters)');
    if (!reason || reason.trim().length < 10) {
        if (reason !== null) alert('Please provide a reason of at least 10 characters.');
        return false;
    }
    form.querySelector('[name="reason"]').value = reason.trim();
    return confirm('Revoke ' + serial + '? This cannot be undone.');
}
</script>
@endpush

@endsection
