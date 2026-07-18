<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiTokenController extends Controller
{
    /**
     * Create a new personal access token.
     */
    public function store(Request $request)
    {
        $request->validate([
            'token_name' => ['required', 'string', 'max:255'],
        ]);

        $token = $request->user()->createToken($request->token_name);

        return back()->with('apiToken', $token->plainTextToken)
            ->with('status', 'api-token-created');
    }

    /**
     * Revoke a specific personal access token.
     */
    public function destroy(Request $request, $tokenId)
    {
        $request->user()->tokens()->where('id', $tokenId)->delete();

        return back()->with('status', 'api-token-deleted');
    }
}
