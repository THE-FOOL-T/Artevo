<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use App\Services\ActivityLogger;
use App\Services\ArtifactVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ArtifactVerificationController extends Controller
{
    public function __construct(
        private ArtifactVerificationService $verificationService,
        private ActivityLogger              $activityLogger,
    ) {}

    /**
     * Submit an artifact for admin review.
     * Only the artifact owner may call this, and only when not already
     * verified or pending.
     */
    public function store(Artifact $artifact): RedirectResponse
    {
        Gate::authorize('submitForVerification', $artifact);

        $this->verificationService->submitForVerification($artifact, auth()->user());

        $this->activityLogger->log(
            action: 'artifact.verification-submitted',
            description: auth()->user()->name . " submitted \"{$artifact->name}\" for verification.",
            subject: $artifact,
            user: auth()->user(),
        );

        return back()->with('success', "\"{$artifact->name}\" has been submitted for verification. You'll be notified when an admin reviews it.");
    }
}
