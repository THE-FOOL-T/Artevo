<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ExhibitionSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'exhibition_id',
        'title',
        'body',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function exhibition(): BelongsTo
    {
        return $this->belongsTo(Exhibition::class);
    }

    /**
     * Artifacts are displayed in curator-defined sort_order within the section.
     * An artifact may appear in multiple sections of the same exhibition.
     */
    public function artifacts(): BelongsToMany
    {
        return $this->belongsToMany(
            Artifact::class,
            'exhibition_section_artifact',
            'exhibition_section_id',
            'artifact_id'
        )
        ->withPivot('sort_order')
        ->orderBy('exhibition_section_artifact.sort_order');
    }
}
