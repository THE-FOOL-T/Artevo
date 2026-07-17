<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveContactMessageRequest;
use App\Models\ContactMessage;
use App\Services\ContactService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Public "Contact Artevo" page: general support, museum partnership
 * inquiries, bug reports and feature suggestions all flow through the
 * same form, tagged by category.
 */
class ContactController extends Controller
{
    public function __construct(private ContactService $contactService)
    {
    }

    public function create(): View
    {
        return view('pages.contact', [
            'categories' => ContactMessage::CATEGORIES,
        ]);
    }

    public function store(SaveContactMessageRequest $request): RedirectResponse
    {
        $this->contactService->submit($request->validated(), $request);

        return back()->with('success', "Thanks for reaching out — we'll reply within 1–2 business days.");
    }
}
