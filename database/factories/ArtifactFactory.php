<?php

namespace Database\Factories;

use App\Models\Artifact;
use App\Models\ArtifactCategory;
use App\Models\ArtifactMaterial;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArtifactFactory extends Factory
{
    protected $model = Artifact::class;

    public function definition(): array
    {
        $name = ucfirst($this->faker->words(3, true));

        return [
            'created_by' => User::factory()->collector(),
            'museum_id' => null,
            'collector_id' => User::factory()->collector(),
            'category_id' => ArtifactCategory::factory(),
            'material_id' => ArtifactMaterial::factory(),
            'name' => $name,
            'short_description' => $this->faker->sentence(12),
            'description' => $this->faker->paragraphs(3, true),
            'civilization' => $this->faker->randomElement(['Ancient Egyptian', 'Roman', 'Mughal', 'Byzantine', 'Ming Dynasty']),
            'era' => $this->faker->randomElement(['Bronze Age', 'Iron Age', 'Classical Antiquity', 'Medieval', 'Renaissance']),
            'century' => $this->faker->randomElement(['5th century BCE', '2nd century CE', '14th century', '18th century']),
            'country_of_origin' => $this->faker->country(),
            'region' => $this->faker->state(),
            'discovery_location' => $this->faker->city(),
            'language' => $this->faker->randomElement(['Hieroglyphic', 'Latin', 'Old Persian', 'Sanskrit', null]),
            'dimensions' => $this->faker->numberBetween(5, 80) . 'cm x ' . $this->faker->numberBetween(5, 80) . 'cm',
            'weight' => $this->faker->numberBetween(1, 20) . 'kg',
            'condition' => $this->faker->randomElement(['Excellent', 'Good', 'Fair', 'Poor']),
            'estimated_value' => $this->faker->randomFloat(2, 500, 250000),
            'status' => Artifact::STATUS_PUBLIC,
        ];
    }

    /**
     * A museum-owned artifact instead of the default collector-owned one.
     * Usage: Artifact::factory()->forMuseum($museum)->create()
     */
    public function forMuseum(\App\Models\Museum $museum): static
    {
        return $this->state(fn (array $attributes) => [
            'museum_id' => $museum->id,
            'collector_id' => null,
            'created_by' => $museum->curator_id,
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => ['status' => Artifact::STATUS_PRIVATE]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => ['status' => Artifact::STATUS_ARCHIVED]);
    }
}
