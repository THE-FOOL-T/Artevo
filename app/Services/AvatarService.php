<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Handles storing a new profile avatar and cleaning up the previous file,
 * so ProfileController stays focused on HTTP concerns.
 */
class AvatarService
{
    /**
     * Store the uploaded avatar for the given user, deleting whatever
     * avatar they had before, and return the new storage path.
     */
    public function replace(User $user, UploadedFile $file): string
    {
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        return $file->store('avatars', 'public');
    }
}
