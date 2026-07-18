<?php

namespace Tests\Feature\Auctions;

use App\Models\Artifact;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Museum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuctionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_curator_can_create_an_auction_for_their_artifact(): void
    {
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();
        $artifact = Artifact::factory()->for($museum)->create();

        $response = $this->actingAs($curator)->post(route('curator.artifact-auction.store', [$museum, $artifact]), [
            'title' => 'Test Auction',
            'description' => 'Test Description',
            'reserve_price' => 2000,
            'bid_increment' => 50,
            'starts_at' => now()->addDay()->toDateTimeString(),
            'ends_at' => now()->addDays(7)->toDateTimeString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('auctions', [
            'artifact_id' => $artifact->id,
            'title' => 'Test Auction',
            'status' => Auction::STATUS_DRAFT,
        ]);
    }

    /** @test */
    public function a_collector_can_create_an_auction_for_their_artifact(): void
    {
        $collector = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create(['collector_id' => $collector->id, 'museum_id' => null]);

        $response = $this->actingAs($collector)->post(route('collector.artifact-auction.store', $artifact), [
            'title' => 'Collector Auction',
            'description' => 'Test Description',
            'reserve_price' => 2000,
            'bid_increment' => 50,
            'starts_at' => now()->addDay()->toDateTimeString(),
            'ends_at' => now()->addDays(5)->toDateTimeString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('auctions', [
            'artifact_id' => $artifact->id,
            'title' => 'Collector Auction',
            'status' => Auction::STATUS_DRAFT,
        ]);
    }

    /** @test */
    public function an_auction_creator_can_publish_a_draft_auction(): void
    {
        $curator = User::factory()->curator()->create();
        $auction = Auction::factory()->draft()->create(['created_by' => $curator->id]);

        $response = $this->actingAs($curator)->patch(route('curator.auctions.publish', $auction));

        $response->assertRedirect();
        $this->assertEquals(Auction::STATUS_ACTIVE, $auction->fresh()->status);
    }



    /** @test */
    public function a_collector_can_place_a_valid_bid(): void
    {
        $collector = User::factory()->collector()->create();
        $auction = Auction::factory()->active()->create(['current_price' => 1000]);

        $response = $this->actingAs($collector)->post(route('auctions.bid', $auction), [
            'amount' => 1500,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('auction_bids', [
            'auction_id' => $auction->id,
            'user_id' => $collector->id,
            'amount' => 1500,
        ]);

        $this->assertEquals(1500, $auction->fresh()->current_price);
    }

    /** @test */
    public function a_bid_must_be_higher_than_the_current_price(): void
    {
        $collector = User::factory()->collector()->create();
        $auction = Auction::factory()->active()->create(['current_price' => 1500]);
        AuctionBid::factory()->create(['auction_id' => $auction->id, 'amount' => 1500]);

        $response = $this->actingAs($collector)->post(route('auctions.bid', $auction), [
            'amount' => 1200, // Lower than current_price
        ]);

        $response->assertSessionHasErrors('amount');
    }

    /** @test */
    public function a_creator_can_close_their_active_auction(): void
    {
        $curator = User::factory()->curator()->create();
        $auction = Auction::factory()->active()->create(['created_by' => $curator->id]);

        $response = $this->actingAs($curator)->patch(route('curator.auctions.close', $auction));

        $response->assertRedirect();
        $this->assertEquals(Auction::STATUS_CLOSED, $auction->fresh()->status);
    }

    /** @test */
    public function a_creator_can_cancel_an_auction_without_bids(): void
    {
        $curator = User::factory()->curator()->create();
        $auction = Auction::factory()->active()->create(['created_by' => $curator->id]);

        $response = $this->actingAs($curator)->delete(route('curator.auctions.cancel', $auction));

        $response->assertRedirect();
        $this->assertEquals(Auction::STATUS_CANCELLED, $auction->fresh()->status);
    }
}
