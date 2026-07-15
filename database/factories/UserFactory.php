<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'avatar_path' => null,
            'role' => User::ROLE_VISITOR,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user's email address should be unverified —
     * used to test the verification-required flow.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_ADMIN]);
    }

    public function curator(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_CURATOR]);
    }

    public function collector(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_COLLECTOR]);
    }

    public function visitor(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_VISITOR]);
    }
}
