<?php

namespace App\Policies;

use App\Models\Donation;
use App\Models\User;

class DonationPolicy
{
    /**
     * The donor, the target museum's curator, and admins may view a donation.
     */
    public function view(User $user, Donation $donation): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // The original donor
        if ($donation->donor_id === $user->id) {
            return true;
        }

        // The curator of the receiving museum
        return $donation->museum?->curator_id === $user->id;
    }

    /**
     * Only admins may approve or reject donations.
     */
    public function review(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only admins may trigger the final ownership transfer.
     */
    public function transfer(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * A donor may cancel their own request while it is still pending.
     */
    public function cancel(User $user, Donation $donation): bool
    {
        return $donation->donor_id === $user->id && $donation->isPending();
    }
}
