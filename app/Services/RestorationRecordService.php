<?php

namespace App\Services;

use App\Models\Artifact;
use App\Models\RestorationRecord;
use App\Models\User;

class RestorationRecordService
{
    /**
     * Add a restoration record, appending it at the end of the list.
     *
     * @param  array<string, mixed>  $data  Validated from StoreRestorationRecordRequest
     */
    public function addRecord(Artifact $artifact, User $recorder, array $data): RestorationRecord
    {
        $maxOrder = $artifact->restorationRecords()->max('sort_order') ?? -1;

        return $artifact->restorationRecords()->create([
            ...$data,
            'recorded_by' => $recorder->id,
            'sort_order'  => $maxOrder + 1,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateRecord(RestorationRecord $record, array $data): RestorationRecord
    {
        $record->update($data);

        return $record->fresh();
    }

    public function deleteRecord(RestorationRecord $record): void
    {
        $record->delete();
    }

    /**
     * Persist a curator-defined display order for all restoration records.
     *
     * @param  int[]  $orderedIds
     */
    public function reorder(Artifact $artifact, array $orderedIds): void
    {
        foreach ($orderedIds as $position => $recordId) {
            $artifact->restorationRecords()
                ->where('id', (int) $recordId)
                ->update(['sort_order' => $position]);
        }
    }
}
