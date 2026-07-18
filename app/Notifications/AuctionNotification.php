<?php

namespace App\Notifications;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuctionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Auction $auction,
        public readonly string $type, // 'bid_placed', 'closed', 'won', 'outbid'
        public readonly ?AuctionBid $bid = null,
        public readonly ?User $bidder = null,
        public readonly ?float $newAmount = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage);

        switch ($this->type) {
            case 'bid_placed':
                $amount = number_format((float) $this->bid->amount, 2);
                return $mail
                    ->subject("New bid on \"{$this->auction->title}\" — Artevo")
                    ->greeting('You got a bid!')
                    ->line("{$this->bidder->name} placed a bid of {$this->auction->currency} {$amount} on your auction \"{$this->auction->title}\".")
                    ->line("Current highest bid: {$this->auction->currency} {$amount}")
                    ->action('View Auction', route('auctions.show', $this->auction))
                    ->line('Good luck with your auction!');

            case 'closed':
                $mail->subject("Your auction \"{$this->auction->title}\" has closed — Artevo");
                if ($this->bid && $this->bid->bidder) {
                    $amount     = number_format((float) $this->bid->amount, 2);
                    $winnerName = $this->bid->bidder->name;
                    $mail->greeting('Your auction has ended!')
                         ->line("Your auction \"{$this->auction->title}\" received {$this->auction->bids_count} bids and closed at {$this->auction->currency} {$amount}.")
                         ->line("Winner: {$winnerName}. Please reach out to arrange the handover.");
                } else {
                    $mail->greeting('Your auction has ended.')
                         ->line("Your auction \"{$this->auction->title}\" closed with no bids.");
                }
                return $mail->action('View Auction', route('auctions.show', $this->auction))
                            ->line('Thank you for using Artevo Auctions.');

            case 'won':
                $amount = number_format((float) $this->auction->current_price, 2);
                return $mail
                    ->subject("You won the auction for \"{$this->auction->title}\" — Artevo")
                    ->greeting('Congratulations — you won!')
                    ->line("Your bid of {$this->auction->currency} {$amount} was the highest on \"{$this->auction->title}\".")
                    ->line('The auction owner will contact you to arrange the next steps.')
                    ->action('View Auction', route('auctions.show', $this->auction))
                    ->line('Thank you for participating on Artevo.');

            case 'outbid':
                $formattedAmount = number_format($this->newAmount, 2);
                return $mail
                    ->subject("You've been outbid on \"{$this->auction->title}\" — Artevo")
                    ->greeting('Heads up!')
                    ->line("Someone placed a higher bid of {$this->auction->currency} {$formattedAmount} on \"{$this->auction->title}\".")
                    ->line('Don\'t give up — place a higher bid before the auction closes.')
                    ->action('Bid Again', route('auctions.show', $this->auction))
                    ->line("Time remaining: approximately " . gmdate('H:i:s', $this->auction->remainingSeconds()));
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return match ($this->type) {
            'bid_placed' => [
                'type'          => 'auction_bid_placed',
                'auction_id'    => $this->auction->id,
                'auction_title' => $this->auction->title,
                'bid_amount'    => $this->bid->amount,
                'bidder_name'   => $this->bidder->name,
                'link'          => route('auctions.show', $this->auction),
            ],
            'closed' => [
                'type'           => 'auction_closed',
                'auction_id'     => $this->auction->id,
                'auction_title'  => $this->auction->title,
                'winning_amount' => $this->bid?->amount,
                'winner_name'    => $this->bid?->bidder?->name,
                'link'           => route('auctions.show', $this->auction),
            ],
            'won' => [
                'type'           => 'auction_won',
                'auction_id'     => $this->auction->id,
                'auction_title'  => $this->auction->title,
                'winning_amount' => $this->auction->current_price,
                'link'           => route('auctions.show', $this->auction),
            ],
            'outbid' => [
                'type'           => 'user_outbid',
                'auction_id'     => $this->auction->id,
                'auction_title'  => $this->auction->title,
                'new_amount'     => $this->newAmount,
                'link'           => route('auctions.show', $this->auction),
            ],
            default => [],
        };
    }
}
