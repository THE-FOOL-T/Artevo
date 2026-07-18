<?php

namespace Tests\Feature\Restorations;

use App\Models\Artifact;
use App\Models\Museum;
use App\Models\RestorationRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestorationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_artifact_owner_can_add_a_restoration_record(): void
    {
        $collector = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create(['collector_id' => $collector->id]);

        $response = $this->actingAs($collector)->postJson(route('artifacts.restoration.store', $artifact), [
            'category' => RestorationRecord::CATEGORY_REPAIR,
            'title' => 'Fixing a crack',
            'description' => 'Applied epoxy to the crack on the side.',
            'conservator_name' => 'John Doe',
            'institution' => 'Local Repair Shop',
            'started_at' => now()->subMonth()->toDateString(),
            'completed_at' => now()->toDateString(),
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('restoration_records', [
            'artifact_id' => $artifact->id,
            'category' => RestorationRecord::CATEGORY_REPAIR,
            'title' => 'Fixing a crack',
            'recorded_by' => $collector->id,
        ]);
    }

    /** @test */
    public function a_curator_can_add_a_restoration_record_to_museum_artifact(): void
    {
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();
        $artifact = Artifact::factory()->for($museum)->create();

        $response = $this->actingAs($curator)->postJson(route('artifacts.restoration.store', $artifact), [
            'category' => RestorationRecord::CATEGORY_CONSERVATION,
            'title' => 'Cleaning',
            'started_at' => now()->subDays(5)->toDateString(),
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('restoration_records', [
            'artifact_id' => $artifact->id,
            'category' => RestorationRecord::CATEGORY_CONSERVATION,
        ]);
    }

    /** @test */
    public function a_visitor_cannot_add_a_restoration_record(): void
    {
        $visitor = User::factory()->create();
        $artifact = Artifact::factory()->create();

        $response = $this->actingAs($visitor)->postJson(route('artifacts.restoration.store', $artifact), [
            'category' => RestorationRecord::CATEGORY_REPAIR,
            'title' => 'Unauthorized fix',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function an_owner_can_update_a_restoration_record(): void
    {
        $collector = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create(['collector_id' => $collector->id]);
        $record = RestorationRecord::factory()->create([
            'artifact_id' => $artifact->id,
            'recorded_by' => $collector->id,
            'title' => 'Old Title',
        ]);

        $response = $this->actingAs($collector)->putJson(route('artifacts.restoration.update', [$artifact, $record]), [
            'category' => RestorationRecord::CATEGORY_REPAIR,
            'title' => 'Updated Title',
        ]);

        $response->assertOk();
        $this->assertEquals('Updated Title', $record->fresh()->title);
    }

    /** @test */
    public function an_owner_can_delete_a_restoration_record(): void
    {
        $collector = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create(['collector_id' => $collector->id]);
        $record = RestorationRecord::factory()->create([
            'artifact_id' => $artifact->id,
            'recorded_by' => $collector->id,
        ]);

        $response = $this->actingAs($collector)->deleteJson(route('artifacts.restoration.destroy', [$artifact, $record]));

        $response->assertOk();
        $this->assertDatabaseMissing('restoration_records', ['id' => $record->id]);
    }

    /** @test */
    public function an_owner_can_reorder_restoration_records(): void
    {
        $collector = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create(['collector_id' => $collector->id]);
        $record1 = RestorationRecord::factory()->create(['artifact_id' => $artifact->id, 'sort_order' => 0]);
        $record2 = RestorationRecord::factory()->create(['artifact_id' => $artifact->id, 'sort_order' => 1]);

        $response = $this->actingAs($collector)->postJson(route('artifacts.restoration.reorder', $artifact), [
            'ids' => [
                $record2->id,
                $record1->id,
            ],
        ]);

        $response->assertOk();
        $this->assertEquals(0, $record2->fresh()->sort_order);
        $this->assertEquals(1, $record1->fresh()->sort_order);
    }
}
