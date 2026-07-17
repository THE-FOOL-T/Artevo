<?php

namespace Database\Factories;

use App\Models\Artifact;
use App\Models\ArtifactDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArtifactDocumentFactory extends Factory
{
    protected $model = ArtifactDocument::class;

    public function definition(): array
    {
        return [
            'artifact_id' => Artifact::factory(),
            'title' => $this->faker->sentence(4),
            'document_type' => $this->faker->randomElement(['Certificate', 'Manuscript', 'Research Paper']),
            'document_path' => 'artifacts/documents/placeholder.pdf',
        ];
    }
}
