<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auction extends Model
{
    use HasFactory;

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_CLOSED    = 'closed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_ACTIVE,
        self::STATUS_CLOSED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'artifact_id',
        'created_by',
        'winner_id',
        'status',
        'title',
        'description',
        'currency',
        'reserve_price',
        'current_price',
        'bid_increment',
        'starts_at',
        'ends_at',
        'bids_count',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'reserve_price' => 'decimal:2',
            'current_price' => 'decimal:2',
            'bid_increment' => 'decimal:2',
            'starts_at'     => 'datetime',
            'ends_at'       => 'datetime',
            'bids_count'    => 'integer',
            'views_count'   => 'integer',
        ];
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    /**
     * All bids, newest first.
     */
    public function bids(): HasMany
    {
        return $this->hasMany(AuctionBid::class)->orderByDesc('created_at');
    }

    /**
     * The current winning (highest) bid.
     */
    public function winningBid(): HasMany
    {
        return $this->hasMany(AuctionBid::class)->where('is_winning', true);
    }

    /**
     * Users watching this auction.
     */
    public function watchers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'auction_watchers')
            ->withTimestamps();
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * "Open" = active AND the bidding window is currently open.
     */
    public function scopeOpen(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_CLOSED]);
    }

    // ─── Status helpers ───────────────────────────────────────────────────────

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Active AND the time window is currently open.
     */
    public function isOpen(): bool
    {
        if (! $this->isActive()) {
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

    /**
     * Seconds remaining until the auction closes. Returns 0 if already expired.
     */
    public function remainingSeconds(): int
    {
        if (! $this->ends_at || ! $this->isOpen()) {
            return 0;
        }

        return max(0, (int) now()->diffInSeconds($this->ends_at, false));
    }

    /**
     * The minimum next bid amount a user must place.
     */
    public function nextMinimumBid(): float
    {
        return (float) $this->current_price + (float) $this->bid_increment;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE    => 'Active',
            self::STATUS_CLOSED    => 'Closed',
            self::STATUS_CANCELLED => 'Cancelled',
            default                => 'Draft',
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE    => 'success',
            self::STATUS_CLOSED    => 'muted',
            self::STATUS_CANCELLED => 'danger',
            default                => 'warning',
        };
    }

    /**
     * Bumps the view counter without touching updated_at.
     */
    public function incrementViews(): void
    {
        $this->timestamps = false;
        $this->increment('views_count');
        $this->timestamps = true;
    }

    /**
     * Slug-based routing: auctions/{auction} resolves via artifact slug for
     * pretty URLs. We fall back to the numeric ID if no artifact is loaded.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
