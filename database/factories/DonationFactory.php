<?php

namespace Database\Factories;

use App\Models\Artifact;
use App\Models\Donation;
use App\Models\Museum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition(): array
    {
        return [
            'artifact_id' => Artifact::factory(),
            'donor_id' => User::factory(),
            'museum_id' => Museum::factory(),
            'status' => Donation::STATUS_PENDING,
            'message' => $this->faker->paragraph(),
            'donated_at' => now(),
            'reviewed_by' => null,
            'rejection_reason' => null,
            'transferred_at' => null,
            'certificate_number' => null,
            'provenance_note' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Donation::STATUS_APPROVED,
            'reviewed_by' => User::factory()->admin(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Donation::STATUS_REJECTED,
            'reviewed_by' => User::factory()->admin(),
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }

    public function transferred(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Donation::STATUS_TRANSFERRED,
            'reviewed_by' => User::factory()->admin(),
            'transferred_at' => now(),
            'certificate_number' => 'DON-' . $this->faker->unique()->numerify('######'),
        ]);
    }
}
