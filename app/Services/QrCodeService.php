<?php

namespace App\Services;

use App\Models\Artifact;
use App\Models\ArtifactQrCode;
use App\Models\QrScan;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QrCodeService
{
    /**
     * Get (or create) the QR code record for an artifact.
     */
    public function ensureRecord(Artifact $artifact): ArtifactQrCode
    {
        return $artifact->qrCode ?? ArtifactQrCode::create([
            'artifact_id' => $artifact->id,
            'token'       => Str::uuid()->toString(),
            'generation'  => 1,
        ]);
    }

    /**
     * Generate a PNG QR code image and return it as a raw binary string.
     * The QR encodes the public scan URL: /qr/{token}
     */
    public function generatePng(ArtifactQrCode $qrCode): string
    {
        $builder = new Builder();
        $result = $builder->build(
            writer: new PngWriter(),
            data: $qrCode->scanUrl(),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 400,
            margin: 16,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        return $result->getString();
    }

    /**
     * Generate an SVG QR code and return it as a string.
     */
    public function generateSvg(ArtifactQrCode $qrCode): string
    {
        $builder = new Builder();
        $result = $builder->build(
            writer: new SvgWriter(),
            data: $qrCode->scanUrl(),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 12
        );

        return $result->getString();
    }

    /**
     * Generate a PNG and return it as a base64 data URI for embedding in HTML.
     */
    public function generateDataUri(ArtifactQrCode $qrCode): string
    {
        $png = $this->generatePng($qrCode);
        return 'data:image/png;base64,' . base64_encode($png);
    }

    /**
     * Log a QR scan — increment counter and write an analytics record.
     * Ignores bots via basic user-agent heuristic.
     */
    public function recordScan(ArtifactQrCode $qrCode, Request $request): void
    {
        $ua = $request->userAgent() ?? '';

        // Skip obvious crawlers / health checks
        if (preg_match('/bot|crawl|spider|curl|wget|monitoring/i', $ua)) {
            return;
        }

        QrScan::create([
            'artifact_qr_code_id' => $qrCode->id,
            'ip_address'          => $request->ip(),
            'user_agent'          => $ua,
            'referrer'            => $request->header('Referer'),
            'generation'          => $qrCode->generation,
            'scanned_at'          => now(),
        ]);

        $qrCode->increment('scan_count');
        $qrCode->update(['last_scanned_at' => now()]);
    }

    /**
     * Invalidate the current token and issue a new one.
     * Old printed QR codes will now point to the scan URL for the OLD token,
     * which won't match any record — effectively breaking stale QR prints.
     */
    public function regenerate(ArtifactQrCode $qrCode): ArtifactQrCode
    {
        $qrCode->update([
            'token'      => Str::uuid()->toString(),
            'generation' => $qrCode->generation + 1,
        ]);

        return $qrCode->fresh();
    }
}
