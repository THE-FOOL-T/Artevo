<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Exhibition extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED  = 'archived';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
        self::STATUS_ARCHIVED,
    ];

    protected $fillable = [
        'museum_id',
        'created_by',
        'name',
        'slug',
        'tagline',
        'description',
        'cover_image_path',
        'status',
        'is_featured',
        'starts_at',
        'ends_at',
        'admission_fee',
        'location',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'starts_at'    => 'datetime',
            'ends_at'      => 'datetime',
            'admission_fee'=> 'decimal:2',
            'is_featured'  => 'boolean',
            'views_count'  => 'integer',
        ];
    }

    /**
     * Auto-generates a unique slug from the name on every save where the
     * name is new or has changed — same collision-safe pattern used by
     * Museum and Collection.
     */
    protected static function booted(): void
    {
        static::saving(function (Exhibition $exhibition) {
            if ($exhibition->slug && ! $exhibition->isDirty('name')) {
                return;
            }

            $base   = Str::slug($exhibition->name);
            $slug   = $base;
            $suffix = 1;

            while (
                static::withTrashed()
                    ->where('slug', $slug)
                    ->when($exhibition->exists, fn ($q) => $q->whereKeyNot($exhibition->getKey()))
                    ->exists()
            ) {
                $slug = "{$base}-" . ++$suffix;
            }

            $exhibition->slug = $slug;
        });
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function museum(): BelongsTo
    {
        return $this->belongsTo(Museum::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Sections are always loaded in curator-defined order.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(ExhibitionSection::class)->orderBy('sort_order');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    // ─── Status helpers ───────────────────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * An exhibition is "active" when it is published and the current
     * datetime falls within its optional date window. If no dates are set,
     * a published exhibition is always considered active.
     */
    public function isActive(): bool
    {
        if (! $this->isPublished()) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }

    // ─── Other helpers ────────────────────────────────────────────────────────

    public function isFree(): bool
    {
        return $this->admission_fee === null || (float) $this->admission_fee === 0.0;
    }

    public function admissionLabel(): string
    {
        return $this->isFree() ? 'Free admission' : 'USD ' . number_format((float) $this->admission_fee, 2);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_ARCHIVED  => 'Archived',
            default                => 'Draft',
        };
    }

    public function coverImageUrl(): ?string
    {
        return $this->cover_image_path ? Storage::url($this->cover_image_path) : null;
    }

    /**
     * Increments the view counter without touching updated_at — same
     * pattern as Museum::incrementViews().
     */
    public function incrementViews(): void
    {
        $this->timestamps = false;
        $this->increment('views_count');
        $this->timestamps = true;
    }

    /**
     * Slug-based routing, consistent with Museum, Artifact, and Collection.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
