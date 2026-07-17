<?php

namespace App\Policies;

use App\Models\Museum;
use App\Models\User;

class MuseumPolicy
{
    /**
     * Anyone can browse the public museum directory and profile pages.
     */
    public function viewAny(): bool
    {
        return true;
    }

    public function view(): bool
    {
        return true;
    }

    /**
     * Only curators and admins may create a museum profile — collectors
     * and visitors manage their own artifact collections instead (a
     * different concept from a Museum).
     */
    public function create(User $user): bool
    {
        return $user->isCurator() || $user->isAdmin();
    }

    /**
     * A curator may update only the museum they created; an admin may
     * update any museum.
     */
    public function update(User $user, Museum $museum): bool
    {
        return $user->isAdmin() || $user->id === $museum->curator_id;
    }

    public function delete(User $user, Museum $museum): bool
    {
        return $this->update($user, $museum);
    }

    /**
     * Managing gallery images/contacts follows the same rule as editing
     * the museum itself.
     */
    public function manageMedia(User $user, Museum $museum): bool
    {
        return $this->update($user, $museum);
    }
}
