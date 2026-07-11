<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Renders the static marketing pages that don't yet need a dedicated
 * module of their own. As Artevo grows, this stays limited to purely
 * informational pages — anything backed by a database table gets its
 * own controller instead.
 */
class PageController extends Controller
{
    public function home(): View
    {
        return view('home');
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }
}
