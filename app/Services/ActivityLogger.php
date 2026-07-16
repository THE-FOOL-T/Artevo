<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Writes to the activity_logs table. Every phase that adds a real action
 * worth auditing (artifact created, auction bid, verification approved...)
 * calls this same service, so log entries stay consistent in shape no
 * matter which module produced them.
 */
class ActivityLogger
{
    /**
     * Record an activity log entry.
     *
     * @param  string  $action  Dot-namespaced action key, e.g. "user.login", "role.changed"
     * @param  string  $description  Human-readable summary shown in the admin log viewer
     * @param  Model|null  $subject  The record this action was performed on, if any
     * @param  User|null  $user  Defaults to the currently authenticated user
     * @param  array<string, mixed>  $properties  Extra structured context (e.g. old/new values)
     */
    public function log(
        string $action,
        string $description,
        ?Model $subject = null,
        ?User $user = null,
        array $properties = []
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => ($user ?? Auth::user())?->id,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => (string) Request::header('User-Agent'),
        ]);
    }
}
