<?php

namespace Tests\Feature\Curator;

use App\Models\Museum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MuseumManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_curator_can_create_a_museum(): void
    {
        $curator = User::factory()->curator()->create();

        $response = $this->actingAs($curator)->post(route('curator.museums.store'), [
            'name' => 'The Riverside History Museum',
            'tagline' => 'A journey through the delta.',
        ]);

        $this->assertDatabaseHas('museums', [
            'name' => 'The Riverside History Museum',
            'curator_id' => $curator->id,
        ]);
        $response->assertRedirect();
    }

    /** @test */
    public function a_collector_cannot_create_a_museum(): void
    {
        $collector = User::factory()->collector()->create();

        $response = $this->actingAs($collector)->post(route('curator.museums.store'), [
            'name' => 'Should Not Exist Museum',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('museums', ['name' => 'Should Not Exist Museum']);
    }

    /** @test */
    public function a_curator_can_update_their_own_museum(): void
    {
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create(['name' => 'Old Name']);

        $response = $this->actingAs($curator)->put(route('curator.museums.update', $museum), [
            'name' => 'New Name',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('New Name', $museum->fresh()->name);
    }

    /** @test */
    public function a_curator_cannot_update_another_curators_museum(): void
    {
        $owner = User::factory()->curator()->create();
        $otherCurator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($owner, 'curator')->create(['name' => 'Original Name']);

        $response = $this->actingAs($otherCurator)->put(route('curator.museums.update', $museum), [
            'name' => 'Hijacked Name',
        ]);

        $response->assertForbidden();
        $this->assertSame('Original Name', $museum->fresh()->name);
    }

    /** @test */
    public function an_admin_can_update_any_museum(): void
    {
        $admin = User::factory()->admin()->create();
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create(['name' => 'Original Name']);

        $response = $this->actingAs($admin)->put(route('curator.museums.update', $museum), [
            'name' => 'Admin Edited Name',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('Admin Edited Name', $museum->fresh()->name);
    }

    /** @test */
    public function only_an_admin_can_feature_a_museum(): void
    {
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create(['featured' => false]);

        $this->actingAs($curator)->put(route('curator.museums.update', $museum), [
            'name' => $museum->name,
            'featured' => '1',
        ]);

        $this->assertFalse($museum->fresh()->featured);
    }

    /** @test */
    public function a_curator_can_delete_their_own_museum(): void
    {
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();

        $response = $this->actingAs($curator)->delete(route('curator.museums.destroy', $museum));

        $response->assertRedirect(route('curator.museums.index'));
        $this->assertSoftDeleted('museums', ['id' => $museum->id]);
    }

    /** @test */
    public function guests_cannot_reach_any_curator_museum_route(): void
    {
        $museum = Museum::factory()->create();

        $this->get(route('curator.museums.index'))->assertRedirect(route('login'));
        $this->get(route('curator.museums.edit', $museum))->assertRedirect(route('login'));
    }

    /** @test */
    public function the_museums_index_only_shows_the_curators_own_museums(): void
    {
        $curator = User::factory()->curator()->create();
        $otherCurator = User::factory()->curator()->create();

        Museum::factory()->for($curator, 'curator')->create(['name' => 'Mine']);
        Museum::factory()->for($otherCurator, 'curator')->create(['name' => 'Not Mine']);

        $response = $this->actingAs($curator)->get(route('curator.museums.index'));

        $response->assertSee('Mine');
        $response->assertDontSee('Not Mine');
    }

    /** @test */
    public function an_admin_sees_every_museum_in_the_index(): void
    {
        $admin = User::factory()->admin()->create();
        Museum::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('curator.museums.index'));

        $response->assertOk();
    }
}
