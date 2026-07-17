<?php

namespace Database\Factories;

use App\Models\ArtifactMaterial;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArtifactMaterialFactory extends Factory
{
    protected $model = ArtifactMaterial::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Bronze', 'Gold', 'Silver', 'Terracotta', 'Marble', 'Wood',
                'Bone', 'Ivory', 'Glass', 'Textile', 'Paper', 'Stone',
            ]),
        ];
    }
}
