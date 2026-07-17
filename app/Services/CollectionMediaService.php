<?php

namespace App\Services;

use App\Models\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Handles upload and deletion of a collection's cover image.
 * Follows the exact same pattern as MuseumMediaService so the approach
 * stays consistent across the entire platform.
 */
class CollectionMediaService
{
    /**
     * Store a new cover image, deleting the previous one if it exists.
     * Returns the stored path (stored in collections.cover_image_path).
     */
    public function replaceCoverImage(Collection $collection, UploadedFile $file): string
    {
        if ($collection->cover_image_path) {
            Storage::disk('public')->delete($collection->cover_image_path);
        }

        return $file->store('collections/covers', 'public');
    }

    /**
     * Delete the current cover image from disk and clear the path column.
     */
    public function deleteCoverImage(Collection $collection): void
    {
        if ($collection->cover_image_path) {
            Storage::disk('public')->delete($collection->cover_image_path);
            $collection->update(['cover_image_path' => null]);
        }
    }
}
