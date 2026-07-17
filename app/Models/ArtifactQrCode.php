<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArtifactQrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'artifact_id',
        'token',
        'generation',
        'scan_count',
        'last_scanned_at',
    ];

    protected function casts(): array
    {
        return [
            'last_scanned_at' => 'datetime',
            'scan_count'      => 'integer',
            'generation'      => 'integer',
        ];
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(QrScan::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * The public-facing URL that gets encoded into the QR image.
     */
    public function scanUrl(): string
    {
        return route('qr.scan', $this->token);
    }

    /**
     * Whether the provided generation number matches the current one.
     * If not, the QR on the physical label is stale.
     */
    public function isCurrentGeneration(int $generation): bool
    {
        return $this->generation === $generation;
    }
}
