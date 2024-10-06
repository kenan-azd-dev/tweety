<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Tweet;
use Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{

    /**
     * Return a list of all comments for a given tweet.
     *
     * @param Tweet $tweet
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Tweet $tweet): JsonResponse
    {
        if (Gate::denies('view', $tweet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comments = $tweet->comments()->with('user')->paginate(10); // Use pagination and eager loading

        return response()->json(CommentResource::collection($comments));
    }

    /**
     * Create a new comment for a given tweet.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Tweet $tweet
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Tweet $tweet): JsonResponse
    {
        if (Gate::denies('create', $tweet)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:255',
        ]);

        $comment = $tweet->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        return response()->json(new CommentResource($comment));
    }

    /**
     * Update an existing comment.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        if (Gate::denies('update', $comment)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:255',
        ]);

        $comment->update($validated);

        return response()->json(new CommentResource($comment));
    }

    /**
     * Delete a comment.
     *
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Comment $comment): JsonResponse
    {
        if (Gate::denies('update', $comment)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.'], 200);
    }
}
