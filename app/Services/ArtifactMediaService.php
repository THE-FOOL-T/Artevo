<?php

namespace App\Services;

use App\Models\Artifact;
use Illuminate\Http\UploadedFile;

/**
 * Everything to do with an artifact's images and documents lives here,
 * mirroring MuseumMediaService's pattern.
 */
class ArtifactMediaService
{
    /**
     * Store one or more gallery images. The very first image ever
     * uploaded for an artifact is automatically marked primary (used as
     * the thumbnail/hero image) so a newly created artifact never ends
     * up with a gallery but no cover shot.
     *
     * @param  array<UploadedFile>  $files
     */
    public function addImages(Artifact $artifact, array $files, ?string $caption = null): void
    {
        $hasPrimary = $artifact->images()->where('is_primary', true)->exists();
        $nextSortOrder = (int) $artifact->images()->max('sort_order') + 1;

        foreach ($files as $file) {
            $artifact->images()->create([
                'image_path' => $file->store('artifacts/gallery', 'public'),
                'caption' => $caption,
                'is_primary' => ! $hasPrimary,
                'sort_order' => $nextSortOrder++,
            ]);

            $hasPrimary = true;
        }
    }

    public function addDocument(Artifact $artifact, UploadedFile $file, string $title, ?string $type = null): void
    {
        $artifact->documents()->create([
            'title' => $title,
            'document_type' => $type,
            'document_path' => $file->store('artifacts/documents', 'public'),
        ]);
    }
}
