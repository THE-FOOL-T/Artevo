<?php

namespace App\Notifications;

use App\Models\Artifact;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArtifactVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Artifact $artifact,
        public readonly string $type, // 'submitted', 'verified', 'rejected'
        public readonly ?User $submittedBy = null,
        public readonly ?string $verifiedByName = null,
        public readonly ?string $note = null,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->type === 'submitted' ? ['database'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = new MailMessage;

        switch ($this->type) {
            case 'verified':
                return $mail
                    ->subject("Your artifact has been verified — Artevo")
                    ->greeting('Congratulations!')
                    ->line("\"{$this->artifact->name}\" ({$this->artifact->artifact_code}) has been reviewed and verified by {$this->verifiedByName}.")
                    ->line('A verified badge is now displayed on your artifact\'s public page, increasing visitor trust.')
                    ->action('View Artifact', route('artifacts.show', $this->artifact))
                    ->line('Thank you for contributing to Artevo.');

            case 'rejected':
                return $mail
                    ->subject("Artifact verification update — Artevo")
                    ->greeting('Verification update')
                    ->line("Your artifact \"{$this->artifact->name}\" ({$this->artifact->artifact_code}) could not be verified at this time.")
                    ->line("**Reviewer's note:** {$this->note}")
                    ->line("Please update the artifact and re-submit for verification once the issues are resolved.")
                    ->action('Edit Artifact', route('artifacts.show', $this->artifact))
                    ->line('Thank you for your patience.');
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return match ($this->type) {
            'submitted' => [
                'type'            => 'artifact_submitted_for_verification',
                'artifact_id'     => $this->artifact->id,
                'artifact_name'   => $this->artifact->name,
                'artifact_code'   => $this->artifact->artifact_code,
                'submitted_by'    => $this->submittedBy?->name,
                'link'            => route('admin.artifact-verifications.index'),
            ],
            'verified' => [
                'type'          => 'artifact_verified',
                'artifact_id'   => $this->artifact->id,
                'artifact_name' => $this->artifact->name,
                'verified_by'   => $this->verifiedByName,
                'link'          => route('artifacts.show', $this->artifact),
            ],
            'rejected' => [
                'type'          => 'artifact_verification_rejected',
                'artifact_id'   => $this->artifact->id,
                'artifact_name' => $this->artifact->name,
                'note'          => $this->note,
                'link'          => route('artifacts.show', $this->artifact),
            ],
            default => [],
        };
    }
}
