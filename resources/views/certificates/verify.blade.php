@extends('layouts.app')

@section('title', 'Certificate Verification — ' . $certificate->serial . ' — Artevo')
@section('meta_description', 'Verify the authenticity of this Artevo certificate for ' . $certificate->artifact->name)

@section('content')

{{-- ── Hero ────────────────────────────────────────────────────────────────── --}}
<section class="av-section" style="background:linear-gradient(135deg,#0e0c1a 0%,#1e1428 60%,#2a1e38 100%); padding:var(--space-16) 0 var(--space-12);">
    <div class="container" style="text-align:center;">
        @if($certificate->isRevoked())
            <div style="display:inline-flex; align-items:center; gap:var(--space-2); background:rgba(220,38,38,.18); border:1px solid rgba(220,38,38,.4); border-radius:var(--radius-sm); padding:6px 16px; margin-bottom:var(--space-4);">
                <span style="color:#f87171; font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em;">⚠ Revoked Certificate</span>
            </div>
        @else
            <div style="display:inline-flex; align-items:center; gap:var(--space-2); background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.35); border-radius:var(--radius-sm); padding:6px 16px; margin-bottom:var(--space-4);">
                <span style="color:#34d399; font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em;">✓ Verified Certificate</span>
            </div>
        @endif

        <h1 style="color:#fff; font-size:clamp(1.6rem,3vw,2.4rem); margin-bottom:var(--space-2);">
            {{ $certificate->typeLabel() }}
        </h1>
        <p style="color:rgba(255,255,255,.5); font-family:var(--font-mono); font-size:.82rem; letter-spacing:.04em;">
            Serial: {{ $certificate->serial }}
        </p>
    </div>
</section>

