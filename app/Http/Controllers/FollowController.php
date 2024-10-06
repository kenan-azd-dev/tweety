<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\FollowRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FollowController extends Controller
{
    /**
     * Follow a user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(User $user): JsonResponse
    {
        // Check if the user is authorized to follow the other user
        if (Gate::denies('follow', $user)) {
            return response()->json([
                'message' => 'You are not allowed to follow this user.'
            ], 403);
        }

        $currentUser = Auth::user();

        Follow::create([
            'follower_id' => $currentUser->id,
            'followed_id' => $user->id
        ]);

        return response()->json(['message' => 'Successfully followed user.'], 200);
    }

    /**
     * Unfollow a user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        $follower = Auth::user();

        $follow = Follow::where('sender_id', $follower->id)
            ->where('receiver_id', $user->id)
            ->first();

        if (!$follow) {
            return response()->json([
                'message' => 'Follow not found.'
            ], 404);
        }

        // Check if the user is authorized to unfollow the other user
        if (Gate::denies('unfollow', $user)) {
            return response()->json([
                'message' => 'You are not allowed to unfollow this user.'
            ], 403);
        }

        $follow->delete();

        return response()->json(['message' => 'Successfully unfollowed user.'], 200);
    }

}
