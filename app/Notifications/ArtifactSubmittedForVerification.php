<?php

namespace App\Notifications;

use App\Models\Artifact;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Database-only notification sent to every admin when an artifact owner
 * submits their artifact for verification review.
 */
class ArtifactSubmittedForVerification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Artifact $artifact,
        public readonly User     $submittedBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'            => 'artifact_submitted_for_verification',
            'artifact_id'     => $this->artifact->id,
            'artifact_name'   => $this->artifact->name,
            'artifact_code'   => $this->artifact->artifact_code,
            'submitted_by'    => $this->submittedBy->name,
            'link'            => route('admin.artifact-verifications.index'),
        ];
    }
}
