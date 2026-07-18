<?php

namespace Tests\Feature\Artifacts;

use App\Models\Artifact;
use App\Models\ArtifactDocument;
use App\Models\ArtifactImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArtifactMediaManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_owner_can_upload_gallery_images(): void
    {
        Storage::fake('public');

        $collector = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create(['collector_id' => $collector->id, 'created_by' => $collector->id]);

        $this->actingAs($collector)->post(route('artifacts.images.store', $artifact), [
            'images' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')],
        ]);

        $this->assertSame(2, $artifact->images()->count());
    }

    /** @test */
    public function the_first_uploaded_image_becomes_the_primary_cover(): void
    {
        Storage::fake('public');

        $collector = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create(['collector_id' => $collector->id, 'created_by' => $collector->id]);

        $this->actingAs($collector)->post(route('artifacts.images.store', $artifact), [
            'images' => [UploadedFile::fake()->image('a.jpg')],
        ]);
        $this->actingAs($collector)->post(route('artifacts.images.store', $artifact), [
            'images' => [UploadedFile::fake()->image('b.jpg')],
        ]);

        $this->assertSame(1, $artifact->images()->where('is_primary', true)->count());
        $this->assertTrue($artifact->images()->orderBy('id')->first()->is_primary);
    }

    /** @test */
    public function a_non_owner_cannot_upload_images(): void
    {
        Storage::fake('public');

        $owner = User::factory()->collector()->create();
        $intruder = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create(['collector_id' => $owner->id, 'created_by' => $owner->id]);

        $response = $this->actingAs($intruder)->post(route('artifacts.images.store', $artifact), [
            'images' => [UploadedFile::fake()->image('a.jpg')],
        ]);

        $response->assertForbidden();
        $this->assertSame(0, $artifact->images()->count());
    }

    /** @test */
    public function the_owner_can_change_the_cover_image(): void
    {
        Storage::fake('public');

        $collector = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create(['collector_id' => $collector->id, 'created_by' => $collector->id]);
        $first = ArtifactImage::factory()->for($artifact)->create(['is_primary' => true]);
        $second = ArtifactImage::factory()->for($artifact)->create(['is_primary' => false]);

        $this->actingAs($collector)->patch(route('artifacts.images.primary', [$artifact, $second]));

        $this->assertFalse($first->fresh()->is_primary);
        $this->assertTrue($second->fresh()->is_primary);
    }

    /** @test */
    public function removing_the_cover_image_promotes_another(): void
    {
        Storage::fake('public');

        $collector = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create(['collector_id' => $collector->id, 'created_by' => $collector->id]);
        $cover = ArtifactImage::factory()->for($artifact)->create(['is_primary' => true, 'image_path' => 'artifacts/gallery/cover.jpg']);
        ArtifactImage::factory()->for($artifact)->create(['is_primary' => false]);
        Storage::disk('public')->put('artifacts/gallery/cover.jpg', 'fake');

        $this->actingAs($collector)->delete(route('artifacts.images.destroy', [$artifact, $cover]));

        $this->assertSame(1, $artifact->images()->where('is_primary', true)->count());
    }

    /** @test */
    public function the_owner_can_upload_a_document(): void
    {
        Storage::fake('public');

        $collector = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create(['collector_id' => $collector->id, 'created_by' => $collector->id]);

        $this->actingAs($collector)->post(route('artifacts.documents.store', $artifact), [
            'title' => 'Authenticity Certificate',
            'document_type' => 'Certificate',
            'document' => UploadedFile::fake()->create('cert.pdf', 200, 'application/pdf'),
        ]);

        $this->assertDatabaseHas('artifact_documents', [
            'artifact_id' => $artifact->id,
            'title' => 'Authenticity Certificate',
        ]);
    }

    /** @test */
    public function a_non_owner_cannot_remove_a_document(): void
    {
        $owner = User::factory()->collector()->create();
        $intruder = User::factory()->collector()->create();
        $artifact = Artifact::factory()->create(['collector_id' => $owner->id, 'created_by' => $owner->id]);
        $document = ArtifactDocument::factory()->for($artifact)->create();

        $response = $this->actingAs($intruder)->delete(route('artifacts.documents.destroy', [$artifact, $document]));

        $response->assertForbidden();
        $this->assertDatabaseHas('artifact_documents', ['id' => $document->id]);
    }
}
