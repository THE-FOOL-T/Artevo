<?php

namespace App\Services;

use App\Models\Artifact;
use App\Models\ArtifactTag;
use App\Models\Museum;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Everything about creating/updating an artifact's own fields and tags
 * lives here, so Curator\ArtifactController and Collector\ArtifactController
 * can share it instead of duplicating the same logic twice.
 */
class ArtifactService
{
    /**
     * Create an artifact owned by a museum (curator flow).
     *
     * @param  array<string, mixed>  $data  Validated fields from StoreArtifactRequest, minus 'tags'
     * @param  array<string>  $tagNames
     */
    public function createForMuseum(Museum $museum, User $creator, array $data, array $tagNames = []): Artifact
    {
        $artifact = Artifact::create([
            ...$data,
            'museum_id' => $museum->id,
            'collector_id' => null,
            'created_by' => $creator->id,
        ]);

        $this->syncTags($artifact, $tagNames);

        return $artifact;
    }

    /**
     * Create an artifact owned by a collector's personal collection.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string>  $tagNames
     */
    public function createForCollector(User $collector, array $data, array $tagNames = []): Artifact
    {
        $artifact = Artifact::create([
            ...$data,
            'museum_id' => null,
            'collector_id' => $collector->id,
            'created_by' => $collector->id,
        ]);

        $this->syncTags($artifact, $tagNames);

        return $artifact;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string>  $tagNames
     */
    public function update(Artifact $artifact, array $data, array $tagNames = []): Artifact
    {
        $artifact->update($data);
        $this->syncTags($artifact, $tagNames);

        return $artifact;
    }

    /**
     * Finds each tag by name (case-insensitive), creating it if it
     * doesn't exist yet, then syncs the artifact's tag list to exactly
     * that set.
     *
     * @param  array<string>  $tagNames
     */
    public function syncTags(Artifact $artifact, array $tagNames): void
    {
        $tagIds = collect($tagNames)
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->unique(fn (string $name) => Str::lower($name))
            ->map(function (string $name) {
                $tag = ArtifactTag::whereRaw('LOWER(name) = ?', [Str::lower($name)])->first()
                    ?? ArtifactTag::create(['name' => $name, 'slug' => Str::slug($name)]);

                return $tag->id;
            });

        $artifact->tags()->sync($tagIds);
    }
}
