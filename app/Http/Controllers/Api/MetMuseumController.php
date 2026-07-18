<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MetMuseumService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Internal JSON API for the Met Museum integration.
 * Used by the artifact create/edit form's "Import from Met Museum" panel.
 * Requires authentication — curators and collectors only.
 */
class MetMuseumController extends Controller
{
    public function __construct(protected MetMuseumService $met) {}

    /**
     * GET /api/met/search?q=rosetta+stone
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2|max:200']);

        $results = $this->met->search($request->string('q'));

        return response()->json([
            'results' => $results,
            'count'   => count($results),
        ]);
    }

    /**
     * GET /api/met/objects/{id}
     */
    public function show(int $id): JsonResponse
    {
        $raw = $this->met->getObject($id);

        if (! $raw) {
            return response()->json(['error' => 'Object not found'], 404);
        }

        return response()->json($this->met->normalise($raw));
    }
}
