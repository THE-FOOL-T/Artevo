<?php

namespace App\Services;

use App\Models\Artifact;
use App\Models\User;
use App\Notifications\ArtifactVerificationNotification;
use App\Services\CertificateService;

class ArtifactVerificationService
{
    /**
     * Submit an artifact for admin review.
     * Sets status to `pending` and notifies all admin users.
     */
    public function submitForVerification(Artifact $artifact, User $submittedBy): void
    {
        $artifact->update([
            'verification_status' => Artifact::VERIFICATION_PENDING,
            'verified_by'         => null,
            'verified_at'         => null,
            'verification_note'   => null,
        ]);

        // Notify all admins of the new submission
        User::where('role', 'admin')->each(function (User $admin) use ($artifact, $submittedBy) {
            $admin->notify(new ArtifactVerificationNotification(
                artifact: $artifact,
                type: 'submitted',
                submittedBy: $submittedBy
            ));
        });
    }

    /**
     * Approve an artifact, marking it as verified.
     * The verifier (an admin) is recorded together with a timestamp.
     */
    public function verify(Artifact $artifact, User $admin, ?string $note = null): void
    {
        $artifact->update([
            'verification_status' => Artifact::VERIFICATION_VERIFIED,
            'verified_by'         => $admin->id,
            'verified_at'         => now(),
            'verification_note'   => $note,
        ]);

        // Notify the artifact owner
        $owner = $this->resolveOwner($artifact);
        $owner?->notify(new ArtifactVerificationNotification(
            artifact: $artifact,
            type: 'verified',
            verifiedByName: $admin->name
        ));
    }

    /**
     * Reject an artifact's verification request.
     * A rejection note is required so the owner knows what to fix.
     */
    public function reject(Artifact $artifact, User $admin, string $note): void
    {
        $artifact->update([
            'verification_status' => Artifact::VERIFICATION_REJECTED,
            'verified_by'         => $admin->id,
            'verified_at'         => now(),
            'verification_note'   => $note,
        ]);

        $owner = $this->resolveOwner($artifact);
        $owner?->notify(new ArtifactVerificationNotification(
            artifact: $artifact,
            type: 'rejected',
            note: $note
        ));
    }

    /**
     * Resolve the user who effectively "owns" the artifact to receive
     * verification outcome notifications.
     */
    private function resolveOwner(Artifact $artifact): ?User
    {
        if ($artifact->isMuseumArtifact()) {
            return $artifact->museum?->curator ?? null;
        }

        return $artifact->collector ?? null;
    }
}
