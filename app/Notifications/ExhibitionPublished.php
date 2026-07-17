<?php

namespace App\Notifications;

use App\Models\Exhibition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the exhibition creator (curator) when the exhibition goes live.
 * Covers both self-publish and admin-triggered publish.
 */
class ExhibitionPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Exhibition $exhibition,
        public readonly string $publishedByName,
    ) {}

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
            ->subject("Your exhibition \"{$this->exhibition->name}\" is now live — Artevo")
            ->greeting("Great news!")
            ->line("\"{$this->exhibition->name}\" has been published by {$this->publishedByName} and is now visible to all visitors.")
            ->action('View Exhibition', route('exhibitions.show', $this->exhibition))
            ->line('Thank you for contributing to Artevo.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'exhibition_published',
            'exhibition_id'  => $this->exhibition->id,
            'exhibition_name'=> $this->exhibition->name,
            'published_by'   => $this->publishedByName,
            'link'           => route('exhibitions.show', $this->exhibition),
        ];
    }
}
