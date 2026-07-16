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
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'opening_hours' => 'array',
            'featured' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
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
}
