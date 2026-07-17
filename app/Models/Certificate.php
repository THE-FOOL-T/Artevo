<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    public const TYPE_VERIFICATION       = 'verification';
    public const TYPE_DONATION_TRANSFER  = 'donation_transfer';

    public const TYPES = [
        self::TYPE_VERIFICATION,
        self::TYPE_DONATION_TRANSFER,
    ];

    protected $fillable = [
        'artifact_id',
        'issued_to',
        'issued_by',
        'type',
        'serial',
        'reference_type',
        'reference_id',
        'notes',
        'revoked_at',
        'revocation_reason',
    ];

    protected function casts(): array
    {
        return [
            'revoked_at' => 'datetime',
        ];
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_to');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isValid(): bool
    {
        return ! $this->isRevoked();
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_DONATION_TRANSFER => 'Certificate of Ownership Transfer',
            default                      => 'Certificate of Authenticity',
        };
    }

    public function typeIcon(): string
    {
        return match ($this->type) {
            self::TYPE_DONATION_TRANSFER => '🤝',
            default                      => '🏛',
        };
    }

    /**
     * The public verification URL for this certificate.
     */
    public function verificationUrl(): string
    {
        return route('certificates.verify', $this->serial);
    }

    /**
     * The route key — serial is cleaner than an integer ID in public URLs.
     */
    public function getRouteKeyName(): string
    {
        return 'serial';
    }
}
