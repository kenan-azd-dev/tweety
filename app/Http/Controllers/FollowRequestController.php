<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Follow;
use App\Models\FollowRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FollowRequestController extends Controller
{

    /**
     * Send a follow request.
     *
     * @param  \App\Models\User  $receiver
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(User $receiver): JsonResponse
    {
        $sender = Auth::user();

        // Authorize the request using the policy
        if (Gate::denies('send', $receiver)) {
            return response()->json(['message' => 'You cannot send a follow request to this user.'], 403);
        }

        // Send the follow request
        FollowRequest::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        return response()->json(['message' => 'Follow request sent successfully!'], 201);
    }

    /**
     * Cancel a follow request that has been sent by the current user.
     *
     * @param  \App\Models\User  $receiver
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $receiver): JsonResponse
    {
        $sender = Auth::user();

        // Find the follow request
        $followRequest = FollowRequest::where('sender_id', $sender->id)
            ->where('receiver_id', $receiver->id)
            ->first();

        if (!$followRequest) {
            return response()->json(['message' => 'Follow request not found.'], 404);
        }

        // Authorize the request using the policy
        if (Gate::denies('cancel', $followRequest)) {
            return response()->json(['message' => 'Unauthorized. You are not the sender of this follow request.'], 403);
        }

        $followRequest->delete();

        return response()->json(['message' => 'Follow request canceled successfully.'], 200);
    }

    /**
     * Accept a follow request from the given user.
     *
     * @param  \App\Models\User  $sender
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept(User $sender): JsonResponse
    {
        $receiver = Auth::user();

        // Find the follow request
        $followRequest = FollowRequest::where('sender_id', $sender->id)
            ->where('receiver_id', $receiver->id)
            ->first();

        if (!$followRequest) {
            return response()->json(['message' => 'Follow request not found.'], 404);
        }

        // Authorize the request using the policy
        if (Gate::denies('accept', $followRequest)) {
            return response()->json(['message' => 'Unauthorized. You are not the receiver of this follow request.'], 403);
        }

        // Accept the follow request by following the user
        Follow::create([
            'follower_id' => $sender->id,
            'followed_id' => $receiver->id
        ]);

        // Delete the follow request
        $followRequest->delete();

        return response()->json(['message' => 'Follow request accepted.'], 200);
    }


    /**
     * Decline a follow request from the given user.
     *
     * @param  \App\Models\User  $sender
     * @return \Illuminate\Http\JsonResponse
     */
    public function decline(User $sender): JsonResponse
    {
        $receiver = Auth::user();

        // Find the follow request
        $followRequest = FollowRequest::where('sender_id', $sender->id)
            ->where('receiver_id', $receiver->id)
            ->first();

        if (!$followRequest) {
            return response()->json(['message' => 'Follow request not found.'], 404);
        }

        // Authorize the request using the policy
        if (Gate::denies('decline', $followRequest)) {
            return response()->json(['message' => 'Unauthorized. You are not the receiver of this follow request.'], 403);
        }

        // Decline the follow request by deleting it
        $followRequest->delete();

        return response()->json(['message' => 'Follow request declined.'], 200);
    }


    /**
     * Retrieve all received follow requests.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function received(): JsonResponse
    {
        $user = Auth::user();

        // Get the pending follow requests
        $receivedFollowRequests = $user->receivedFollowRequests()->with('sender')->get();

        // Transform the pending requests using UserResource
        return response()->json([
            'data' => $receivedFollowRequests->map(function ($followRequest) {
                return [
                    'user' => new UserResource($followRequest->sender),
                    'requested_at' => $followRequest->created_at->toDateTimeString(), // Date of the follow request
                ];
            }),
        ]);
    }


    /**
     * Retrieve all sent follow requests.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sent(): JsonResponse
    {
        $user = Auth::user();

        // Get the sent follow requests
        $sentFollowRequests = $user->sentFollowRequests()->with('receiver')->get();

        // Transform the pending requests using UserResource
        return response()->json([
            'data' => $sentFollowRequests->map(function ($followRequest) {
                return [
                    'user' => new UserResource($followRequest->receiver),
                    'requested_at' => $followRequest->created_at->toDateTimeString(), // Date of the follow request
                ];
            }),
        ]);
    }
}
