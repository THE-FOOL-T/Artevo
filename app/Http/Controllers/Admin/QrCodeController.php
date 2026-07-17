<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\ArtifactQrCode;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QrCodeController extends Controller
{
    public function __construct(
        private QrCodeService $qrCodeService,
    ) {}

    /**
     * Admin QR scan overview — all artifacts with their QR scan counts,
     * ordered by most scanned first.
     */
    public function index(): View
    {
        $qrCodes = ArtifactQrCode::with(['artifact.images'])
            ->orderByDesc('scan_count')
            ->paginate(30);

        $totalScans    = ArtifactQrCode::sum('scan_count');
        $totalWithCode = ArtifactQrCode::count();
        $totalArtifacts = Artifact::count();

        return view('admin.qr-codes.index', compact(
            'qrCodes',
            'totalScans',
            'totalWithCode',
            'totalArtifacts',
        ));
    }

    /**
     * Regenerate the QR token for an artifact.
     * This invalidates all previously-printed QR labels.
     */
    public function regenerate(Artifact $artifact): RedirectResponse
    {
        $qrCode = $this->qrCodeService->ensureRecord($artifact);
        $this->qrCodeService->regenerate($qrCode);

        return back()->with('success', "QR code for \"{$artifact->name}\" regenerated. Old printed labels are now invalid.");
    }
}
