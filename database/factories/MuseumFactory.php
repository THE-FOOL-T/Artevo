<?php

namespace Database\Factories;

use App\Models\Museum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MuseumFactory extends Factory
{
    protected $model = Museum::class;

    public function definition(): array
    {
        $name = $this->faker->company() . ' Museum';

        return [
            'curator_id' => User::factory()->curator(),
            'name' => $name,
            'tagline' => $this->faker->sentence(8),
            'description' => $this->faker->paragraphs(3, true),
            'foundation_year' => $this->faker->numberBetween(1750, 2015),
            'website' => $this->faker->url(),
            'social_links' => [
                'facebook' => 'https://facebook.com/' . $this->faker->slug(2),
                'instagram' => 'https://instagram.com/' . $this->faker->slug(2),
            ],
            'opening_hours' => [
                'monday' => 'Closed',
                'tuesday' => '10:00–18:00',
                'wednesday' => '10:00–18:00',
                'thursday' => '10:00–18:00',
                'friday' => '10:00–20:00',
                'saturday' => '10:00–20:00',
                'sunday' => '10:00–17:00',
            ],
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'featured' => false,
            'verification_status' => Museum::VERIFICATION_PENDING,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => ['featured' => true]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => ['verification_status' => Museum::VERIFICATION_VERIFIED]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => ['verification_status' => Museum::VERIFICATION_REJECTED]);
    }
}
