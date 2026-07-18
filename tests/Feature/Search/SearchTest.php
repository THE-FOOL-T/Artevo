<?php

namespace Tests\Feature\Search;

use App\Models\Artifact;
use App\Models\ArtifactCategory;
use App\Models\ArtifactMaterial;
use App\Models\Museum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function artifacts_can_be_searched_by_text(): void
    {
        $artifact1 = Artifact::factory()->create(['name' => 'Golden Chalice', 'status' => Artifact::STATUS_PUBLIC]);
        $artifact2 = Artifact::factory()->create(['name' => 'Silver Sword', 'status' => Artifact::STATUS_PUBLIC]);

        $response = $this->get('/artifacts?search=Golden');

        $response->assertOk();
        $response->assertSee('Golden Chalice');
        $response->assertDontSee('Silver Sword');
    }

    /** @test */
    public function artifacts_can_be_filtered_by_category_and_material(): void
    {
        $category = ArtifactCategory::factory()->create();
        $material = ArtifactMaterial::factory()->create();
        
        $artifact1 = Artifact::factory()->create([
            'category_id' => $category->id,
            'material_id' => $material->id,
            'status' => Artifact::STATUS_PUBLIC,
        ]);
        $artifact2 = Artifact::factory()->create(['status' => Artifact::STATUS_PUBLIC]);

        $response = $this->get('/artifacts?category=' . $category->id . '&material=' . $material->id);

        $response->assertOk();
        $response->assertSee($artifact1->name);
        $response->assertDontSee($artifact2->name);
    }

    /** @test */
    public function artifacts_are_sorted_by_name(): void
    {
        $artifact1 = Artifact::factory()->create(['name' => 'Zeus Statue', 'status' => Artifact::STATUS_PUBLIC]);
        $artifact2 = Artifact::factory()->create(['name' => 'Apollo Statue', 'status' => Artifact::STATUS_PUBLIC]);

        $response = $this->get('/artifacts?sort=name');

        $response->assertOk();
        
        $content = $response->getContent();
        $this->assertStringContainsString('Apollo Statue', $content);
        $this->assertStringContainsString('Zeus Statue', $content);
        $this->assertTrue(strpos($content, 'Apollo Statue') < strpos($content, 'Zeus Statue'));
    }

    /** @test */
    public function museums_can_be_searched_by_name_and_city(): void
    {
        $museum1 = Museum::factory()->create(['name' => 'Louvre', 'city' => 'Paris']);
        $museum2 = Museum::factory()->create(['name' => 'Metropolitan', 'city' => 'New York']);

        $response = $this->get('/museums?search=Paris');

        $response->assertOk();
        $response->assertSee('Louvre');
        $response->assertDontSee('Metropolitan');
    }

    /** @test */
    public function museums_can_be_filtered_by_verified_status(): void
    {
        $verifiedMuseum = Museum::factory()->create([
            'name' => 'Verified Museum',
            'verification_status' => Museum::VERIFICATION_VERIFIED,
        ]);
        $pendingMuseum = Museum::factory()->create([
            'name' => 'Pending Museum',
            'verification_status' => Museum::VERIFICATION_PENDING,
        ]);

        $response = $this->get('/museums?verified=1');

        $response->assertOk();
        $response->assertSee('Verified Museum');
        $response->assertDontSee('Pending Museum');
    }
}
