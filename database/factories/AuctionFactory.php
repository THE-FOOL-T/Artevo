<?php

namespace Database\Factories;

use App\Models\Artifact;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuctionFactory extends Factory
{
    protected $model = Auction::class;

    public function definition(): array
    {
        return [
            'artifact_id' => Artifact::factory(),
            'created_by' => User::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => Auction::STATUS_DRAFT,
            'currency' => 'USD',
            'reserve_price' => $this->faker->randomFloat(2, 1000, 5000),
            'current_price' => $this->faker->randomFloat(2, 100, 1000),
            'ends_at' => now()->addDays(7),
            'winner_id' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Auction::STATUS_ACTIVE,
            'current_price' => current($attributes) ?? $attributes['current_price'] ?? 500, // if not set
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Auction::STATUS_DRAFT,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Auction::STATUS_CLOSED,
            'ends_at' => now()->subDay(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Auction::STATUS_CANCELLED,
        ]);
    }
}
