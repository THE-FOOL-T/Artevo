<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_registration_page_loads(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    /** @test */
    public function a_new_user_can_register_and_is_logged_in(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Amelia Hart',
            'email' => 'amelia@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['email' => 'amelia@example.com']);
    }

    /** @test */
    public function registration_requires_a_unique_email(): void
    {
        \App\Models\User::factory()->create(['email' => 'amelia@example.com']);

        $response = $this->post(route('register'), [
            'name' => 'Amelia Hart',
            'email' => 'amelia@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function registration_requires_matching_password_confirmation(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Amelia Hart',
            'email' => 'amelia@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'not-the-same',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}
