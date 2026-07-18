<?php

namespace App\Services;

use App\Models\Artifact;
use App\Models\Auction;
use App\Models\User;
use App\Notifications\AuctionNotification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AuctionService
{
    /**
     * Create a draft auction for an artifact.
     *
     * @param  array<string, mixed>  $data  Validated from StoreAuctionRequest
     */
    public function create(Artifact $artifact, User $creator, array $data): Auction
    {
        return Auction::create([
            ...$data,
            'artifact_id'   => $artifact->id,
            'created_by'    => $creator->id,
            'current_price' => $data['reserve_price'],
            'status'        => Auction::STATUS_DRAFT,
        ]);
    }

    /**
     * Transition a draft auction to active.
     */
    public function publish(Auction $auction): Auction
    {
        if (! $auction->isDraft()) {
            throw new RuntimeException('Only draft auctions can be published.');
        }

        $auction->update(['status' => Auction::STATUS_ACTIVE]);

        return $auction->fresh();
    }

    /**
     * Close an active auction: set winner, notify parties.
     */
    public function close(Auction $auction): Auction
    {
        if ($auction->isClosed() || $auction->isCancelled()) {
            throw new RuntimeException('Auction is already closed or cancelled.');
        }

        $winningBid = $auction->bids()->where('is_winning', true)->first();

        $auction->update([
            'status'    => Auction::STATUS_CLOSED,
            'winner_id' => $winningBid?->user_id,
        ]);

        // Notify winner
        if ($winningBid && $winningBid->bidder) {
            $winningBid->bidder->notify(new AuctionNotification(
                auction: $auction,
                type: 'won'
            ));
        }

        // Notify creator
        if ($auction->creator) {
            $auction->creator->notify(new AuctionNotification(
                auction: $auction,
                type: 'closed',
                bid: $winningBid
            ));
        }

        return $auction->fresh();
    }

    /**
     * Cancel a draft or active auction (no bids allowed — enforced by policy).
     */
    public function cancel(Auction $auction): Auction
    {
        $auction->update(['status' => Auction::STATUS_CANCELLED]);

        return $auction->fresh();
    }

    /**
     * Bump view count without touching updated_at.
     */
    public function incrementViews(Auction $auction): void
    {
        $auction->timestamps = false;
        $auction->increment('views_count');
        $auction->timestamps = true;
    }
}
