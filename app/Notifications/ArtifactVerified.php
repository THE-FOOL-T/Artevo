<?php

namespace App\Notifications;

use App\Models\Artifact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the artifact owner when an admin approves their verification request.
 */
class ArtifactVerified extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Artifact $artifact,
        public readonly string   $verifiedByName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your artifact has been verified — Artevo")
            ->greeting('Congratulations!')
            ->line("\"{$this->artifact->name}\" ({$this->artifact->artifact_code}) has been reviewed and verified by {$this->verifiedByName}.")
            ->line('A verified badge is now displayed on your artifact\'s public page, increasing visitor trust.')
            ->action('View Artifact', route('artifacts.show', $this->artifact))
            ->line('Thank you for contributing to Artevo.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'artifact_verified',
            'artifact_id'   => $this->artifact->id,
            'artifact_name' => $this->artifact->name,
            'verified_by'   => $this->verifiedByName,
            'link'          => route('artifacts.show', $this->artifact),
        ];
    }
}
