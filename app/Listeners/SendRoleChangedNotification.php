<?php

namespace App\Listeners;

use App\Events\UserRoleChanged;
use App\Notifications\RoleChanged;

class SendRoleChangedNotification
{
    public function handle(UserRoleChanged $event): void
    {
        $event->target->notify(new RoleChanged(
            previousRole: $event->previousRole,
            newRole: $event->newRole,
            changedByName: $event->changedBy->name,
        ));
    }
}
