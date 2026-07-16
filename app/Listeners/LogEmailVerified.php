<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Verified;

class LogEmailVerified
{
    public function __construct(private ActivityLogger $activityLogger)
    {
    }

    public function handle(Verified $event): void
    {
        $this->activityLogger->log(
            action: 'user.email-verified',
            description: "{$event->user->name} verified their email address.",
            user: $event->user,
        );
    }
}
