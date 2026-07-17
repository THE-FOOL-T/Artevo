<?php

namespace App\Listeners;

use App\Events\MuseumVerificationStatusChanged;
use App\Notifications\MuseumVerificationUpdated;

class SendMuseumVerificationNotification
{
    public function handle(MuseumVerificationStatusChanged $event): void
    {
        $event->museum->curator->notify(
            new MuseumVerificationUpdated($event->museum, $event->newStatus)
        );
    }
}
