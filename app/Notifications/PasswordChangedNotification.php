<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
            ->subject('Your Artevo password was changed')
            ->greeting("Hi {$notifiable->name},")
            ->line('This is a confirmation that your password was just changed.')
            ->line("If you didn't make this change, please contact us immediately.")
            ->action('Contact support', route('contact'));
    }

    /**
     * @return array<string, string>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Password changed',
            'body' => 'Your password was changed on ' . now()->format('M j, Y \a\t g:i A') . '.',
            'url' => route('profile.edit'),
        ];
    }
}
