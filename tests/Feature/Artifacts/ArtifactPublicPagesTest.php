<?php

namespace Tests\Feature\Artifacts;

use App\Models\Artifact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtifactPublicPagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function anyone_can_browse_the_public_artifact_directory(): void
    {
        Artifact::factory()->count(3)->create();

        $this->get(route('artifacts.index'))->assertOk();
    }

    /** @test */
    public function the_directory_only_lists_public_artifacts(): void
    {
        Artifact::factory()->create(['name' => 'Public Piece']);
        Artifact::factory()->private()->create(['name' => 'Private Piece']);
        Artifact::factory()->archived()->create(['name' => 'Archived Piece']);

        $response = $this->get(route('artifacts.index'));

        $response->assertSee('Public Piece');
        $response->assertDontSee('Private Piece');
        $response->assertDontSee('Archived Piece');
    }

    /** @test */
    public function the_directory_can_be_searched(): void
    {
        Artifact::factory()->create(['name' => 'The Rosetta Fragment']);
        Artifact::factory()->create(['name' => 'A Ming Vase']);

        $response = $this->get(route('artifacts.index', ['search' => 'Rosetta']));

        $response->assertSee('Rosetta Fragment');
        $response->assertDontSee('Ming Vase');
    }

    /** @test */
    public function anyone_can_view_a_public_artifact(): void
    {
        $artifact = Artifact::factory()->create(['name' => 'The Lantern Urn']);

        $this->get(route('artifacts.show', $artifact))->assertOk()->assertSee('The Lantern Urn');
    }

    /** @test */
    public function a_private_artifact_is_hidden_from_guests(): void
    {
        $artifact = Artifact::factory()->private()->create();

        $this->get(route('artifacts.show', $artifact))->assertForbidden();
    }

    /** @test */
    public function a_private_artifact_is_hidden_from_other_users(): void
    {
        $owner = User::factory()->collector()->create();
        $stranger = User::factory()->collector()->create();
        $artifact = Artifact::factory()->private()->create(['collector_id' => $owner->id, 'created_by' => $owner->id]);

        $this->actingAs($stranger)->get(route('artifacts.show', $artifact))->assertForbidden();
    }

    /** @test */
    public function the_owner_can_view_their_own_private_artifact(): void
    {
        $owner = User::factory()->collector()->create();
        $artifact = Artifact::factory()->private()->create(['collector_id' => $owner->id, 'created_by' => $owner->id]);

        $this->actingAs($owner)->get(route('artifacts.show', $artifact))->assertOk();
    }

    /** @test */
    public function an_admin_can_view_any_private_artifact(): void
    {
        $admin = User::factory()->admin()->create();
        $artifact = Artifact::factory()->private()->create();

        $this->actingAs($admin)->get(route('artifacts.show', $artifact))->assertOk();
    }

    /** @test */
    public function each_artifact_gets_a_unique_generated_code(): void
    {
        $artifact = Artifact::factory()->create();

        $this->assertMatchesRegularExpression('/^ART-\d{6}$/', $artifact->fresh()->artifact_code);
    }
}
