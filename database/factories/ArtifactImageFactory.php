<?php

namespace Database\Factories;

use App\Models\Artifact;
use App\Models\ArtifactImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArtifactImageFactory extends Factory
{
    protected $model = ArtifactImage::class;

    public function definition(): array
    {
        return [
            'artifact_id' => Artifact::factory(),
            'image_path' => 'artifacts/gallery/placeholder.jpg',
            'caption' => $this->faker->sentence(6),
            'is_primary' => false,
            'sort_order' => 0,
        ];
    }
}
