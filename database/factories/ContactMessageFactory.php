<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'category' => $this->faker->randomElement(array_keys(ContactMessage::CATEGORIES)),
            'subject' => $this->faker->sentence(6),
            'message' => $this->faker->paragraph(3),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => 'Mozilla/5.0 (Test Suite)',
            'status' => 'new',
        ];
    }
}
