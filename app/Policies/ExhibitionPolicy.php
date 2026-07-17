<?php

namespace App\Policies;

use App\Models\Exhibition;
use App\Models\User;

class ExhibitionPolicy
{
    /**
     * Anyone may view a published exhibition.
     * Drafts and archived exhibitions are only visible to the owning
     * curator (via their museum) and administrators.
     */
    public function view(?User $user, Exhibition $exhibition): bool
    {
        if ($exhibition->isPublished()) {
            return true;
        }

        return $user && ($user->isAdmin() || $this->ownsCurator($user, $exhibition));
    }

    public function create(User $user): bool
    {
        return $user->isCurator() || $user->isAdmin();
    }

    public function update(User $user, Exhibition $exhibition): bool
    {
        return $user->isAdmin() || $this->ownsCurator($user, $exhibition);
    }

    public function delete(User $user, Exhibition $exhibition): bool
    {
        return $this->update($user, $exhibition);
    }

    /**
     * Curators may publish/archive their own exhibitions.
     * Admins may publish/archive any.
     */
    public function publish(User $user, Exhibition $exhibition): bool
    {
        return $this->update($user, $exhibition);
    }

    /**
     * Only admins may feature an exhibition.
     */
    public function feature(User $user, Exhibition $exhibition): bool
    {
        return $user->isAdmin();
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * True when the user is the curator of the museum that owns this exhibition.
     */
    private function ownsCurator(User $user, Exhibition $exhibition): bool
    {
        return (int) $exhibition->museum?->curator_id === $user->id;
    }
}
