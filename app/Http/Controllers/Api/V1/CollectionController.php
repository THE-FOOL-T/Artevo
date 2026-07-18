<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        // For API Phase 1, just return simple array/JSON of user's collections
        return $request->user()->collections()->paginate(15);
    }
}
