<?php

namespace App\Http\Controllers\Curator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Curator\StoreMuseumContactRequest;
use App\Models\Museum;
use App\Models\MuseumContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class MuseumContactController extends Controller
{
    public function store(StoreMuseumContactRequest $request, Museum $museum): RedirectResponse
    {
        $museum->contacts()->create($request->validated());

        return back()->with('success', 'Contact added.');
    }

    public function destroy(Museum $museum, MuseumContact $contact): RedirectResponse
    {
        Gate::authorize('manageMedia', $museum);

        abort_unless($contact->museum_id === $museum->id, 404);

        $contact->delete();

        return back()->with('success', 'Contact removed.');
    }
}
