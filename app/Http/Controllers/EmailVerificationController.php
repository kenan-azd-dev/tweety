<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;

class EmailVerificationController extends Controller
{
    /**
     * Verify the user's email.
     */
    public function verify(Request $request): JsonResponse
    {
        // Assuming that the request has user ID and hash for email verification
        $user = User::findOrFail($request->id);

        // First, check if the user has already verified the email
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        // Decode the hash from the URL
        $decodedHash = urldecode($request->hash);

        // Check if the hash matches the email verification hash
        if (!hash_equals($decodedHash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid or expired verification link.'], 400);
        }

        // Mark the email as verified and trigger the verification event
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Return success response
        return response()->json(['message' => 'Email successfully verified.']);
    }


    /**
     * Resend the email verification link.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email is already verified.'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent!']);
    }
}
