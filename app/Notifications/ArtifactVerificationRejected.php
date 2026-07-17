<?php

namespace App\Notifications;

use App\Models\Artifact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the artifact owner when an admin rejects their verification request.
 * Always includes the rejection note so the owner knows what to fix.
 */
class ArtifactVerificationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Artifact $artifact,
        public readonly string   $note,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Artifact verification update — Artevo")
            ->greeting('Verification update')
            ->line("Your artifact \"{$this->artifact->name}\" ({$this->artifact->artifact_code}) could not be verified at this time.")
            ->line("**Reviewer's note:** {$this->note}")
            ->line("Please update the artifact and re-submit for verification once the issues are resolved.")
            ->action('Edit Artifact', route('artifacts.show', $this->artifact))
            ->line('Thank you for your patience.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'artifact_verification_rejected',
            'artifact_id'   => $this->artifact->id,
            'artifact_name' => $this->artifact->name,
            'note'          => $this->note,
            'link'          => route('artifacts.show', $this->artifact),
        ];
    }
}