{{-- ── Certificate card ───────────────────────────────────────────────────── --}}
<section class="av-section av-section--white" style="padding-top:var(--space-10); padding-bottom:var(--space-16);">
    <div class="container" style="max-width:760px;">

        @if($certificate->isRevoked())
        <div style="background:rgba(220,38,38,.07); border:1px solid rgba(220,38,38,.3); border-radius:var(--radius-md); padding:var(--space-4) var(--space-5); margin-bottom:var(--space-6); display:flex; gap:var(--space-3);">
            <span style="font-size:1.4rem;">⚠</span>
            <div>
                <p style="font-weight:700; color:#dc2626; margin:0 0 4px;">This certificate has been revoked</p>
                <p style="font-size:.85rem; color:var(--color-muted); margin:0;">
                    Revoked on {{ $certificate->revoked_at->format('d F Y') }}.
                    @if($certificate->revocation_reason) Reason: {{ $certificate->revocation_reason }}@endif
                </p>
            </div>
        </div>
        @endif

        {{-- Main certificate panel --}}
        <div style="border:2px solid var(--color-border); border-radius:var(--radius-lg); overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.08);">

            {{-- Ornamental header --}}
            <div style="background:linear-gradient(135deg,#1a1208,#2d1f08); padding:var(--space-6) var(--space-8); text-align:center; position:relative; overflow:hidden;">
                <div style="position:absolute; inset:0; opacity:.06; background:repeating-linear-gradient(45deg,#d4af37 0,#d4af37 1px,transparent 0,transparent 50%); background-size:12px 12px;"></div>
                <p style="font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.35em; color:rgba(212,175,55,.6); margin:0 0 var(--space-2); position:relative;">Artevo · Smart Artifact Archive</p>
                <h2 style="font-size:1.5rem; color:#d4af37; margin:0; position:relative; letter-spacing:.04em;">{{ $certificate->typeLabel() }}</h2>
                <div style="width:80px; height:1px; background:linear-gradient(to right,transparent,#d4af37,transparent); margin:var(--space-3) auto 0; position:relative;"></div>
            </div>

            <div style="padding:var(--space-8);">

                {{-- Artifact section --}}
                <div class="flex" style="gap:var(--space-6); align-items:flex-start; flex-wrap:wrap;">
                    @if($certificate->artifact->primaryImage())
                        <div style="flex-shrink:0; width:100px; height:100px; border-radius:var(--radius-md); overflow:hidden; border:2px solid rgba(212,175,55,.3);">
                            <img src="{{ $certificate->artifact->primaryImage()->url() }}"
                                 alt="{{ $certificate->artifact->name }}"
                                 style="width:100%; height:100%; object-fit:cover;">
                        </div>
                    @else
                        <div style="flex-shrink:0; width:100px; height:100px; border-radius:var(--radius-md); border:2px solid rgba(212,175,55,.3); background:var(--color-surface-2); display:flex; align-items:center; justify-content:center; font-size:2.5rem;">🏺</div>
                    @endif

                    <div style="flex:1; min-width:200px;">
                        @if($certificate->artifact->category)
                            <span style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brass-700);">{{ $certificate->artifact->category->name }}</span>
                        @endif
                        <h3 style="font-size:1.3rem; margin:var(--space-1) 0 var(--space-1);">{{ $certificate->artifact->name }}</h3>
                        <p style="font-family:var(--font-mono); font-size:.75rem; color:var(--color-muted); margin:0 0 var(--space-3);">{{ $certificate->artifact->artifact_code }}</p>

                        <a href="{{ route('artifacts.show', $certificate->artifact) }}"
                           style="font-size:.82rem; font-weight:600; color:var(--brass-700); text-decoration:none;">
                            View artifact page →
                        </a>
                    </div>
                </div>

                <hr style="border:none; border-top:1px solid var(--color-border); margin:var(--space-6) 0;">

                {{-- Certification statement --}}
                <div style="text-align:center; padding:var(--space-2) var(--space-4);">
                    @if($certificate->type === \App\Models\Certificate::TYPE_DONATION_TRANSFER)
                        <p style="font-size:.95rem; line-height:1.7; color:var(--color-body); font-family:Georgia, serif;">
                            This document certifies that the above artifact has been <strong>officially transferred</strong>
                            in accordance with the Artevo Provenance Registry.
                            @if($certificate->notes)<br><em style="color:var(--color-muted); font-size:.88rem;">{{ $certificate->notes }}</em>@endif
                        </p>
                    @else
                        <p style="font-size:.95rem; line-height:1.7; color:var(--color-body); font-family:Georgia, serif;">
                            This document certifies that the above artifact has been <strong>examined, authenticated,
                            and verified</strong> by the Artevo Review Panel in accordance with our standards of
                            cultural heritage preservation and documentation.
                        </p>
                    @endif
                </div>

                <hr style="border:none; border-top:1px solid var(--color-border); margin:var(--space-6) 0;">

                {{-- Meta grid --}}
                <div class="grid grid-4" style="gap:var(--space-4); font-size:.82rem;">
                    <div>
                        <p style="font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--brass-700); margin:0 0 4px;">Issued To</p>
                        <p style="margin:0; font-weight:600;">{{ $certificate->recipient?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--brass-700); margin:0 0 4px;">Issued By</p>
                        <p style="margin:0; font-weight:600;">{{ $certificate->issuer?->name ?? 'Artevo Panel' }}</p>
                    </div>
                    <div>
                        <p style="font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--brass-700); margin:0 0 4px;">Issue Date</p>
                        <p style="margin:0; font-weight:600;">{{ $certificate->created_at->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p style="font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--brass-700); margin:0 0 4px;">Status</p>
                        <p style="margin:0; font-weight:700; color:{{ $certificate->isRevoked() ? '#dc2626' : '#10b981' }};">
                            {{ $certificate->isRevoked() ? 'Revoked' : 'Valid' }}
                        </p>
                    </div>
                </div>

                <hr style="border:none; border-top:1px solid var(--color-border); margin:var(--space-6) 0;">

                {{-- Download action --}}
                @auth
                <div class="flex" style="gap:var(--space-3); align-items:center; flex-wrap:wrap;">
                    <a href="{{ route('certificates.download', $certificate) }}"
                       class="av-btn av-btn--primary"
                       style="font-size:.88rem;">
                        ⬇ Download PDF Certificate
                    </a>
                    <p style="margin:0; font-size:.78rem; color:var(--color-muted);">
                        Save or print a permanent record of this certificate.
                    </p>
                </div>
                @else
                <p style="font-size:.85rem; color:var(--color-muted);">
                    <a href="{{ route('login') }}" style="color:var(--brass-700); font-weight:600;">Sign in</a> to download the PDF certificate.
                </p>
                @endauth

            </div>
        </div>

        {{-- Verification note --}}
        <p style="text-align:center; font-size:.75rem; color:var(--color-muted); margin-top:var(--space-6);">
            This page confirms the authenticity of certificate <strong>{{ $certificate->serial }}</strong>.<br>
            The URL of this page is the canonical verification link. Any downloaded PDF will print this URL.
        </p>

    </div>
</section>

@endsection
