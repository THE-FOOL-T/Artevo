<?php

namespace App\Policies;

use App\Models\User;

/**
 * Authorization for actions performed on/by a User account. Currently
 * covers role changes (Admin\UserController); grows to cover profile
 * visibility etc. as the platform needs it.
 */
class UserPolicy
{
    /**
     * Whether $actor may change $target's role.
     *
     * Only administrators may change roles, and an administrator may not
     * change their own role — this prevents a solo admin from locking
     * themselves out by accident, and keeps a paper trail requiring a
     * second administrator for any admin-role change.
     */
    public function updateRole(User $actor, User $target): bool
    {
        return $actor->isAdmin() && $actor->isNot($target);
    }
}
