<?php

namespace Tests\Feature\Museums;

use App\Models\Museum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MuseumPublicPagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function anyone_can_browse_the_museum_directory(): void
    {
        Museum::factory()->count(3)->create();

        $response = $this->get(route('museums.index'));

        $response->assertOk();
    }

    /** @test */
    public function the_directory_can_be_searched_by_name(): void
    {
        Museum::factory()->create(['name' => 'The Cairo Antiquities Museum']);
        Museum::factory()->create(['name' => 'Berlin Modern Art House']);

        $response = $this->get(route('museums.index', ['search' => 'Cairo']));

        $response->assertOk();
        $response->assertSee('Cairo Antiquities');
        $response->assertDontSee('Berlin Modern');
    }

    /** @test */
    public function anyone_can_view_a_museum_profile_page(): void
    {
        $museum = Museum::factory()->create(['name' => 'The Lantern House Museum']);

        $response = $this->get(route('museums.show', $museum));

        $response->assertOk();
        $response->assertSee('The Lantern House Museum');
    }

    /** @test */
    public function museum_profile_pages_use_a_slug_url(): void
    {
        $museum = Museum::factory()->create(['name' => 'Unique Slug Test Museum']);

        $this->assertStringContainsString('unique-slug-test-museum', $museum->fresh()->slug);
        $this->get('/museums/' . $museum->slug)->assertOk();
    }
}
