<?php

namespace App\Events;

use App\Models\Museum;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MuseumVerificationStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Museum $museum,
        public User $changedBy,
        public string $previousStatus,
        public string $newStatus,
    ) {
    }
}
