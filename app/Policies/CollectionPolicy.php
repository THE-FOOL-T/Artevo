<?php

namespace App\Policies;

use App\Models\Collection;
use App\Models\User;

class CollectionPolicy
{
    /**
     * Anyone can browse/view a public collection.
     * A private collection is only visible to its owner or an administrator.
     */
    public function view(?User $user, Collection $collection): bool
    {
        if ($collection->isPublic()) {
            return true;
        }

        return $user && ($user->isAdmin() || $collection->isOwnedBy($user));
    }

    /**
     * Curators and collectors may create collections.
     * Visitors cannot — they must first upgrade their account.
     */
    public function create(User $user): bool
    {
        return $user->isCurator() || $user->isCollector() || $user->isAdmin();
    }

    /**
     * The collection owner (curator who owns the museum, or the collector)
     * and administrators may edit a collection.
     */
    public function update(User $user, Collection $collection): bool
    {
        return $user->isAdmin() || $collection->isOwnedBy($user);
    }

    public function delete(User $user, Collection $collection): bool
    {
        return $this->update($user, $collection);
    }

    /**
     * Any authenticated user may favorite a public collection.
     */
    public function favorite(User $user, Collection $collection): bool
    {
        return $collection->isPublic();
    }

    /**
     * Only the collection owner or an admin can add/remove/reorder artifacts.
     */
    public function manageArtifacts(User $user, Collection $collection): bool
    {
        return $this->update($user, $collection);
    }
}
