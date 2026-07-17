<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuratorNote extends Model
{
    use HasFactory;

    public const TYPE_OBSERVATION      = 'observation';
    public const TYPE_CONDITION_CHECK  = 'condition-check';
    public const TYPE_RECOMMENDATION   = 'recommendation';
    public const TYPE_ADMIN_NOTE       = 'admin-note';

    public const TYPES = [
        self::TYPE_OBSERVATION     => 'Observation',
        self::TYPE_CONDITION_CHECK => 'Condition Check',
        self::TYPE_RECOMMENDATION  => 'Recommendation',
        self::TYPE_ADMIN_NOTE      => 'Admin Note',
    ];

    protected $fillable = [
        'artifact_id',
        'author_id',
        'note_type',
        'body',
        'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
        ];
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function typeLabel(): string
    {
        return self::TYPES[$this->note_type] ?? ucfirst($this->note_type);
    }

    /**
     * Returns a CSS-variable-safe colour name for the note type badge.
     */
    public function typeColor(): string
    {
        return match ($this->note_type) {
            self::TYPE_CONDITION_CHECK => 'warning',
            self::TYPE_RECOMMENDATION  => 'success',
            self::TYPE_ADMIN_NOTE      => 'danger',
            default                    => 'muted',
        };
    }

    /**
     * Returns the hex accent used for the left-border stripe on each note card.
     */
    public function typeBorderColor(): string
    {
        return match ($this->note_type) {
            self::TYPE_CONDITION_CHECK => '#d97706',
            self::TYPE_RECOMMENDATION  => '#10b981',
            self::TYPE_ADMIN_NOTE      => '#ef4444',
            default                    => 'var(--color-border)',
        };
    }
}
