<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArtifactResource;
use App\Models\Artifact;
use Illuminate\Http\Request;

class ArtifactController extends Controller
{
    public function index(Request $request)
    {
        $artifacts = Artifact::where('visibility', 'public')->paginate(15);

        return ArtifactResource::collection($artifacts);
    }

    public function show(Artifact $artifact)
    {
        if ($artifact->visibility !== 'public') {
            abort(404);
        }

        return new ArtifactResource($artifact);
    }
}
