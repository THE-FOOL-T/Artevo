<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MuseumResource;
use App\Models\Museum;
use Illuminate\Http\Request;

class MuseumController extends Controller
{
    public function index(Request $request)
    {
        $museums = Museum::where('verification_status', Museum::VERIFICATION_VERIFIED)
            ->paginate(15);

        return MuseumResource::collection($museums);
    }

    public function show(Museum $museum)
    {
        if (! $museum->isVerified()) {
            abort(404);
        }

        return new MuseumResource($museum);
    }
}
