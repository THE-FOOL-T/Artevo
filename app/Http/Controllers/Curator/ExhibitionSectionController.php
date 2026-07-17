<?php

namespace App\Http\Controllers\Curator;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveExhibitionSectionRequest;
use App\Models\Exhibition;
use App\Models\ExhibitionSection;
use App\Services\ExhibitionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExhibitionSectionController extends Controller
{
    public function __construct(private ExhibitionService $exhibitionService) {}

    /**
     * Add a new section to an exhibition.
     */
    public function store(SaveExhibitionSectionRequest $request, Exhibition $exhibition): JsonResponse
    {
        Gate::authorize('update', $exhibition);

        $section = $this->exhibitionService->addSection($exhibition, $request->validated());

        return response()->json([
            'success' => true,
            'section' => [
                'id'         => $section->id,
                'title'      => $section->title,
                'body'       => $section->body,
                'sort_order' => $section->sort_order,
            ],
        ]);
    }

    /**
     * Update a section's title and/or body.
     */
    public function update(UpdateExhibitionSectionRequest $request, Exhibition $exhibition, ExhibitionSection $section): JsonResponse
    {
        Gate::authorize('update', $exhibition);

        $section = $this->exhibitionService->updateSection($section, $request->validated());

        return response()->json([
            'success' => true,
            'section' => [
                'id'    => $section->id,
                'title' => $section->title,
                'body'  => $section->body,
            ],
        ]);
    }

    /**
     * Delete a section and all its artifact memberships.
     */
    public function destroy(Exhibition $exhibition, ExhibitionSection $section): JsonResponse
    {
        Gate::authorize('update', $exhibition);

        $this->exhibitionService->removeSection($section);

        return response()->json(['success' => true]);
    }

    /**
     * Persist a curator-defined section order.
     * Expects JSON body: { "ids": [3, 1, 2] }
     */
    public function reorder(Request $request, Exhibition $exhibition): JsonResponse
    {
        Gate::authorize('update', $exhibition);

        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $this->exhibitionService->reorderSections($exhibition, $validated['ids']);

        return response()->json(['success' => true]);
    }
}
