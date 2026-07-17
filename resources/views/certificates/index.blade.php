@extends('layouts.app')

@section('title', 'My Certificates — Artevo')
@section('meta_description', 'View and download your Artevo Certificates of Authenticity.')

@section('content')

<section class="av-section av-section--white" style="padding-top:var(--space-10); padding-bottom:var(--space-16);">
    <div class="container">

        <x-tag>Phase 16</x-tag>
        <h1 style="margin-top:var(--space-4);">My Certificates</h1>
        <p style="color:var(--color-muted); max-width:520px; margin-top:var(--space-2);">
            Certificates of Authenticity and Ownership Transfer issued for your artifacts.
        </p>

        @if(session('success'))
            <x-alert type="success" class="mt-4">{{ session('success') }}</x-alert>
        @endif

        @if($certificates->isEmpty())
            <div style="text-align:center; padding:var(--space-16) 0; color:var(--color-muted);">
                <div style="font-size:3rem; margin-bottom:var(--space-3);">🏛</div>
                <h3 style="margin-bottom:var(--space-2);">No certificates yet</h3>
                <p style="max-width:380px; margin:0 auto;">
                    Certificates are issued automatically when an artifact is verified or an ownership transfer is completed.
                </p>
            </div>
        @else
            <div style="margin-top:var(--space-8); display:flex; flex-direction:column; gap:var(--space-4);">
                @foreach($certificates as $cert)
                <div style="border:1px solid var(--color-border); border-radius:var(--radius-md); overflow:hidden; display:flex; align-items:stretch; {{ $cert->isRevoked() ? 'opacity:.65;' : '' }}">

                    {{-- Left accent bar --}}
                    <div style="width:4px; background:{{ $cert->isRevoked() ? '#dc2626' : '#10b981' }}; flex-shrink:0;"></div>

                    {{-- Artifact thumbnail --}}
                    <div style="flex-shrink:0; width:72px; background:var(--color-surface-2); display:flex; align-items:center; justify-content:center;">
                        @if($cert->artifact->primaryImage())
                            <img src="{{ $cert->artifact->primaryImage()->url() }}"
                                 alt="{{ $cert->artifact->name }}"
                                 style="width:72px; height:72px; object-fit:cover;">
                        @else
                            <span style="font-size:1.6rem;">🏺</span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div style="padding:var(--space-3) var(--space-4); flex:1; display:flex; align-items:center; gap:var(--space-6); flex-wrap:wrap;">
                        <div style="flex:1; min-width:180px;">
                            <div class="flex" style="align-items:center; gap:var(--space-2); margin-bottom:4px; flex-wrap:wrap;">
                                <span style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:{{ $cert->isRevoked() ? '#dc2626' : '#10b981' }};">
                                    {{ $cert->isRevoked() ? '⚠ Revoked' : '✓ Valid' }}
                                </span>
                                <span style="font-size:.7rem; color:var(--color-muted);">·</span>
                                <span style="font-size:.7rem; color:var(--color-muted); text-transform:uppercase; letter-spacing:.05em;">{{ $cert->typeLabel() }}</span>
                            </div>
                            <p style="font-weight:700; margin:0 0 2px; font-size:.95rem;">{{ $cert->artifact->name }}</p>
                            <p style="font-family:var(--font-mono); font-size:.72rem; color:var(--color-muted); margin:0;">{{ $cert->serial }}</p>
                        </div>

                        <div style="flex-shrink:0; font-size:.8rem; color:var(--color-muted); text-align:right;">
                            <p style="margin:0;">Issued {{ $cert->created_at->format('d M Y') }}</p>
                            @if($cert->issuer)
                                <p style="margin:0; font-size:.72rem;">by {{ $cert->issuer->name }}</p>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex" style="gap:var(--space-2); flex-shrink:0; align-items:center;">
                            <a href="{{ $cert->verificationUrl() }}"
                               style="font-size:.78rem; font-weight:600; color:var(--brass-700); text-decoration:none;">
                                Verify →
                            </a>
                            @if($cert->isValid())
                                <a href="{{ route('certificates.download', $cert) }}"
                                   class="av-btn av-btn--outline"
                                   style="font-size:.75rem; padding:4px 12px;"
                                   download>
                                    ⬇ PDF
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div style="margin-top:var(--space-8);">
                {{ $certificates->links() }}
            </div>
        @endif

    </div>
</section>

@endsection
