<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handles login and returns an authentication token.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function store(LoginRequest $request): JsonResponse
    {
        // Authenticate
        $request->authenticate();

        // Regenerate
        $request->session()->regenerate();

        // Generate an API token
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    /**
     * Destroy an authenticated session and return a JSON response.
     * 
     * Revoke the user's token, and return a successful logout message.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(): JsonResponse
    {
        // Revoke the user's token
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful.'
        ], 200);
    }

    /**
     * Logs out the user of all sessions.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAll(Request $request): JsonResponse
    {
        // Revoke all tokens for the user
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out from all devices.'], 200);
    }
}
