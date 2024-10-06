<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ProfileController extends Controller
{
    public function followers(User $user): JsonResponse
    {
        if (Gate::denies('viewProfile', $user)) {
            return response()->json(['message' => 'You do not have permission to view this profile.'], 403);
        }

        return response()->json([
            'followers' => $user->followers(),
        ]);
    }

    public function following(User $user): JsonResponse
    {
        if (Gate::denies('viewProfile', $user)) {
            return response()->json(['message' => 'You do not have permission to view this profile.'], 403);
        }

        return response()->json([
            'following' => $user->following(),
        ]);
    }
}