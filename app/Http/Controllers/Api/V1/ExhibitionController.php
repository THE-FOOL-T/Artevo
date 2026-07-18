<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExhibitionResource;
use App\Models\Exhibition;
use Illuminate\Http\Request;

class ExhibitionController extends Controller
{
    public function index(Request $request)
    {
        $exhibitions = Exhibition::where('status', 'published')->paginate(15);

        return ExhibitionResource::collection($exhibitions);
    }

    public function show(Exhibition $exhibition)
    {
        if ($exhibition->status !== 'published') {
            abort(404);
        }

        return new ExhibitionResource($exhibition);
    }
}
