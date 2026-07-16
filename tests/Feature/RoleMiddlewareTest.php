<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * CuratorMiddleware and CollectorMiddleware aren't attached to any real
 * route yet — their first consumers are the Museum module (Phase 5+) and
 * the Artifact module (Phase 7+) respectively. Registering throwaway
 * test routes here lets the middleware itself be verified now rather
 * than waiting until those phases to discover a bug in it.
 */
class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'curator'])->get('/__test-curator-only', fn () => 'curator-ok');
        Route::middleware(['web', 'auth', 'collector'])->get('/__test-collector-only', fn () => 'collector-ok');
    }

    /** @test */
    public function curator_middleware_allows_curators_and_blocks_everyone_else(): void
    {
        $curator = User::factory()->curator()->create();
        $collector = User::factory()->collector()->create();

        $this->actingAs($curator)->get('/__test-curator-only')->assertOk()->assertSee('curator-ok');
        $this->actingAs($collector)->get('/__test-curator-only')->assertForbidden();
    }

    /** @test */
    public function collector_middleware_allows_collectors_and_blocks_everyone_else(): void
    {
        $collector = User::factory()->collector()->create();
        $visitor = User::factory()->visitor()->create();

        $this->actingAs($collector)->get('/__test-collector-only')->assertOk()->assertSee('collector-ok');
        $this->actingAs($visitor)->get('/__test-collector-only')->assertForbidden();
    }
}
