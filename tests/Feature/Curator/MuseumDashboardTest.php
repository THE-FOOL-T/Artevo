<?php

namespace Tests\Feature\Curator;

use App\Models\Museum;
use App\Models\MuseumImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MuseumDashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_curator_can_view_their_museums_dashboard(): void
    {
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();

        $response = $this->actingAs($curator)->get(route('curator.museums.dashboard', $museum));

        $response->assertOk();
    }

    /** @test */
    public function a_curator_cannot_view_another_curators_museum_dashboard(): void
    {
        $owner = User::factory()->curator()->create();
        $otherCurator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($owner, 'curator')->create();

        $response = $this->actingAs($otherCurator)->get(route('curator.museums.dashboard', $museum));

        $response->assertForbidden();
    }

    /** @test */
    public function the_dashboard_reports_the_real_gallery_and_contact_counts(): void
    {
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();
        MuseumImage::factory()->count(4)->for($museum)->create();

        $response = $this->actingAs($curator)->get(route('curator.museums.dashboard', $museum));

        $response->assertOk();
        $response->assertViewHas('museum', fn ($viewMuseum) => $viewMuseum->images_count === 4 && $viewMuseum->contacts_count === 0);
    }

    /** @test */
    public function visiting_the_public_profile_increments_the_view_counter(): void
    {
        $museum = Museum::factory()->create();
        $this->assertSame(0, $museum->views_count);

        $this->get(route('museums.show', $museum));
        $this->get(route('museums.show', $museum));

        $this->assertSame(2, $museum->fresh()->views_count);
    }
}
