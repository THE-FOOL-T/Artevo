<?php

namespace App\Notifications;

use App\Models\Exhibition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the exhibition creator when an admin marks their exhibition as featured.
 */
class ExhibitionFeatured extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Exhibition $exhibition) {}

    /**
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your exhibition has been featured — Artevo")
            ->greeting("Congratulations!")
            ->line("\"{$this->exhibition->name}\" has been selected as a featured exhibition on Artevo and will appear prominently on the exhibitions page.")
            ->action('View Exhibition', route('exhibitions.show', $this->exhibition))
            ->line('Thank you for your contribution to Artevo.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'            => 'exhibition_featured',
            'exhibition_id'   => $this->exhibition->id,
            'exhibition_name' => $this->exhibition->name,
            'link'            => route('exhibitions.show', $this->exhibition),
        ];
    }
}
