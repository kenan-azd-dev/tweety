<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\AgeValidationRule;
use App\Services\ImageService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    /**
     * Handles an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the incoming request
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'min:10', 'max:15', 'unique:users'],
            'birth_date' => ['required', 'date', new AgeValidationRule()],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised()],
            'password_confirmation' => ['required', 'same:password'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'bio' => ['nullable', 'string', 'max:255'],
        ]);

        // Save image if it exists
        $mediaPath = $request->hasFile('media')
            ? $this->imageService->storeFile($request->file('media'), 'profile_photos')
            : null;

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_photo_path' => $mediaPath,
            'bio' => $request->bio,
        ]);

        // Log the user in
        $token = $user->createToken($request->email)->plainTextToken;

        // Dispatch the email verification notification
        event(new Registered($user));

        return response()->json([
            'message' => 'User created successfully! Please verify your email.',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // Validate the request
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'birth_date' => ['sometimes', 'date', new AgeValidationRule()],
            'is_private' => ['sometimes', 'boolean'],
            'bio' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'min:10', 'max:15', 'unique:users,phone,' . $user->id],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'current_password' => ['sometimes', 'required_with:password,email', 'string'], // Require current password for email or password change
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(), 'confirmed'], // Add password confirmation
            'email' => ['sometimes', 'email', 'unique:users,email,' . $user->id], // Email validation
        ]);

        // Check if profile photo exists and store it
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                $this->imageService->deleteFile($user->profile_photo_path);
            }

            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo_path = $path;
        }

        // Check if user wants to update their password
        if ($request->filled('password')) {
            // Verify the current password before allowing the update
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => 'The current password is incorrect.'], 400);
            }

            // Hash and update the new password
            $user->password = Hash::make($request->password);
        }

        // Handle email update
        if ($request->filled('email') && $request->email !== $user->email) {
            // Verify current password before changing email
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => 'The current password is incorrect.'], 400);
            }

            // Update email only after verification
            $user->email = $request->email;
            $user->email_verified_at = null; // Unverify email if it's changed
            $user->save();

            // Send new verification email
            $user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Profile updated successfully. Please verify your new email.',
                'user' => $user,
            ]);
        }

        // Update other fields
        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        if ($request->filled('username')) {
            $user->username = $request->username;
        }
        if ($request->filled('birth_date')) {
            $user->birth_date = $request->birth_date;
        }
        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->filled('bio')) {
            $user->bio = $request->bio;
        }
        if ($request->filled('is_private')) {
            $user->is_private = $request->is_private;
        }

        // Save updated user
        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user,
        ]);
    }



    /**
     * Delete the authenticated user's account and related data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        // Get the authenticated user
        $user = $request->user();

        // 1. Revoke all tokens (logout)
        $user->tokens()->delete();

        // 2. Delete all tweets/media related to the user
        $user->tweets()->each(function ($tweet) {
            // If the tweet has media, delete the media from storage
            if ($tweet->media_path) {
                $this->imageService->deleteFile($tweet->media_path);
            }
            $tweet->delete(); // Delete the tweet
        });

        // 3. Optionally delete user's profile photo if exists
        if ($user->profile_photo_path) {
            $this->imageService->deleteFile($user->profile_photo_path);
        }

        // 4. Finally, delete the user account
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully.'], 200);
    }
}
