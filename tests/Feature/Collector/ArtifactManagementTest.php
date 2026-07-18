<?php

namespace Tests\Feature\Collector;

use App\Models\Artifact;
use App\Models\ArtifactCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtifactManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_collector_can_add_an_artifact_to_their_collection(): void
    {
        $collector = User::factory()->collector()->create();
        $category = ArtifactCategory::factory()->create();

        $response = $this->actingAs($collector)->post(route('collector.artifacts.store'), [
            'name' => 'A Family Heirloom Coin',
            'category_id' => $category->id,
            'status' => 'private',
        ]);

        $this->assertDatabaseHas('artifacts', [
            'name' => 'A Family Heirloom Coin',
            'collector_id' => $collector->id,
            'museum_id' => null,
            'created_by' => $collector->id,
        ]);
        $response->assertRedirect();
    }

    /** @test */
    public function a_visitor_cannot_add_an_artifact(): void
    {
        $visitor = User::factory()->visitor()->create();
        $category = ArtifactCategory::factory()->create();

        $response = $this->actingAs($visitor)->post(route('collector.artifacts.store'), [
            'name' => 'Should Not Exist',
            'category_id' => $category->id,
            'status' => 'private',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('artifacts', ['name' => 'Should Not Exist']);
    }

    /** @test */
    public function a_collector_can_update_their_own_artifact(): void
    {
        $collector = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create([
            'collector_id' => $collector->id,
            'created_by' => $collector->id,
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($collector)->put(route('collector.artifacts.update', $artifact), [
            'name' => 'New Name',
            'category_id' => $artifact->category_id,
            'status' => $artifact->status,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('New Name', $artifact->fresh()->name);
    }

    /** @test */
    public function a_collector_cannot_update_another_collectors_artifact(): void
    {
        $owner = User::factory()->collector()->create();
        $intruder = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create([
            'collector_id' => $owner->id,
            'created_by' => $owner->id,
            'name' => 'Original Name',
        ]);

        $response = $this->actingAs($intruder)->put(route('collector.artifacts.update', $artifact), [
            'name' => 'Hijacked Name',
            'category_id' => $artifact->category_id,
            'status' => $artifact->status,
        ]);

        $response->assertForbidden();
        $this->assertSame('Original Name', $artifact->fresh()->name);
    }

    /** @test */
    public function a_curator_cannot_manage_an_artifact_via_the_collector_route(): void
    {
        $curator = User::factory()->curator()->create();
        $collector = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create([
            'collector_id' => $collector->id,
            'created_by' => $collector->id,
        ]);

        $response = $this->actingAs($curator)->get(route('collector.artifacts.edit', $artifact));

        $response->assertForbidden();
    }

    /** @test */
    public function the_collector_index_only_shows_their_own_artifacts(): void
    {
        $collector = User::factory()->collector()->create();
        $otherCollector = User::factory()->collector()->create();

        Artifact::factory()->create(['collector_id' => $collector->id, 'created_by' => $collector->id, 'name' => 'Mine']);
        Artifact::factory()->create(['collector_id' => $otherCollector->id, 'created_by' => $otherCollector->id, 'name' => 'Not Mine']);

        $response = $this->actingAs($collector)->get(route('collector.artifacts.index'));

        $response->assertSee('Mine');
        $response->assertDontSee('Not Mine');
    }

    /** @test */
    public function a_collector_can_delete_their_own_artifact(): void
    {
        $collector = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create(['collector_id' => $collector->id, 'created_by' => $collector->id]);

        $response = $this->actingAs($collector)->delete(route('collector.artifacts.destroy', $artifact));

        $response->assertRedirect(route('collector.artifacts.index'));
        $this->assertSoftDeleted('artifacts', ['id' => $artifact->id]);
    }
}
