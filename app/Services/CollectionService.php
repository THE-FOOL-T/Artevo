<?php

namespace App\Services;

use App\Models\Artifact;
use App\Models\Collection;
use App\Models\Museum;
use App\Models\User;

/**
 * All business logic for creating, updating, and managing the artifact
 * membership of Collections lives here — thin controllers inject this
 * service and call it instead of embedding logic themselves.
 */
class CollectionService
{
    /**
     * Create a collection owned by a museum (curator flow).
     *
     * @param  array<string, mixed>  $data  Validated fields from StoreCollectionRequest
     */
    public function createForMuseum(Museum $museum, User $creator, array $data): Collection
    {
        return Collection::create([
            ...$data,
            'museum_id'    => $museum->id,
            'collector_id' => null,
            'created_by'   => $creator->id,
        ]);
    }

    /**
     * Create a collection owned by a collector (personal collection).
     *
     * @param  array<string, mixed>  $data
     */
    public function createForCollector(User $collector, array $data): Collection
    {
        return Collection::create([
            ...$data,
            'museum_id'    => null,
            'collector_id' => $collector->id,
            'created_by'   => $collector->id,
        ]);
    }

    /**
     * Update an existing collection's core fields.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Collection $collection, array $data): Collection
    {
        $collection->update($data);

        return $collection->fresh();
    }

    /**
     * Add a single artifact to a collection.
     * Silently does nothing if the artifact is already in the collection
     * (the pivot's composite PK prevents duplicates at the DB level).
     */
    public function addArtifact(Collection $collection, Artifact $artifact): void
    {
        if ($collection->artifacts()->where('artifact_id', $artifact->id)->doesntExist()) {
            // Place the new artifact at the end of the current ordering.
            $maxOrder = $collection->artifacts()->max('collection_artifact.sort_order') ?? -1;

            $collection->artifacts()->attach($artifact->id, [
                'sort_order' => $maxOrder + 1,
            ]);
        }
    }

    /**
     * Remove a single artifact from a collection.
     */
    public function removeArtifact(Collection $collection, Artifact $artifact): void
    {
        $collection->artifacts()->detach($artifact->id);
    }

    /**
     * Re-order the artifacts in a collection.
     * Accepts an ordered array of artifact IDs; the position in the array
     * becomes the new sort_order value on the pivot.
     *
     * @param  int[]  $orderedArtifactIds
     */
    public function reorderArtifacts(Collection $collection, array $orderedArtifactIds): void
    {
        foreach ($orderedArtifactIds as $position => $artifactId) {
            $collection->artifacts()->updateExistingPivot((int) $artifactId, [
                'sort_order' => $position,
            ]);
        }
    }

    /**
     * Toggle a user's favorite on a collection.
     * Returns true if the collection is now favorited, false if it was unfavorited.
     */
    public function toggleFavorite(User $user, Collection $collection): bool
    {
        $isFavorited = $collection->favoritedBy()->where('user_id', $user->id)->exists();

        if ($isFavorited) {
            $collection->favoritedBy()->detach($user->id);

            return false;
        }

        $collection->favoritedBy()->attach($user->id);

        return true;
    }
}
