<?php

namespace Database\Factories;

use App\Models\Artifact;
use App\Models\ArtifactDocument;
use App\Models\RestorationRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestorationRecordFactory extends Factory
{
    protected $model = RestorationRecord::class;

    public function definition(): array
    {
        return [
            'artifact_id' => Artifact::factory(),
            'recorded_by' => User::factory(),
            'category' => $this->faker->randomElement(array_keys(RestorationRecord::CATEGORIES)),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'conservator_name' => $this->faker->name(),
            'institution' => $this->faker->company(),
            'started_at' => $this->faker->dateTimeBetween('-1 year', '-6 months'),
            'completed_at' => $this->faker->dateTimeBetween('-5 months', 'now'),
            'artifact_document_id' => null, // Can be set via state or manually
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
