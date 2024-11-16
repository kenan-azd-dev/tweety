<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ProfileController extends Controller
{
    /**
     * Retrieve the list of users that follow the given user.
     * 
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function followers(User $user): JsonResponse
    {
        if (Gate::denies('viewProfile', $user)) {
            return response()->json(['message' => 'You do not have permission to view this profile.'], 403);
        }

        return response()->json([
            'followers' => $user->followers(),
        ]);
    }

    /**
     * Retrieve the list of users that the given user is following.
     * 
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function following(User $user): JsonResponse
    {
        if (Gate::denies('viewProfile', $user)) {
            return response()->json(['message' => 'You do not have permission to view this profile.'], 403);
        }

        return response()->json([
            'following' => $user->following(),
        ]);
    }


    /**
     * Display the specified user by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        $user = User::findOrFail($user->id);
        return response()->json($user);
    }

}