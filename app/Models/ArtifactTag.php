<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ArtifactTag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function artifacts(): BelongsToMany
    {
        return $this->belongsToMany(Artifact::class, 'artifact_tag', 'tag_id', 'artifact_id');
    }
}
