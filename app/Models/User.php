<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The four platform roles. Administrator and Curator are assigned by
     * an existing administrator (see Admin\UserController) — they are
     * never selectable at registration. Visitor is the default for every
     * new account; a visitor can self-upgrade to Collector at any time
     * (see RoleUpgradeController).
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_CURATOR = 'curator';
    public const ROLE_COLLECTOR = 'collector';
    public const ROLE_VISITOR = 'visitor';

    /**
     * All valid roles, in the order they should appear in admin UI
     * dropdowns — most-privileged first.
     */
    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_CURATOR,
        self::ROLE_COLLECTOR,
        self::ROLE_VISITOR,
    ];

    /**
     * Mass-assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
        'role',
    ];

    /**
     * Attributes hidden from array/JSON representations (API responses,
     * debug dumps) — never expose the password hash or remember token.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Public URL for the user's uploaded avatar, or null if they haven't
     * uploaded one — callers fall back to the initials avatar in that case
     * (see the `<x-avatar>` component).
     */
    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? Storage::url($this->avatar_path) : null;
    }

    /**
     * Up to two initials derived from the user's name, used by the
     * initials-avatar fallback (e.g. "Amelia Hart" -> "AH").
     */
    public function initials(): string
    {
        $initials = Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->map(fn (string $word) => Str::substr($word, 0, 1))
            ->take(2)
            ->implode('');

        return Str::upper($initials) ?: 'A';
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isCurator(): bool
    {
        return $this->role === self::ROLE_CURATOR;
    }

    public function isCollector(): bool
    {
        return $this->role === self::ROLE_COLLECTOR;
    }

    public function isVisitor(): bool
    {
        return $this->role === self::ROLE_VISITOR;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Human-readable role label for badges/UI (e.g. "Administrator"
     * rather than the raw "admin" column value).
     */
    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_CURATOR => 'Curator',
            self::ROLE_COLLECTOR => 'Collector',
            default => 'Visitor',
        };
    }

    /**
     * A collector's personal artifact collection (as opposed to
     * artifacts belonging to a museum they curate).
     */
    public function collectedArtifacts(): HasMany
    {
        return $this->hasMany(Artifact::class, 'collector_id');
    }

    /**
     * Collections this user created as a collector (personal, not museum-linked).
     */
    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class, 'collector_id');
    }

    /**
     * Collections this user has starred/favorited.
     */
    public function favoritedCollections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_favorites')
            ->withTimestamps();
    }
}
