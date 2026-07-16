<?php

namespace Database\Factories;

use App\Models\Museum;
use App\Models\MuseumContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class MuseumContactFactory extends Factory
{
    protected $model = MuseumContact::class;

    public function definition(): array
    {
        return [
            'museum_id' => Museum::factory(),
            'label' => 'General Inquiries',
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'is_primary' => true,
        ];
    }
}
