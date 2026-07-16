<?php

namespace App\Listeners;

use App\Events\UserRoleChanged;
use App\Services\ActivityLogger;

class LogRoleChange
{
    public function __construct(private ActivityLogger $activityLogger)
    {
    }

    public function handle(UserRoleChanged $event): void
    {
        $this->activityLogger->log(
            action: 'role.changed',
            description: "{$event->changedBy->name} changed {$event->target->name}'s role from {$event->previousRole} to {$event->newRole}.",
            subject: $event->target,
            user: $event->changedBy,
            properties: [
                'previous_role' => $event->previousRole,
                'new_role' => $event->newRole,
            ],
        );
    }
}
