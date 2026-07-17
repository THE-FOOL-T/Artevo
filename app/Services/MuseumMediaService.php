<?php

namespace App\Services;

use App\Models\Museum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Everything to do with a museum's images — logo, cover, and gallery —
 * lives here rather than scattered across MuseumController and
 * MuseumImageController.
 */
class MuseumMediaService
{
    public function replaceLogo(Museum $museum, UploadedFile $file): string
    {
        if ($museum->logo_path) {
            Storage::disk('public')->delete($museum->logo_path);
        }

        return $file->store('museums/logos', 'public');
    }

    public function replaceCoverImage(Museum $museum, UploadedFile $file): string
    {
        if ($museum->cover_image_path) {
            Storage::disk('public')->delete($museum->cover_image_path);
        }

        return $file->store('museums/covers', 'public');
    }

    /**
     * Store one or more gallery images, appended after whatever's
     * already there.
     *
     * @param  array<UploadedFile>  $files
     */
    public function addGalleryImages(Museum $museum, array $files, ?string $caption = null): void
    {
        $nextSortOrder = (int) $museum->images()->max('sort_order') + 1;

        foreach ($files as $file) {
            $museum->images()->create([
                'image_path' => $file->store('museums/gallery', 'public'),
                'caption' => $caption,
                'sort_order' => $nextSortOrder++,
            ]);
        }
    }
}
