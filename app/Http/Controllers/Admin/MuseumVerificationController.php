<?php

namespace App\Http\Controllers\Admin;

use App\Events\MuseumVerificationStatusChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveMuseumVerificationRequest;
use App\Models\Museum;
use Illuminate\Http\RedirectResponse;

class MuseumVerificationController extends Controller
{
    public function update(SaveMuseumVerificationRequest $request, Museum $museum): RedirectResponse
    {
        $previousStatus = $museum->verification_status;
        $newStatus = $request->validated('verification_status');

        $museum->update(['verification_status' => $newStatus]);

        if ($previousStatus !== $newStatus) {
            MuseumVerificationStatusChanged::dispatch($museum, $request->user(), $previousStatus, $newStatus);
        }

        return back()->with('success', "{$museum->name}'s verification status is now: " . ucfirst($newStatus) . '.');
    }
}
