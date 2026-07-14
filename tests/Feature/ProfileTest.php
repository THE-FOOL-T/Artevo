<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_profile_page_loads_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertOk();
    }

    /** @test */
    public function guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('profile.edit'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function a_user_can_update_their_name_and_email(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ]);

        $response->assertSessionHasNoErrors();
        $user->refresh();

        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('updated@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    /** @test */
    public function a_user_can_upload_an_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $avatar = UploadedFile::fake()->image('avatar.jpg');

        $this
            ->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $avatar,
            ]);

        $user->refresh();

        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    /** @test */
    public function a_user_can_delete_their_account_with_correct_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('Password123')]);

        $response = $this
            ->actingAs($user)
            ->delete(route('profile.destroy'), ['password' => 'Password123']);

        $response->assertRedirect(route('home'));
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function account_deletion_requires_the_correct_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('Password123')]);

        $response = $this
            ->actingAs($user)
            ->delete(route('profile.destroy'), ['password' => 'wrong-password']);

        $response->assertSessionHasErrorsIn('userDeletion', 'password');
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
