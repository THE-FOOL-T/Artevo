<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtifactProvenance extends Model
{
    use HasFactory;

    protected $table = 'artifact_provenance';

    /**
     * Suggested type values — not enforced as an enum so curators can
     * enter domain-specific types without a migration.
     */
    public const SUGGESTED_TYPES = [
        'purchase'    => 'Purchase',
        'donation'    => 'Donation',
        'discovery'   => 'Discovery',
        'exhibition'  => 'Exhibition',
        'transfer'    => 'Transfer',
        'auction'     => 'Auction',
        'publication' => 'Publication',
        'other'       => 'Other',
    ];

    protected $fillable = [
        'artifact_id',
        'recorded_by',
        'type',
        'title',
        'description',
        'date',
        'location',
        'source_url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'date'       => 'date',
            'sort_order' => 'integer',
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

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Human-readable type label, falling back to a title-cased version
     * of the stored string for custom types.
     */
    public function typeLabel(): string
    {
        return self::SUGGESTED_TYPES[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Icon used in the provenance timeline for each type.
     */
    public function typeIcon(): string
    {
        return match ($this->type) {
            'purchase'    => '💰',
            'donation'    => '🎁',
            'discovery'   => '🏺',
            'exhibition'  => '🖼',
            'transfer'    => '🔄',
            'auction'     => '🔨',
            'publication' => '📖',
            default       => '📌',
        };
    }
}
