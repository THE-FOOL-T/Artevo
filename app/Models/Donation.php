<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    public const STATUS_PENDING     = 'pending';
    public const STATUS_APPROVED    = 'approved';
    public const STATUS_REJECTED    = 'rejected';
    public const STATUS_TRANSFERRED = 'transferred';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_TRANSFERRED,
    ];

    protected $fillable = [
        'artifact_id',
        'donor_id',
        'museum_id',
        'reviewed_by',
        'status',
        'message',
        'rejection_reason',
        'certificate_number',
        'donated_at',
        'transferred_at',
        'provenance_note',
    ];

    protected function casts(): array
    {
        return [
            'donated_at'     => 'date',
            'transferred_at' => 'datetime',
        ];
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function museum(): BelongsTo
    {
        return $this->belongsTo(Museum::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeTransferred(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_TRANSFERRED);
    }

    // ─── Status helpers ───────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isTransferred(): bool
    {
        return $this->status === self::STATUS_TRANSFERRED;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING     => 'Pending Review',
            self::STATUS_APPROVED    => 'Approved',
            self::STATUS_REJECTED    => 'Rejected',
            self::STATUS_TRANSFERRED => 'Transferred',
            default                  => ucfirst($this->status),
        };
    }

    /**
     * CSS-friendly color token for status badges.
     */
    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING     => 'warning',
            self::STATUS_APPROVED    => 'info',
            self::STATUS_TRANSFERRED => 'success',
            self::STATUS_REJECTED    => 'danger',
            default                  => 'muted',
        };
    }

    /**
     * Returns a human-readable certificate code once the donation is
     * transferred, e.g. "DON-000042".
     */
    public function certificateCode(): ?string
    {
        return $this->certificate_number;
    }

    /**
     * Whether this donation can still be cancelled by the donor.
     */
    public function isCancellable(): bool
    {
        return $this->isPending();
    }
}
