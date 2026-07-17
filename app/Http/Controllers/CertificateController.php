<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function __construct(
        private CertificateService $certificateService,
    ) {}

    /**
     * Public certificate verification page.
     * Anyone with the serial number can verify a certificate is genuine.
     */
    public function verify(Certificate $certificate): View
    {
        $certificate->load(['artifact.images', 'artifact.category', 'recipient', 'issuer']);

        return view('certificates.verify', compact('certificate'));
    }

    /**
     * List all certificates for the authenticated user's artifacts.
     */
    public function index(): View
    {
        $certificates = Certificate::where('issued_to', auth()->id())
            ->with(['artifact.images', 'artifact.category'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('certificates.index', compact('certificates'));
    }

    /**
     * Download a certificate as a PDF.
     * Only the recipient, the artifact owner, or an admin may download.
     */
    public function download(Certificate $certificate): Response
    {
        Gate::authorize('view', $certificate->artifact);

        $path = "certificates/{$certificate->serial}.pdf";

        if (! \Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            return back()->with('info', 'Your certificate is currently being generated. Please check back in a few moments.');
        }

        $filename = "artevo-certificate-{$certificate->serial}.pdf";

        return \Illuminate\Support\Facades\Storage::disk('local')->download($path, $filename);
    }

    /**
     * Issue a Certificate of Authenticity for a verified artifact on demand.
     * Only available to the artifact owner once the artifact is verified.
     */
    public function issue(Artifact $artifact): RedirectResponse
    {
        Gate::authorize('view', $artifact);

        if (! $artifact->isVerified()) {
            return back()->with('error', 'Only verified artifacts can have a certificate issued.');
        }

        $this->certificateService->issueVerification($artifact, auth()->user());

        return back()->with('success', 'Certificate of Authenticity issued successfully.');
    }
}
