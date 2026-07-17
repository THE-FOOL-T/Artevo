<?php

namespace App\Policies;

use App\Models\Artifact;
use App\Models\User;

class ArtifactPolicy
{
    /**
     * Anyone can browse/view a public artifact. A private or archived
     * one is only visible to its owner or an admin.
     */
    public function view(?User $user, Artifact $artifact): bool
    {
        if ($artifact->isPublic()) {
            return true;
        }

        return $user && ($user->isAdmin() || $artifact->isOwnedBy($user));
    }

    /**
     * Curators and collectors may both create artifacts — a curator's
     * artifact belongs to one of their museums, a collector's belongs to
     * their personal collection. Visitors cannot.
     */
    public function create(User $user): bool
    {
        return $user->isCurator() || $user->isCollector() || $user->isAdmin();
    }

    public function update(User $user, Artifact $artifact): bool
    {
        return $user->isAdmin() || $artifact->isOwnedBy($user);
    }

    public function delete(User $user, Artifact $artifact): bool
    {
        return $this->update($user, $artifact);
    }

    public function manageMedia(User $user, Artifact $artifact): bool
    {
        return $this->update($user, $artifact);
    }

    // ─── Phase 11 ─────────────────────────────────────────────────────────────

    /**
     * The artifact owner can submit for verification when the artifact is
     * not already verified or awaiting review.
     */
    public function submitForVerification(User $user, Artifact $artifact): bool
    {
        if (! $artifact->isOwnedBy($user) && ! $user->isAdmin()) {
            return false;
        }

        return ! $artifact->isVerified() && ! $artifact->isPendingVerification();
    }

    /**
     * Only admins may approve or reject verification submissions.
     */
    public function verify(User $user, Artifact $artifact): bool
    {
        return $user->isAdmin();
    }

    /**
     * The artifact owner or an admin may manage provenance records.
     */
    public function manageProvenance(User $user, Artifact $artifact): bool
    {
        return $user->isAdmin() || $artifact->isOwnedBy($user);
    }

    // ─── Phase 12 ─────────────────────────────────────────────────────────────

    /**
     * Only the artifact owner and admins can read or write private curator notes.
     */
    public function manageCuratorNotes(User $user, Artifact $artifact): bool
    {
        return $user->isAdmin() || $artifact->isOwnedBy($user);
    }

    /**
     * Owner or admin may add, edit, delete, and reorder restoration records.
     */
    public function manageRestorationRecords(User $user, Artifact $artifact): bool
    {
        return $user->isAdmin() || $artifact->isOwnedBy($user);
    }
}
