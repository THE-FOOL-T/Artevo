<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    /**
     * Activity logs are written once by ActivityLogger and never edited
     * by a user-facing form, so every column is fillable rather than
     * maintaining a duplicate allowlist here.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The record this action was performed on, once Phase 5+ starts
     * attaching logs to real subjects (Artifact, Auction, Museum...).
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
