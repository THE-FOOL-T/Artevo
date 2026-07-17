<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Museum extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Museum-level verification (this institution is legitimate) is
     * distinct from artifact verification (Phase 11, this specific
     * object is authentic). Only an admin can change this — see
     * MuseumPolicy::verify and Admin\MuseumVerificationController.
     */
    public const VERIFICATION_PENDING = 'pending';
    public const VERIFICATION_VERIFIED = 'verified';
    public const VERIFICATION_REJECTED = 'rejected';

    protected $fillable = [
        'curator_id',
        'name',
        'slug',
        'tagline',
        'description',
        'logo_path',
        'cover_image_path',
        'foundation_year',
        'website',
        'social_links',
        'opening_hours',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'featured',
        'verification_status',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'opening_hours' => 'array',
            'featured' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
            'views_count' => 'integer',
        ];
    }

    /**
     * Generates a unique slug from the name whenever it's missing or the
     * name changes — curators never type a slug themselves.
     */
    protected static function booted(): void
    {
        static::saving(function (Museum $museum) {
            if ($museum->slug && ! $museum->isDirty('name')) {
                return;
            }

            $base = Str::slug($museum->name);
            $slug = $base;
            $suffix = 1;

            while (
                static::where('slug', $slug)
                    ->when($museum->exists, fn ($query) => $query->whereKeyNot($museum->getKey()))
                    ->exists()
            ) {
                $slug = "{$base}-" . ++$suffix;
            }

            $museum->slug = $slug;
        });
    }

    public function curator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'curator_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(MuseumImage::class)->orderBy('sort_order');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(MuseumContact::class);
    }

    public function artifacts(): HasMany
    {
        return $this->hasMany(Artifact::class);
    }

    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }

    public function exhibitions(): HasMany
    {
        return $this->hasMany(Exhibition::class);
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? Storage::url($this->logo_path) : null;
    }

    public function coverImageUrl(): ?string
    {
        return $this->cover_image_path ? Storage::url($this->cover_image_path) : null;
    }

    /**
     * Pretty URLs use the slug (see routes/web.php's {museum:slug} bind).
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isVerified(): bool
    {
        return $this->verification_status === self::VERIFICATION_VERIFIED;
    }

    public function isPendingVerification(): bool
    {
        return $this->verification_status === self::VERIFICATION_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->verification_status === self::VERIFICATION_REJECTED;
    }

    public function verificationLabel(): string
    {
        return match ($this->verification_status) {
            self::VERIFICATION_VERIFIED => 'Verified Institution',
            self::VERIFICATION_REJECTED => 'Verification Rejected',
            default => 'Verification Pending',
        };
    }

    /**
     * A plain link to Google Maps' directions UI — a lightweight stand-in
     * for the full interactive map integration coming in Phase 16.
     */
    public function directionsUrl(): ?string
    {
        if (! $this->latitude || ! $this->longitude) {
            return null;
        }

        return "https://www.google.com/maps/dir/?api=1&destination={$this->latitude},{$this->longitude}";
    }

    /**
     * Increments the view counter without touching updated_at — a
     * page view isn't a content change, so it shouldn't look like an
     * edit in the admin activity log or the "last updated" timestamp.
     */
    public function incrementViews(): void
    {
        $this->timestamps = false;
        $this->increment('views_count');
        $this->timestamps = true;
    }
}
