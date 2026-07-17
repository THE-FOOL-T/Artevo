<?php

namespace App\Services;

use App\Models\Artifact;
use App\Models\Exhibition;
use App\Models\ExhibitionSection;
use App\Models\Museum;
use App\Models\User;

/**
 * All business logic for creating and managing Exhibitions lives here.
 * Controllers are thin and delegate every state mutation to this service.
 */
class ExhibitionService
{
    /**
     * Create a new exhibition for a museum.
     *
     * @param  array<string, mixed>  $data  Validated fields from StoreExhibitionRequest
     */
    public function create(Museum $museum, User $creator, array $data): Exhibition
    {
        return Exhibition::create([
            ...$data,
            'museum_id'  => $museum->id,
            'created_by' => $creator->id,
            'status'     => $data['status'] ?? Exhibition::STATUS_DRAFT,
        ]);
    }

    /**
     * Update core exhibition fields.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Exhibition $exhibition, array $data): Exhibition
    {
        $exhibition->update($data);

        return $exhibition->fresh();
    }

    /**
     * Transition an exhibition to "published" status.
     */
    public function publish(Exhibition $exhibition): Exhibition
    {
        $exhibition->update(['status' => Exhibition::STATUS_PUBLISHED]);

        return $exhibition->fresh();
    }

    /**
     * Transition an exhibition to "archived" status.
     */
    public function archive(Exhibition $exhibition): Exhibition
    {
        $exhibition->update(['status' => Exhibition::STATUS_ARCHIVED]);

        return $exhibition->fresh();
    }

    // ─── Section management ───────────────────────────────────────────────────

    /**
     * Add a new narrative section to the exhibition.
     * It is placed at the end of the current section list.
     *
     * @param  array<string, mixed>  $data
     */
    public function addSection(Exhibition $exhibition, array $data): ExhibitionSection
    {
        $maxOrder = $exhibition->sections()->max('sort_order') ?? -1;

        return $exhibition->sections()->create([
            ...$data,
            'sort_order' => $maxOrder + 1,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateSection(ExhibitionSection $section, array $data): ExhibitionSection
    {
        $section->update($data);

        return $section->fresh();
    }

    public function removeSection(ExhibitionSection $section): void
    {
        $section->delete();
    }

    /**
     * Persist a new display order for all sections of an exhibition.
     *
     * @param  int[]  $orderedSectionIds
     */
    public function reorderSections(Exhibition $exhibition, array $orderedSectionIds): void
    {
        foreach ($orderedSectionIds as $position => $sectionId) {
            $exhibition->sections()
                ->where('id', (int) $sectionId)
                ->update(['sort_order' => $position]);
        }
    }

    // ─── Artifact-in-section management ──────────────────────────────────────

    /**
     * Add an artifact to a section.
     * Silently does nothing if the artifact is already in the section.
     */
    public function addArtifactToSection(ExhibitionSection $section, Artifact $artifact): void
    {
        if ($section->artifacts()->where('artifact_id', $artifact->id)->doesntExist()) {
            $maxOrder = $section->artifacts()->max('exhibition_section_artifact.sort_order') ?? -1;

            $section->artifacts()->attach($artifact->id, [
                'sort_order' => $maxOrder + 1,
            ]);
        }
    }

    public function removeArtifactFromSection(ExhibitionSection $section, Artifact $artifact): void
    {
        $section->artifacts()->detach($artifact->id);
    }

    /**
     * @param  int[]  $orderedArtifactIds
     */
    public function reorderSectionArtifacts(ExhibitionSection $section, array $orderedArtifactIds): void
    {
        foreach ($orderedArtifactIds as $position => $artifactId) {
            $section->artifacts()->updateExistingPivot((int) $artifactId, [
                'sort_order' => $position,
            ]);
        }
    }
}
