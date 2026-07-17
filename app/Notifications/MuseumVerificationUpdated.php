<?php

namespace App\Notifications;

use App\Models\Museum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MuseumVerificationUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Museum $museum,
        public string $newStatus,
    ) {
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Verification update for {$this->museum->name}")
            ->greeting("Hi {$notifiable->name},");

        if ($this->newStatus === Museum::VERIFICATION_VERIFIED) {
            $mail->line("Good news — \"{$this->museum->name}\" is now a Verified Institution on Artevo.")
                ->line('The verified badge now appears on its public profile and directory listing.');
        } else {
            $mail->line("\"{$this->museum->name}\"'s verification status was changed to: {$this->newStatus}.")
                ->line('You can update the profile and it will be reviewed again.');
        }

        return $mail->action('View museum profile', route('museums.show', $this->museum));
    }

    /**
     * @return array<string, string>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Museum verification updated',
            'body' => "\"{$this->museum->name}\" is now: " . ucfirst($this->newStatus),
            'url' => route('curator.museums.edit', $this->museum),
        ];
    }
}
