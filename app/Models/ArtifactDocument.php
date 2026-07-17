<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ArtifactDocument extends Model
{
    use HasFactory;

    protected $fillable = ['artifact_id', 'title', 'document_path', 'document_type'];

    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }

    public function url(): string
    {
        return Storage::url($this->document_path);
    }
}
