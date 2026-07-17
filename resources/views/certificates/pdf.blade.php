<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $certificate->typeLabel() }} — {{ $certificate->artifact->name }}</title>
    <style>
        @page { margin: 0; size: A4 landscape; }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Georgia, 'Times New Roman', serif;
            background: #fdfaf4;
            color: #1a1208;
            width: 297mm;
            height: 210mm;
            position: relative;
            overflow: hidden;
        }

        /* ── Ornamental border ── */
        .border-outer {
            position: absolute;
            inset: 8mm;
            border: 2px solid #b8943a;
        }
        .border-inner {
            position: absolute;
            inset: 11mm;
            border: 1px solid #d4af37;
        }

        /* ── Corner ornaments ── */
        .corner {
            position: absolute;
            width: 18mm;
            height: 18mm;
            font-size: 18px;
            color: #b8943a;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .corner--tl { top: 6mm;  left: 6mm; }
        .corner--tr { top: 6mm;  right: 6mm; transform: rotate(90deg); }
        .corner--bl { bottom: 6mm; left: 6mm;  transform: rotate(-90deg); }
        .corner--br { bottom: 6mm; right: 6mm; transform: rotate(180deg); }

        /* ── Content wrapper ── */
        .content {
            position: absolute;
            inset: 14mm 16mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        /* ── Header ── */
        .org-name {
            font-size: 9pt;
            letter-spacing: 0.35em;
            text-transform: uppercase;
            color: #b8943a;
            text-align: center;
        }
        .cert-type {
            font-size: 20pt;
            font-weight: bold;
            letter-spacing: 0.05em;
            text-align: center;
            color: #1a1208;
            margin-top: 3mm;
        }
        .divider {
            width: 80mm;
            height: 1px;
            background: linear-gradient(to right, transparent, #b8943a, transparent);
            margin: 3mm auto;
        }

        /* ── Body ── */
        .body-text {
            font-size: 10pt;
            text-align: center;
            line-height: 1.7;
            color: #3a2e1a;
            max-width: 220mm;
        }
        .artifact-name {
            font-size: 15pt;
            font-style: italic;
            font-weight: bold;
            color: #1a1208;
        }
        .highlight {
            color: #8a6a20;
            font-weight: bold;
        }

        /* ── Artifact image ── */
        .artifact-img {
            width: 28mm;
            height: 28mm;
            object-fit: cover;
            border: 1.5px solid #b8943a;
            margin: 0 auto 3mm;
            display: block;
        }
        .artifact-img-placeholder {
            width: 28mm;
            height: 28mm;
            background: #f0e8d0;
            border: 1.5px solid #b8943a;
            margin: 0 auto 3mm;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* ── Meta row ── */
        .meta-row {
            display: flex;
            gap: 12mm;
            justify-content: center;
            font-size: 8pt;
            color: #6b5a2e;
        }
        .meta-item { text-align: center; }
        .meta-label { font-size: 6.5pt; text-transform: uppercase; letter-spacing: 0.12em; color: #b8943a; }

        /* ── Footer ── */
        .footer {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .sig-block { text-align: center; }
        .sig-line { width: 50mm; height: 1px; background: #b8943a; margin: 0 auto 2mm; }
        .sig-name { font-size: 7.5pt; color: #3a2e1a; }
        .sig-role { font-size: 6pt; color: #8a6a20; letter-spacing: 0.05em; text-transform: uppercase; }

        .serial-block { text-align: center; }
        .serial-label { font-size: 6.5pt; text-transform: uppercase; letter-spacing: 0.12em; color: #b8943a; }
        .serial-num { font-size: 8pt; font-family: 'Courier New', monospace; color: #1a1208; font-weight: bold; }

        .verify-block { text-align: center; }
        .verify-label { font-size: 6.5pt; text-transform: uppercase; letter-spacing: 0.08em; color: #b8943a; }
        .verify-url { font-size: 7pt; font-family: 'Courier New', monospace; color: #3a2e1a; word-break: break-all; }

        /* ── Revoked watermark ── */
        .revoked-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 48pt;
            font-weight: bold;
            color: rgba(180, 30, 30, 0.18);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            pointer-events: none;
            white-space: nowrap;
        }
    </style>
</head>
<body>

    {{-- Ornamental borders --}}
    <div class="border-outer"></div>
    <div class="border-inner"></div>

    {{-- Corner ornaments --}}
    <div class="corner corner--tl">✦</div>
    <div class="corner corner--tr">✦</div>
    <div class="corner corner--bl">✦</div>
    <div class="corner corner--br">✦</div>

    {{-- Revoked watermark --}}
    @if($certificate->isRevoked())
        <div class="revoked-watermark">REVOKED</div>
    @endif

    <div class="content">

        {{-- Header --}}
        <div style="text-align:center;">
            <div class="org-name">Artevo · Smart Artifact Archive & Authentication Platform</div>
            <div class="cert-type">{{ $certificate->typeLabel() }}</div>
            <div class="divider"></div>
        </div>

        {{-- Artifact image --}}
        <div>
            @if($certificate->artifact->primaryImage())
                <img class="artifact-img"
                     src="{{ public_path('storage/' . $certificate->artifact->primaryImage()->path) }}"
                     alt="{{ $certificate->artifact->name }}">
            @else
                <div class="artifact-img-placeholder">🏺</div>
            @endif
        </div>

        {{-- Body text --}}
        <div class="body-text">
            This is to certify that the artifact<br>
            <span class="artifact-name">{{ $certificate->artifact->name }}</span><br>
            @if($certificate->artifact->category)
                <span style="font-size:9pt; color:#6b5a2e;">{{ $certificate->artifact->category->name }}</span> ·
            @endif
            <span style="font-size:9pt; color:#6b5a2e;">Ref: {{ $certificate->artifact->artifact_code }}</span><br><br>
            @if($certificate->type === \App\Models\Certificate::TYPE_DONATION_TRANSFER)
                has been officially transferred in accordance with the Artevo Provenance Registry.<br>
                This document serves as proof of authenticated ownership transfer.
            @else
                has been examined, authenticated, and <span class="highlight">verified</span> by the<br>
                Artevo Review Panel in accordance with our standards of cultural heritage preservation.
            @endif
        </div>

        {{-- Meta row --}}
        <div class="meta-row">
            @if($certificate->artifact->civilization)
                <div class="meta-item">
                    <div class="meta-label">Civilization</div>
                    <div>{{ $certificate->artifact->civilization }}</div>
                </div>
            @endif
            @if($certificate->artifact->country_of_origin)
                <div class="meta-item">
                    <div class="meta-label">Country of Origin</div>
                    <div>{{ $certificate->artifact->country_of_origin }}</div>
                </div>
            @endif
            @if($certificate->artifact->era)
                <div class="meta-item">
                    <div class="meta-label">Era</div>
                    <div>{{ $certificate->artifact->era }}</div>
                </div>
            @endif
            <div class="meta-item">
                <div class="meta-label">Issued</div>
                <div>{{ $certificate->created_at->format('d M Y') }}</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            {{-- Issuer signature --}}
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-name">{{ $certificate->issuer?->name ?? 'Artevo Review Panel' }}</div>
                <div class="sig-role">Authorising Administrator</div>
            </div>

            {{-- Serial number --}}
            <div class="serial-block">
                <div class="serial-label">Certificate Serial</div>
                <div class="serial-num">{{ $certificate->serial }}</div>
                @if($certificate->isRevoked())
                    <div style="font-size:6.5pt; color:#c0392b; margin-top:1mm; font-weight:bold;">REVOKED — {{ $certificate->revoked_at->format('d M Y') }}</div>
                @endif
            </div>

            {{-- Verification URL --}}
            <div class="verify-block">
                <div class="verify-label">Verify at</div>
                <div class="verify-url">{{ $certificate->verificationUrl() }}</div>
            </div>
        </div>

    </div>
</body>
</html>
