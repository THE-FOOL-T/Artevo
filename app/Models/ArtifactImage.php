<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ArtifactImage extends Model
{
    use HasFactory;

    protected $fillable = ['artifact_id', 'image_path', 'caption', 'is_primary', 'sort_order'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }

    public function url(): string
    {
        if (filter_var($this->image_path, FILTER_VALIDATE_URL) || str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }
        if (str_starts_with($this->image_path, '/') || str_starts_with($this->image_path, 'images/')) {
            return asset(ltrim($this->image_path, '/'));
        }
        return Storage::url($this->image_path);
    }
}
