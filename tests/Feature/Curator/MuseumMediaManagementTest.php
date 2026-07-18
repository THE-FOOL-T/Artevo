<?php

namespace Tests\Feature\Curator;

use App\Models\Museum;
use App\Models\MuseumContact;
use App\Models\MuseumImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MuseumMediaManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_curator_can_upload_gallery_images_to_their_museum(): void
    {
        Storage::fake('public');

        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();

        $this->actingAs($curator)->post(route('curator.museums.images.store', $museum), [
            'images' => [UploadedFile::fake()->image('gallery1.jpg'), UploadedFile::fake()->image('gallery2.jpg')],
            'caption' => 'Opening night',
        ]);

        $this->assertSame(2, $museum->images()->count());
        Storage::disk('public')->assertExists($museum->images()->first()->image_path);
    }

    /** @test */
    public function a_curator_cannot_upload_images_to_another_curators_museum(): void
    {
        Storage::fake('public');

        $owner = User::factory()->curator()->create();
        $intruder = User::factory()->curator()->create();
        $museum = Museum::factory()->for($owner, 'curator')->create();

        $response = $this->actingAs($intruder)->post(route('curator.museums.images.store', $museum), [
            'images' => [UploadedFile::fake()->image('gallery1.jpg')],
        ]);

        $response->assertForbidden();
        $this->assertSame(0, $museum->images()->count());
    }

    /** @test */
    public function a_curator_can_remove_a_gallery_image(): void
    {
        Storage::fake('public');

        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();
        $image = MuseumImage::factory()->for($museum)->create(['image_path' => 'museums/gallery/test.jpg']);
        Storage::disk('public')->put('museums/gallery/test.jpg', 'fake-content');

        $this->actingAs($curator)->delete(route('curator.museums.images.destroy', [$museum, $image]));

        $this->assertDatabaseMissing('museum_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing('museums/gallery/test.jpg');
    }

    /** @test */
    public function a_curator_can_add_a_contact(): void
    {
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();

        $this->actingAs($curator)->post(route('curator.museums.contacts.store', $museum), [
            'label' => 'Press Inquiries',
            'email' => 'press@example.com',
        ]);

        $this->assertDatabaseHas('museum_contacts', [
            'museum_id' => $museum->id,
            'label' => 'Press Inquiries',
        ]);
    }

    /** @test */
    public function a_contact_requires_an_email_or_a_phone(): void
    {
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();

        $response = $this->actingAs($curator)->post(route('curator.museums.contacts.store', $museum), [
            'label' => 'Empty Contact',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function a_curator_can_remove_a_contact(): void
    {
        $curator = User::factory()->curator()->create();
        $museum = Museum::factory()->for($curator, 'curator')->create();
        $contact = MuseumContact::factory()->for($museum)->create();

        $this->actingAs($curator)->delete(route('curator.museums.contacts.destroy', [$museum, $contact]));

        $this->assertDatabaseMissing('museum_contacts', ['id' => $contact->id]);
    }
}
