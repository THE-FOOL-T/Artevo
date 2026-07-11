<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_visitor_can_submit_the_contact_form(): void
    {
        Mail::fake();

        $response = $this->post(route('contact.store'), [
            'name' => 'Amelia Hart',
            'email' => 'amelia@example.com',
            'category' => 'museum_inquiry',
            'subject' => 'Partnership question',
            'message' => 'We would like to discuss listing our collection on Artevo.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'amelia@example.com',
            'category' => 'museum_inquiry',
        ]);

        Mail::assertQueued(ContactMessageReceived::class);
    }

    /** @test */
    public function the_contact_form_requires_a_name_email_and_message(): void
    {
        $response = $this->post(route('contact.store'), [
            'name' => '',
            'email' => 'not-an-email',
            'category' => 'general',
            'message' => 'too short',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    /** @test */
    public function the_contact_form_rejects_an_invalid_category(): void
    {
        $response = $this->post(route('contact.store'), [
            'name' => 'Amelia Hart',
            'email' => 'amelia@example.com',
            'category' => 'not-a-real-category',
            'message' => 'This message is long enough to pass validation.',
        ]);

        $response->assertSessionHasErrors('category');
    }
}
