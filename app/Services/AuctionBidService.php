<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\User;
use App\Notifications\AuctionNotification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AuctionBidService
{
    /**
     * Place a validated bid on an open auction.
     *
     * Rules enforced here (defense-in-depth on top of the form request):
     *  1. Auction must be open (active + within time window).
     *  2. Amount must be ≥ nextMinimumBid.
     *  3. Bidder may not be the auction creator.
     *
     * Side-effects:
     *  - Previous winning bid demoted to is_winning = false.
     *  - auction.current_price + bids_count updated atomically.
     *  - Auto-extend: if < 5 min remaining, ends_at pushed 5 min forward.
     *  - Outbid notification sent to previous highest bidder.
     *  - Bid-placed notification sent to auction creator.
     */
    public function placeBid(Auction $auction, User $bidder, float $amount): AuctionBid
    {
        if (! $auction->isOpen()) {
            throw new RuntimeException('This auction is not currently accepting bids.');
        }

        if ($bidder->id === $auction->created_by) {
            throw new RuntimeException('The auction owner cannot bid on their own auction.');
        }

        $minimum = $auction->nextMinimumBid();
        if ($amount < $minimum) {
            throw new RuntimeException(
                "Bid must be at least {$auction->currency} " . number_format($minimum, 2) . '.'
            );
        }

        return DB::transaction(function () use ($auction, $bidder, $amount) {
            // Capture previous winner before we demote it
            $previousWinningBid = $auction->bids()->where('is_winning', true)->with('bidder')->first();

            // Demote all existing winning bids
            $auction->bids()->where('is_winning', true)->update(['is_winning' => false]);

            // Create new winning bid
            $bid = AuctionBid::create([
                'auction_id' => $auction->id,
                'user_id'    => $bidder->id,
                'amount'     => $amount,
                'is_winning' => true,
            ]);

            // Update auction current price and bid count atomically
            $auction->timestamps = false;
            $auction->increment('bids_count');
            $auction->timestamps = true;
            $auction->update(['current_price' => $amount]);

            // Sniper protection: extend if < 5 minutes remaining
            if ($auction->ends_at && $auction->remainingSeconds() < 300) {
                $auction->update(['ends_at' => now()->addMinutes(5)]);
            }

            // Notify previous winner they've been outbid
            $previousHighestBidder = $previousWinningBid ? $previousWinningBid->bidder : null;
            if ($previousHighestBidder && $previousHighestBidder->id !== $bidder->id) {
                $previousHighestBidder->notify(new AuctionNotification(
                    auction: $auction,
                    type: 'outbid',
                    newAmount: $amount
                ));
            }

            // Notify auction creator a bid was placed
            if ($auction->creator) {
                $auction->creator->notify(new AuctionNotification(
                    auction: $auction,
                    type: 'bid_placed',
                    bid: $bid,
                    bidder: $bidder
                ));
            }

            return $bid;
        });
    }
}
