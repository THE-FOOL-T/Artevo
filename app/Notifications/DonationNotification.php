<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DonationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Donation $donation,
        public string $type, // 'requested', 'approved', 'rejected', 'transferred'
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $artifact = $this->donation->artifact;
        $museum   = $this->donation->museum;
        $donor    = $this->donation->donor;

        $mail = (new MailMessage)->greeting("Hi {$notifiable->name},");

        switch ($this->type) {
            case 'requested':
                return $mail
                    ->subject("New donation request for \"{$artifact->name}\"")
                    ->line("{$donor->name} has submitted a donation request for the artifact \"{$artifact->name}\" to your museum.")
                    ->when($this->donation->message, fn ($m) => $m->line("Donor message: {$this->donation->message}"))
                    ->line('Please review the request in the admin panel.')
                    ->action('Review donation', route('admin.donations.show', $this->donation));

            case 'approved':
                return $mail
                    ->subject("Your donation of \"{$artifact->name}\" has been approved")
                    ->line("Great news! Your donation request for \"{$artifact->name}\" to {$museum->name} has been approved.")
                    ->line('The administrator will complete the ownership transfer shortly. You will receive another notification once the transfer is finalised.')
                    ->action('View donation', route('donations.show', $this->donation));

            case 'rejected':
                return $mail
                    ->subject("Donation request for \"{$artifact->name}\" was not approved")
                    ->line("Unfortunately, your donation request for \"{$artifact->name}\" to {$museum->name} has been declined.")
                    ->when($this->donation->rejection_reason, fn ($m) => $m->line("Reason: {$this->donation->rejection_reason}"))
                    ->line('If you have any questions, please contact the Artevo support team.')
                    ->action('View donation', route('donations.show', $this->donation));

            case 'transferred':
                $cert = $this->donation->certificate_number ?? '—';
                return $mail
                    ->subject("Ownership of \"{$artifact->name}\" has been transferred")
                    ->line("The ownership of \"{$artifact->name}\" has been officially transferred to {$museum->name}.")
                    ->line("Your donation certificate number is: **{$cert}**")
                    ->line('A provenance record has been automatically created for this artifact.')
                    ->action('View artifact', route('artifacts.show', $artifact));
        }

        return $mail;
    }

    /** @return array<string, string> */
    public function toArray(object $notifiable): array
    {
        $artifact = $this->donation->artifact;
        $museum   = $this->donation->museum;
        $donor    = $this->donation->donor;

        return match ($this->type) {
            'requested' => [
                'title' => "New donation request for \"{$artifact->name}\"",
                'body'  => "{$donor->name} wants to donate an artifact to your museum.",
                'url'   => route('admin.donations.show', $this->donation),
            ],
            'approved' => [
                'title' => "Donation of \"{$artifact->name}\" approved",
                'body'  => "Your donation to {$museum->name} has been approved.",
                'url'   => route('donations.show', $this->donation),
            ],
            'rejected' => [
                'title' => "Donation of \"{$artifact->name}\" was not approved",
                'body'  => "Your donation request to {$museum->name} has been declined.",
                'url'   => route('donations.show', $this->donation),
            ],
            'transferred' => [
                'title' => "Ownership of \"{$artifact->name}\" transferred",
                'body'  => "Donation certificate {$this->donation->certificate_number} issued.",
                'url'   => route('artifacts.show', $artifact),
            ],
            default => [],
        };
    }
}
