<?php

namespace App\Notifications;

use App\Models\Collection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the collection owner (curator or collector) when an administrator
 * marks their collection as featured, surfacing it on the public homepage.
 */
class CollectionFeatured extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Collection $collection,
        public string $featuredByName,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your collection \"{$this->collection->name}\" is now featured")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->featuredByName} has featured your collection \"{$this->collection->name}\" on Artevo.")
            ->line('It will now appear in the Featured Collections section on the public collections page.')
            ->action('View your collection', route('collections.show', $this->collection));
    }

    /** @return array<string, string> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Your collection \"{$this->collection->name}\" is now featured",
            'body'  => "{$this->featuredByName} has featured your collection on Artevo.",
            'url'   => route('collections.show', $this->collection),
        ];
    }
}
