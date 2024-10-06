<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use App\Http\Resources\UserResource;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TweetController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Stores a new tweet and returns it as JSON.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'body' => 'required|string|max:280',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Store image if it exists
        $mediaPath = $request->hasFile('image')
            ? $this->imageService->storeFile($request->file('image'), 'tweets_images')
            : null;

        $tweet = Tweet::create([
            'user_id' => Auth::user()->id,
            'body' => $request->body,
            'image_path' => $mediaPath
        ]);

        return response()->json($tweet, 201);
    }

    /**
     * Retrieves a single tweet by ID.
     * 
     * @param int $id The ID of the tweet to retrieve.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Tweet $tweet): JsonResponse
    {
        $tweet->load('user');
        return response()->json($tweet);
    }

    /**
     * Retrieve all tweets by a specific user ID.
     * 
     * @param int $userId The ID of the user whose tweets to retrieve.
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(User $user): JsonResponse
    {
        if (Gate::denies('viewProfile', $user)) {
            return response()->json(['message' => 'This account is private.'], 403);
        }

        $tweets = $user->tweets()->with('user')->paginate(10);

        $tweets->getCollection()->transform(function ($tweet) {
            $tweet->user = new UserResource($tweet->user); // Transform user to UserResource
            return $tweet;
        });

        return response()->json($tweets);
    }

    /**
     * Updates a tweet by ID.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id The ID of the tweet to update.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Tweet $tweet): JsonResponse
    {
        // Use the Gate to authorize the action
        if (Gate::denies('update', $tweet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'body' => 'required|string|max:280',
        ]);

        $tweet->update($request->only('body'));

        return response()->json($tweet);
    }

    /**
     * Deletes a tweet by ID.
     * 
     * @param int $id The ID of the tweet to delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tweet $tweet): JsonResponse
    {
        // Use the Gate to authorize the action
        if (Gate::denies('delete', $tweet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($tweet->media_path) {
            $this->imageService->deleteFile($tweet->media_path);
        }

        $tweet->delete();

        return response()->json(['message' => 'Tweet deleted']);
    }
}
