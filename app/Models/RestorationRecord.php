<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestorationRecord extends Model
{
    use HasFactory;

    public const CATEGORY_CLEANING       = 'cleaning';
    public const CATEGORY_CONSERVATION   = 'conservation';
    public const CATEGORY_REPAIR         = 'repair';
    public const CATEGORY_ANALYSIS       = 'analysis';
    public const CATEGORY_DOCUMENTATION  = 'documentation';
    public const CATEGORY_EXAMINATION    = 'examination';
    public const CATEGORY_OTHER          = 'other';

    public const CATEGORIES = [
        self::CATEGORY_CLEANING      => 'Cleaning',
        self::CATEGORY_CONSERVATION  => 'Conservation',
        self::CATEGORY_REPAIR        => 'Repair',
        self::CATEGORY_ANALYSIS      => 'Analysis',
        self::CATEGORY_DOCUMENTATION => 'Documentation',
        self::CATEGORY_EXAMINATION   => 'Examination',
        self::CATEGORY_OTHER         => 'Other',
    ];

    protected $fillable = [
        'artifact_id',
        'recorded_by',
        'category',
        'title',
        'description',
        'conservator_name',
        'institution',
        'started_at',
        'completed_at',
        'artifact_document_id',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'started_at'   => 'date',
            'completed_at' => 'date',
            'sort_order'   => 'integer',
        ];
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(ArtifactDocument::class, 'artifact_document_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    public function categoryIcon(): string
    {
        return match ($this->category) {
            self::CATEGORY_CLEANING      => '🧹',
            self::CATEGORY_CONSERVATION  => '🛡',
            self::CATEGORY_REPAIR        => '🔧',
            self::CATEGORY_ANALYSIS      => '🔬',
            self::CATEGORY_DOCUMENTATION => '📋',
            self::CATEGORY_EXAMINATION   => '🔍',
            default                      => '📌',
        };
    }

    /**
     * Returns a human-readable duration string if both dates are present.
     * e.g. "Mar 2024 – Jun 2024"
     */
    public function durationLabel(): ?string
    {
        if (! $this->started_at) {
            return null;
        }

        $start = $this->started_at->format('M Y');

        if (! $this->completed_at) {
            return "Started {$start}";
        }

        if ($this->started_at->isSameMonth($this->completed_at)) {
            return $this->started_at->format('M j') . '–' . $this->completed_at->format('j, Y');
        }

        return $start . ' – ' . $this->completed_at->format('M Y');
    }
}
