<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the request input
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'current_password' => ['required_with:password,email', 'string'],
        ]);

        // Retrieve the user by email
        $user = \App\Models\User::where('email', $request->email)->first();

        // Check if the current password matches
        if (!$user || !Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'The provided current password is incorrect.'], 400);
        }

        // Proceed with the password reset
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->string('password')),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // Handle password reset failure
        if ($status != Password::PASSWORD_RESET) {
            return response()->json(['error' => __($status)], 400);
        }

        // Respond with success
        return response()->json(['status' => __($status)], 200);
    }
}
