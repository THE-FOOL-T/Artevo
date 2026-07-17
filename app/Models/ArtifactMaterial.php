<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArtifactMaterial extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function artifacts(): HasMany
    {
        return $this->hasMany(Artifact::class, 'material_id');
    }
}
