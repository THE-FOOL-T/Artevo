<?php

namespace App\Notifications;

use App\Models\Exhibition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExhibitionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Exhibition $exhibition,
        public readonly string $type, // 'published', 'featured'
        public readonly ?string $publishedByName = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = new MailMessage;

        switch ($this->type) {
            case 'published':
                return $mail
                    ->subject("Your exhibition \"{$this->exhibition->name}\" is now live — Artevo")
                    ->greeting("Great news!")
                    ->line("\"{$this->exhibition->name}\" has been published by {$this->publishedByName} and is now visible to all visitors.")
                    ->action('View Exhibition', route('exhibitions.show', $this->exhibition))
                    ->line('Thank you for contributing to Artevo.');

            case 'featured':
                return $mail
                    ->subject("Your exhibition has been featured — Artevo")
                    ->greeting("Congratulations!")
                    ->line("\"{$this->exhibition->name}\" has been selected as a featured exhibition on Artevo and will appear prominently on the exhibitions page.")
                    ->action('View Exhibition', route('exhibitions.show', $this->exhibition))
                    ->line('Thank you for your contribution to Artevo.');
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return match ($this->type) {
            'published' => [
                'type'           => 'exhibition_published',
                'exhibition_id'  => $this->exhibition->id,
                'exhibition_name'=> $this->exhibition->name,
                'published_by'   => $this->publishedByName,
                'link'           => route('exhibitions.show', $this->exhibition),
            ],
            'featured' => [
                'type'            => 'exhibition_featured',
                'exhibition_id'   => $this->exhibition->id,
                'exhibition_name' => $this->exhibition->name,
                'link'            => route('exhibitions.show', $this->exhibition),
            ],
            default => [],
        };
    }
}
