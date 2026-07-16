<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MuseumContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'museum_id',
        'label',
        'email',
        'phone',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function museum(): BelongsTo
    {
        return $this->belongsTo(Museum::class);
    }
}
