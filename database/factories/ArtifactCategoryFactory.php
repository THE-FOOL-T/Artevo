<?php

namespace Database\Factories;

use App\Models\ArtifactCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArtifactCategoryFactory extends Factory
{
    protected $model = ArtifactCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Sculpture', 'Pottery & Ceramics', 'Manuscripts & Documents', 'Weapons & Armor',
            'Jewelry', 'Coins & Currency', 'Textiles', 'Paintings', 'Tools & Instruments', 'Religious Artifacts',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(10),
        ];
    }
}
