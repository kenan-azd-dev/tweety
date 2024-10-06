<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Tweet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Gate;

class LikeController extends Controller
{
    /**
     * Like a tweet.
     *
     * @param \App\Models\Tweet $tweet
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Tweet $tweet): JsonResponse
    {
        if (Gate::denies('like', $tweet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if the user has already liked the tweet
        if ($tweet->likes()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'You have already liked this tweet.'], 409);
        }

        $like = $tweet->likes()->create([
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Tweet liked successfully.', 'like' => $like], 201);
    }

    /**
     * Unlike a tweet.
     *
     * @param \App\Models\Tweet $tweet
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tweet $tweet): JsonResponse
    {
        if (Gate::denies('unlike', $tweet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Find the like and delete it
        $like = $tweet->likes()->where('user_id', auth()->id())->firstOrFail();
        $like->delete();

        return response()->json(['message' => 'Tweet unliked successfully.'], 200);
    }

    /**
     * View the users who have liked a tweet.
     *
     * @param \App\Models\Tweet $tweet
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Tweet $tweet): JsonResponse
    {
        if (Gate::denies('view', $tweet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $likers = $tweet->likes()->with('user')->paginate(10);

        return response()->json(UserResource::collection($likers->pluck('user')));
    }
}
