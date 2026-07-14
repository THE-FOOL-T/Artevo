<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_update_their_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('OldPassword123')]);

        $response = $this
            ->actingAs($user)
            ->put(route('password.update'), [
                'current_password' => 'OldPassword123',
                'password' => 'NewPassword456',
                'password_confirmation' => 'NewPassword456',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewPassword456', $user->fresh()->password));
    }

    /** @test */
    public function the_current_password_must_be_correct(): void
    {
        $user = User::factory()->create(['password' => bcrypt('OldPassword123')]);

        $response = $this
            ->actingAs($user)
            ->put(route('password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'NewPassword456',
                'password_confirmation' => 'NewPassword456',
            ]);

        $response->assertSessionHasErrorsIn('updatePassword', 'current_password');
    }
}
