<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\ArtifactVerificationService;
use App\Services\CertificateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArtifactVerificationController extends Controller
{
    public function __construct(
        private ArtifactVerificationService $verificationService,
        private ActivityLogger              $activityLogger,
        private CertificateService          $certificateService,
    ) {}

    /**
     * Admin queue: all artifacts awaiting review, newest first.
     */
    public function index(Request $request): View
    {
        $query = Artifact::with(['images', 'museum', 'collector', 'creator'])
            ->where('verification_status', Artifact::VERIFICATION_PENDING)
            ->latest('updated_at');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('artifact_code', 'like', "%{$search}%");
            });
        }

        $artifacts = $query->paginate(15)->withQueryString();

        $stats = [
            'pending'  => Artifact::where('verification_status', Artifact::VERIFICATION_PENDING)->count(),
            'verified' => Artifact::where('verification_status', Artifact::VERIFICATION_VERIFIED)->count(),
            'rejected' => Artifact::where('verification_status', Artifact::VERIFICATION_REJECTED)->count(),
        ];

        return view('admin.artifact-verifications.index', [
            'artifacts' => $artifacts,
            'stats'     => $stats,
        ]);
    }

    /**
     * Approve an artifact's verification request.
     */
    public function verify(Request $request, Artifact $artifact): RedirectResponse
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->verificationService->verify($artifact, $request->user(), $request->get('note'));

        // Phase 16: Auto-issue Certificate of Authenticity on verification.
        $this->certificateService->issueVerification($artifact->load('museum.curator', 'collector'), $request->user());

        $this->activityLogger->log(
            action: 'artifact.verified',
            description: "{$request->user()->name} verified artifact \"{$artifact->name}\" (#{$artifact->artifact_code}).",
            subject: $artifact,
            user: $request->user(),
        );

        return back()->with('success', "\"{$artifact->name}\" has been marked as verified.");
    }

    /**
     * Reject an artifact's verification request with a required note.
     */
    public function reject(Request $request, Artifact $artifact): RedirectResponse
    {
        $request->validate([
            'note' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $this->verificationService->reject($artifact, $request->user(), $request->get('note'));

        $this->activityLogger->log(
            action: 'artifact.verification-rejected',
            description: "{$request->user()->name} rejected verification for \"{$artifact->name}\". Note: {$request->get('note')}",
            subject: $artifact,
            user: $request->user(),
        );

        return back()->with('success', "\"{$artifact->name}\" verification has been rejected.");
    }
}
