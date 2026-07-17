<?php

namespace App\Services;

use App\Models\Artifact;
use App\Models\ArtifactProvenance;
use App\Models\User;

class ArtifactProvenanceService
{
    /**
     * Add a provenance record to an artifact, appending it at the end.
     *
     * @param  array<string, mixed>  $data  Validated fields from StoreArtifactProvenanceRequest
     */
    public function addRecord(Artifact $artifact, User $recorder, array $data): ArtifactProvenance
    {
        $maxOrder = $artifact->provenance()->max('sort_order') ?? -1;

        return $artifact->provenance()->create([
            ...$data,
            'recorded_by' => $recorder->id,
            'sort_order'  => $maxOrder + 1,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateRecord(ArtifactProvenance $record, array $data): ArtifactProvenance
    {
        $record->update($data);

        return $record->fresh();
    }

    public function deleteRecord(ArtifactProvenance $record): void
    {
        $record->delete();
    }

    /**
     * Persist a new curator-defined display order for all provenance records.
     *
     * @param  int[]  $orderedIds
     */
    public function reorder(Artifact $artifact, array $orderedIds): void
    {
        foreach ($orderedIds as $position => $recordId) {
            $artifact->provenance()
                ->where('id', (int) $recordId)
                ->update(['sort_order' => $position]);
        }
    }
}
