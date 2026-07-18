<?php

namespace Tests\Feature;

use Tests\TestCase;

class StaticPagesTest extends TestCase
{
    /** @test */
    public function the_home_page_loads_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Artevo');
    }

    /** @test */
    public function the_about_page_loads_successfully(): void
    {
        $response = $this->get(route('about'));

        $response->assertOk();
        $response->assertSee('About Artevo');
    }

    /** @test */
    public function the_privacy_policy_page_loads_successfully(): void
    {
        $response = $this->get(route('privacy'));

        $response->assertOk();
        $response->assertSee('Privacy Policy');
    }

    /** @test */
    public function the_terms_page_loads_successfully(): void
    {
        $response = $this->get(route('terms'));

        $response->assertOk();
        $response->assertSee('Terms');
    }

    /** @test */
    public function the_contact_page_loads_successfully(): void
    {
        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertSee('Contact Artevo');
    }

    /** @test */
    public function an_unknown_url_renders_the_custom_404_page(): void
    {
        $response = $this->get('/this-page-does-not-exist');

        $response->assertNotFound();
        $response->assertSee("isn't in the collection", false);
    }
}
