<?php

namespace App\Listeners;

use App\Events\MuseumVerificationStatusChanged;
use App\Services\ActivityLogger;

class LogMuseumVerificationChange
{
    public function __construct(private ActivityLogger $activityLogger)
    {
    }

    public function handle(MuseumVerificationStatusChanged $event): void
    {
        $this->activityLogger->log(
            action: 'museum.verification-changed',
            description: "{$event->changedBy->name} changed \"{$event->museum->name}\"'s verification status from {$event->previousStatus} to {$event->newStatus}.",
            subject: $event->museum,
            user: $event->changedBy,
            properties: [
                'previous_status' => $event->previousStatus,
                'new_status' => $event->newStatus,
            ],
        );
    }
}
