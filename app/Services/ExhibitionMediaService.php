<?php

namespace App\Services;

use App\Models\Exhibition;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Handles cover image storage for Exhibitions.
 * Mirrors CollectionMediaService — stores in `exhibition-covers/` under
 * the default disk, replaces existing images on update.
 */
class ExhibitionMediaService
{
    private const DISK      = 'public';
    private const DIRECTORY = 'exhibition-covers';

    /**
     * Store a new cover image (replacing any existing one) and return
     * the storage path to be persisted on the exhibition model.
     */
    public function replaceCoverImage(Exhibition $exhibition, UploadedFile $file): string
    {
        // Delete previous cover if one exists
        $this->deleteCoverImage($exhibition);

        $path = $file->store(self::DIRECTORY, self::DISK);

        return $path;
    }

    /**
     * Delete the current cover image from storage.
     * Safe to call even when no cover exists.
     */
    public function deleteCoverImage(Exhibition $exhibition): void
    {
        if ($exhibition->cover_image_path) {
            Storage::disk(self::DISK)->delete($exhibition->cover_image_path);
            $exhibition->updateQuietly(['cover_image_path' => null]);
        }
    }
}
