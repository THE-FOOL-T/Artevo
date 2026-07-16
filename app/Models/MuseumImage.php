<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MuseumImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'museum_id',
        'image_path',
        'caption',
        'sort_order',
    ];

    public function museum(): BelongsTo
    {
        return $this->belongsTo(Museum::class);
    }

    public function url(): string
    {
        return Storage::url($this->image_path);
    }
}
