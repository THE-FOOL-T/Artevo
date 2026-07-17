<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Artifact extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PUBLIC   = 'public';
    public const STATUS_PRIVATE  = 'private';
    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [self::STATUS_PUBLIC, self::STATUS_PRIVATE, self::STATUS_ARCHIVED];

    // ─── Phase 11: Verification ───────────────────────────────────────────────
    public const VERIFICATION_UNVERIFIED = 'unverified';
    public const VERIFICATION_PENDING    = 'pending';
    public const VERIFICATION_VERIFIED   = 'verified';
    public const VERIFICATION_REJECTED   = 'rejected';

    public const VERIFICATION_STATUSES = [
        self::VERIFICATION_UNVERIFIED,
        self::VERIFICATION_PENDING,
        self::VERIFICATION_VERIFIED,
        self::VERIFICATION_REJECTED,
    ];

    protected $fillable = [
        'created_by',
        'museum_id',
        'collector_id',
        'category_id',
        'material_id',
        'name',
        'slug',
        'artifact_code',
        'short_description',
        'description',
        'civilization',
        'era',
        'century',
        'country_of_origin',
        'region',
        'discovery_location',
        'language',
        'dimensions',
        'weight',
        'condition',
        'estimated_value',
        'status',
        // Phase 11
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_note',
    ];

    protected function casts(): array
    {
        return [
            'estimated_value' => 'decimal:2',
            'verified_at'     => 'datetime',
        ];
    }

    /**
     * Generates a unique slug from the name (same collision-safe pattern
     * as Museum), and — once the row has an ID — a human-friendly
     * accession code like "ART-000042".
     */
    protected static function booted(): void
    {
        static::saving(function (Artifact $artifact) {
            if ($artifact->slug && ! $artifact->isDirty('name')) {
                return;
            }

            $base = Str::slug($artifact->name);
            $slug = $base;
            $suffix = 1;

            while (
                static::withTrashed()
                    ->where('slug', $slug)
                    ->when($artifact->exists, fn ($query) => $query->whereKeyNot($artifact->getKey()))
                    ->exists()
            ) {
                $slug = "{$base}-" . ++$suffix;
            }

            $artifact->slug = $slug;
        });

        static::created(function (Artifact $artifact) {
            if (! $artifact->artifact_code) {
                $artifact->updateQuietly([
                    'artifact_code' => 'ART-' . str_pad((string) $artifact->id, 6, '0', STR_PAD_LEFT),
                ]);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function museum(): BelongsTo
    {
        return $this->belongsTo(Museum::class);
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArtifactCategory::class, 'category_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(ArtifactMaterial::class, 'material_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ArtifactTag::class, 'artifact_tag', 'artifact_id', 'tag_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ArtifactImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ArtifactDocument::class);
    }

    /**
     * Collections that contain this artifact (Phase 9).
     * Inverse of Collection::artifacts().
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_artifact')
            ->withPivot('sort_order')
            ->orderBy('collection_artifact.sort_order');
    }

    /**
     * Exhibition sections that include this artifact (Phase 10).
     * Inverse of ExhibitionSection::artifacts().
     */
    public function exhibitionSections(): BelongsToMany
    {
        return $this->belongsToMany(
            ExhibitionSection::class,
            'exhibition_section_artifact',
            'artifact_id',
            'exhibition_section_id'
        )->withPivot('sort_order');
    }

    /**
     * The admin who verified or rejected this artifact (Phase 11).
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Ordered provenance records for this artifact (Phase 11).
     */
    public function provenance(): HasMany
    {
        return $this->hasMany(ArtifactProvenance::class)->orderBy('sort_order');
    }

    /**
     * Private curator notes — pinned first, then newest first (Phase 12).
     */
    public function curatorNotes(): HasMany
    {
        return $this->hasMany(CuratorNote::class)
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at');
    }

    /**
     * Public restoration/conservation records — sorted by sort_order (Phase 12).
     */
    public function restorationRecords(): HasMany
    {
        return $this->hasMany(RestorationRecord::class)->orderBy('sort_order');
    }

    public function primaryImage(): ?ArtifactImage
    {
        return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
    }

    /**
     * True if this artifact belongs to a museum (curator-managed) rather
     * than a collector's personal collection. Exactly one of
     * museum_id/collector_id is ever set — enforced in ArtifactService.
     */
    public function isMuseumArtifact(): bool
    {
        return $this->museum_id !== null;
    }

    /**
     * The user who effectively owns this artifact for authorization
     * purposes: the museum's curator, or the collector themselves.
     */
    public function ownerId(): ?int
    {
        return $this->isMuseumArtifact() ? $this->museum?->curator_id : $this->collector_id;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->ownerId() === $user->id;
    }

    public function isPublic(): bool
    {
        return $this->status === self::STATUS_PUBLIC;
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLIC);
    }

    // ─── Phase 11: Verification helpers ──────────────────────────────────────

    public function isVerified(): bool
    {
        return $this->verification_status === self::VERIFICATION_VERIFIED;
    }

    public function isPendingVerification(): bool
    {
        return $this->verification_status === self::VERIFICATION_PENDING;
    }

    public function isVerificationRejected(): bool
    {
        return $this->verification_status === self::VERIFICATION_REJECTED;
    }

    public function verificationLabel(): string
    {
        return match ($this->verification_status) {
            self::VERIFICATION_VERIFIED   => 'Verified',
            self::VERIFICATION_PENDING    => 'Pending Review',
            self::VERIFICATION_REJECTED   => 'Not Verified',
            default                       => 'Unverified',
        };
    }

    /**
     * CSS-friendly color class name for the verification badge.
     */
    public function verificationBadgeColor(): string
    {
        return match ($this->verification_status) {
            self::VERIFICATION_VERIFIED => 'success',
            self::VERIFICATION_PENDING  => 'warning',
            self::VERIFICATION_REJECTED => 'danger',
            default                     => 'muted',
        };
    }

    /**
     * Pretty URLs use the slug.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
