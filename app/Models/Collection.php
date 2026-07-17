<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Collection extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Visibility mirrors the Artifact pattern but is stored as a boolean
     * rather than an enum — collections are either public or private;
     * there is no "archived" state at the collection level.
     */
    protected $fillable = [
        'created_by',
        'museum_id',
        'collector_id',
        'name',
        'slug',
        'description',
        'cover_image_path',
        'is_public',
        'is_featured',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'is_public'   => 'boolean',
            'is_featured' => 'boolean',
            'views_count' => 'integer',
        ];
    }

    /**
     * Auto-generates a unique slug from the name whenever the name is new
     * or has changed — same collision-safe loop used by Artifact and Museum.
     */
    protected static function booted(): void
    {
        static::saving(function (Collection $collection) {
            if ($collection->slug && ! $collection->isDirty('name')) {
                return;
            }

            $base   = Str::slug($collection->name);
            $slug   = $base;
            $suffix = 1;

            while (
                static::withTrashed()
                    ->where('slug', $slug)
                    ->when($collection->exists, fn ($q) => $q->whereKeyNot($collection->getKey()))
                    ->exists()
            ) {
                $slug = "{$base}-" . ++$suffix;
            }

            $collection->slug = $slug;
        });
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

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

    /**
     * The artifacts in this collection, ordered by the curator/collector's
     * chosen sort_order so drag-and-drop reordering persists.
     */
    public function artifacts(): BelongsToMany
    {
        return $this->belongsToMany(Artifact::class, 'collection_artifact')
            ->withPivot('sort_order')
            ->orderBy('collection_artifact.sort_order');
    }

    /**
     * Users who have starred/favorited this collection.
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'collection_favorites')
            ->withTimestamps();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * True if this collection belongs to a museum (curator-managed), false
     * if it belongs to a collector's personal collection.
     */
    public function isMuseumCollection(): bool
    {
        return $this->museum_id !== null;
    }

    /**
     * The user who effectively owns this collection for authorization purposes:
     * the museum's curator, or the collector themselves.
     */
    public function ownerId(): ?int
    {
        return $this->isMuseumCollection()
            ? $this->museum?->curator_id
            : $this->collector_id;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->ownerId() === $user->id;
    }

    public function isPublic(): bool
    {
        return $this->is_public;
    }

    public function coverImageUrl(): ?string
    {
        return $this->cover_image_path ? Storage::url($this->cover_image_path) : null;
    }

    /**
     * Increments the view counter without touching updated_at — same pattern
     * as Museum::incrementViews() so page-view noise doesn't appear in logs.
     */
    public function incrementViews(): void
    {
        $this->timestamps = false;
        $this->increment('views_count');
        $this->timestamps = true;
    }

    /**
     * Pretty URLs use the slug — same as Artifact and Museum.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
