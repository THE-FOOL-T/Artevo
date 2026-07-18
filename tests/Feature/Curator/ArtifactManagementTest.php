<?php

namespace Tests\Feature\Curator;

use App\Models\Artifact;
use App\Models\ArtifactCategory;
use App\Models\Museum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtifactManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_curator_can_add_an_artifact_to_their_museum(): void
    {
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();
        $category = ArtifactCategory::factory()->create();

        $response = $this->actingAs($curator)->post(route('curator.museums.artifacts.store', $museum), [
            'name' => 'The Bronze Falcon',
            'category_id' => $category->id,
            'status' => 'public',
            'tags' => ['Ancient Egypt', 'Bronze Age'],
        ]);

        $this->assertDatabaseHas('artifacts', [
            'name' => 'The Bronze Falcon',
            'museum_id' => $museum->id,
            'collector_id' => null,
            'created_by' => $curator->id,
        ]);

        $artifact = Artifact::where('name', 'The Bronze Falcon')->first();
        $this->assertSame(2, $artifact->tags()->count());
        $response->assertRedirect(route('curator.museums.artifacts.edit', [$museum, $artifact]));
    }

    /** @test */
    public function a_curator_cannot_add_an_artifact_to_another_curators_museum(): void
    {
        $owner = User::factory()->curator()->create();
        $intruder = User::factory()->curator()->create();
        $museum = Museum::factory()->for($owner, 'curator')->create();
        $category = ArtifactCategory::factory()->create();

        $response = $this->actingAs($intruder)->post(route('curator.museums.artifacts.store', $museum), [
            'name' => 'Should Not Exist',
            'category_id' => $category->id,
            'status' => 'public',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('artifacts', ['name' => 'Should Not Exist']);
    }

    /** @test */
    public function a_collector_cannot_add_an_artifact_via_the_curator_route(): void
    {
        $collector = User::factory()->collector()->create();
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();
        $category = ArtifactCategory::factory()->create();

        $response = $this->actingAs($collector)->post(route('curator.museums.artifacts.store', $museum), [
            'name' => 'Should Not Exist',
            'category_id' => $category->id,
            'status' => 'public',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function a_curator_can_update_an_artifact_in_their_museum(): void
    {
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();
        $artifact = Artifact::factory()->forMuseum($museum)->create(['name' => 'Old Name']);

        $response = $this->actingAs($curator)->put(route('curator.museums.artifacts.update', [$museum, $artifact]), [
            'name' => 'New Name',
            'category_id' => $artifact->category_id,
            'status' => 'public',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('New Name', $artifact->fresh()->name);
    }

    /** @test */
    public function a_curator_can_delete_an_artifact_from_their_museum(): void
    {
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();
        $artifact = Artifact::factory()->forMuseum($museum)->create();

        $response = $this->actingAs($curator)->delete(route('curator.museums.artifacts.destroy', [$museum, $artifact]));

        $response->assertRedirect(route('curator.museums.artifacts.index', $museum));
        $this->assertSoftDeleted('artifacts', ['id' => $artifact->id]);
    }

    /** @test */
    public function the_artifacts_index_is_scoped_to_the_given_museum(): void
    {
        $curator = User::factory()->curator()->create();
        $museumA = Museum::factory()->for($curator, 'curator')->create();
        $museumB = Museum::factory()->for($curator, 'curator')->create();

        Artifact::factory()->forMuseum($museumA)->create(['name' => 'In Museum A']);
        Artifact::factory()->forMuseum($museumB)->create(['name' => 'In Museum B']);

        $response = $this->actingAs($curator)->get(route('curator.museums.artifacts.index', $museumA));

        $response->assertSee('In Museum A');
        $response->assertDontSee('In Museum B');
    }

    /** @test */
    public function an_admin_can_manage_artifacts_in_any_museum(): void
    {
        $admin = User::factory()->admin()->create();
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();
        $category = ArtifactCategory::factory()->create();

        $response = $this->actingAs($admin)->post(route('curator.museums.artifacts.store', $museum), [
            'name' => 'Admin Added Artifact',
            'category_id' => $category->id,
            'status' => 'public',
        ]);

        $this->assertDatabaseHas('artifacts', ['name' => 'Admin Added Artifact', 'museum_id' => $museum->id]);
        $response->assertRedirect();
    }
}
