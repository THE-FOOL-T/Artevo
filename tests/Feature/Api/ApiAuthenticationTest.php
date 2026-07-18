<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_cannot_access_protected_routes(): void
    {
        $response = $this->getJson('/api/v1/user');

        $response->assertUnauthorized();
    }

    /** @test */
    public function authenticated_users_can_access_protected_routes(): void
    {
        $user = User::factory()->create();

        // Using Sanctum's testing helper to authenticate
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/user');

        $response->assertOk();
        $response->assertJson([
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function users_can_generate_an_api_token(): void
    {
        $user = User::factory()->create();

        // In a real application, you'd have an endpoint to issue tokens.
        // Since we don't have a specific login endpoint in the API routes provided,
        // we test the token creation logic itself.
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/user');

        $response->assertOk();
        $response->assertJson([
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }
}
