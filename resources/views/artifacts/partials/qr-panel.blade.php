@props(['artifact'])

@php
    // ensureQrCode() is idempotent: returns existing record or creates one.
    // This means the QR is generated the first time the owner visits the page.
    $qrCode    = $artifact->ensureQrCode();
    $scanCount = $qrCode->scan_count;
@endphp

<div style="border:1px solid var(--color-border); border-radius:var(--radius-md); overflow:hidden;">
    {{-- Header --}}
    <div style="padding:var(--space-3) var(--space-4); background:var(--color-surface-2); border-bottom:1px solid var(--color-border); display:flex; align-items:center; gap:var(--space-2);">
        <span style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--color-muted);">QR Code</span>
        @if($scanCount > 0)
            <span style="margin-left:auto; font-size:.72rem; color:var(--color-muted);">
                {{ number_format($scanCount) }} {{ \Illuminate\Support\Str::plural('scan', $scanCount) }}
            </span>
        @endif

    </div>

    {{-- QR Image --}}
    <div style="padding:var(--space-4); display:flex; flex-direction:column; gap:var(--space-3); align-items:center;">
        {{-- Render via the embed endpoint (which also lazy-creates the record) --}}
        <div style="background:#fff; padding:var(--space-2); border-radius:var(--radius-sm); border:1px solid var(--color-border); display:inline-block;">
            <img src="{{ route('artifacts.qr.embed', $artifact) }}"
                 alt="QR code for {{ $artifact->name }}"
                 width="160" height="160"
                 style="display:block;">
        </div>

        <p style="font-size:.72rem; color:var(--color-muted); text-align:center; margin:0; line-height:1.5;">
            Scan to view this artifact's public page.<br>
            Generation {{ $qrCode->generation }}
            @if($qrCode->last_scanned_at)
                · Last scanned {{ $qrCode->last_scanned_at->diffForHumans() }}
            @endif
        </p>

        {{-- Actions --}}
        <div class="flex" style="gap:var(--space-2); justify-content:center; flex-wrap:wrap;">
            <a href="{{ route('artifacts.qr.download', $artifact) }}"
               class="av-btn av-btn--outline"
               style="font-size:.78rem; padding:4px 12px;"
               download>
                ⬇ Download PNG
            </a>
            <a href="{{ route('artifacts.qr.embed', $artifact) }}"
               class="av-btn av-btn--outline"
               style="font-size:.78rem; padding:4px 12px;"
               target="_blank" rel="noopener">
                🖨 Print SVG
            </a>
        </div>

        {{-- Admin: regenerate --}}
        @if(auth()->user()?->isAdmin())
        <form method="POST"
              action="{{ route('admin.artifacts.qr.regenerate', $artifact) }}"
              onsubmit="return confirm('Regenerate QR? All previously printed labels will no longer work.');">
            @csrf
            <button type="submit"
                    style="background:none; border:none; cursor:pointer; font-size:.72rem; color:var(--color-danger); padding:0; font-weight:600;">
                ↺ Regenerate token
            </button>
        </form>
        @endif
    </div>
</div>
