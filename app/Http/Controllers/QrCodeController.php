<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use App\Models\ArtifactQrCode;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class QrCodeController extends Controller
{
    public function __construct(
        private QrCodeService $qrCodeService,
    ) {}

    /**
     * Public QR scan handler.
     *
     * Resolves the token → logs the scan → redirects to the artifact page.
     * If the token is not found (stale/regenerated QR) return 404.
     */
    public function scan(string $token, Request $request): RedirectResponse
    {
        $qrCode = ArtifactQrCode::where('token', $token)
            ->with('artifact')
            ->first();

        if (! $qrCode || ! $qrCode->artifact) {
            abort(404, 'QR code not found or the artifact has been removed.');
        }

        // Log asynchronously — don't fail the redirect if analytics breaks
        try {
            $this->qrCodeService->recordScan($qrCode, $request);
        } catch (\Throwable) {
            // Silently swallow analytics failures
        }

        return redirect()->route('artifacts.show', $qrCode->artifact);
    }

    /**
     * Download the QR code as a PNG file.
     * Only available to the artifact owner or an admin.
     */
    public function download(Artifact $artifact): Response
    {
        Gate::authorize('view', $artifact); // owner + admin + curator can view

        $qrCode = $this->qrCodeService->ensureRecord($artifact);
        $png    = $this->qrCodeService->generatePng($qrCode);

        $filename = "artevo-qr-{$artifact->artifact_code}.png";

        return response($png, 200, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Return the QR as an inline SVG (for print pages / embedding).
     * Only available to the artifact owner or an admin.
     */
    public function embed(Artifact $artifact): Response
    {
        Gate::authorize('view', $artifact);

        $qrCode = $this->qrCodeService->ensureRecord($artifact);
        $svg    = $this->qrCodeService->generateSvg($qrCode);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
        ]);
    }
}
