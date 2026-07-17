<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function __construct(
        private CertificateService $certificateService,
    ) {}

    /**
     * Admin certificate management — all issued certificates with
     * revocation/validity status.
     */
    public function index(Request $request): View
    {
        $certificates = Certificate::with(['artifact.images', 'recipient', 'issuer'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->when($request->boolean('revoked'), fn ($q) => $q->whereNotNull('revoked_at'))
            ->when(! $request->boolean('revoked'), fn ($q) => $q->whereNull('revoked_at'))
            ->orderByDesc('created_at')
            ->paginate(25);

        $totalIssued  = Certificate::count();
        $totalRevoked = Certificate::whereNotNull('revoked_at')->count();
        $totalValid   = $totalIssued - $totalRevoked;

        return view('admin.certificates.index', compact(
            'certificates',
            'totalIssued',
            'totalRevoked',
            'totalValid',
        ));
    }

    /**
     * Revoke a certificate.
     */
    public function revoke(Certificate $certificate, Request $request): RedirectResponse
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $this->certificateService->revoke($certificate, $request->string('reason'));

        return back()->with('success', "Certificate {$certificate->serial} has been revoked.");
    }
}
