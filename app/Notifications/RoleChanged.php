<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoleChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $previousRole,
        public string $newRole,
        public string $changedByName,
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
        return (new MailMessage)
            ->subject('Your Artevo role has changed')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->changedByName} changed your Artevo role from " . ucfirst($this->previousRole) . ' to ' . ucfirst($this->newRole) . '.')
            ->line('Sign in to see what this unlocks on your dashboard.')
            ->action('Go to dashboard', route('dashboard'));
    }

    /**
     * @return array<string, string>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Your role was updated',
            'body' => 'Changed from ' . ucfirst($this->previousRole) . ' to ' . ucfirst($this->newRole) . " by {$this->changedByName}.",
            'url' => route('dashboard'),
        ];
    }
}
