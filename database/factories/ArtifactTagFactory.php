<?php

namespace Database\Factories;

use App\Models\ArtifactTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArtifactTagFactory extends Factory
{
    protected $model = ArtifactTag::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Ancient Egypt', 'Roman Empire', 'Mughal Empire', 'Medieval', 'Ming Dynasty',
            'Byzantine', 'Pre-Columbian', 'Viking', 'Ottoman', 'Mesopotamian',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
