<?php

namespace App\Policies;

use App\Models\Artifact;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuctionPolicy
{
    use HandlesAuthorization;

    /**
     * Only the artifact owner (or an admin) may list it for auction.
     */
    public function create(User $user, Artifact $artifact): bool
    {
        return $user->isAdmin() || $artifact->isOwnedBy($user);
    }

    /**
     * Anyone who is authenticated and is NOT the auction creator may bid.
     */
    public function bid(User $user, Auction $auction): bool
    {
        if (! $auction->isOpen()) {
            return false;
        }

        return $user->id !== $auction->created_by;
    }

    /**
     * The creator or an admin may publish a draft auction.
     */
    public function publish(User $user, Auction $auction): bool
    {
        return $user->isAdmin() || $user->id === $auction->created_by;
    }

    /**
     * The creator or an admin may close an active auction early.
     */
    public function close(User $user, Auction $auction): bool
    {
        return $user->isAdmin() || $user->id === $auction->created_by;
    }

    /**
     * Only allow cancellation when there are no bids yet.
     */
    public function cancel(User $user, Auction $auction): bool
    {
        if ($auction->bids_count > 0) {
            return false;
        }

        return $user->isAdmin() || $user->id === $auction->created_by;
    }
}
