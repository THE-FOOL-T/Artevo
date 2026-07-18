<?php

namespace Database\Factories;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuctionBidFactory extends Factory
{
    protected $model = AuctionBid::class;

    public function definition(): array
    {
        return [
            'auction_id' => Auction::factory(),
            'user_id' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 5000),
            'is_winning' => false,
        ];
    }
}
