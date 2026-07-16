<?php

namespace Database\Factories;

use App\Models\Museum;
use App\Models\MuseumImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class MuseumImageFactory extends Factory
{
    protected $model = MuseumImage::class;

    public function definition(): array
    {
        return [
            'museum_id' => Museum::factory(),
            'image_path' => 'museums/gallery/placeholder.jpg',
            'caption' => $this->faker->sentence(6),
            'sort_order' => 0,
        ];
    }
}
