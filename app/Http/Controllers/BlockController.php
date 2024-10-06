<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\BlockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;

class BlockController extends Controller
{
    protected $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Returns a list of all users that the given user is currently blocking.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $listOfBlockedUsers = $user->blockedUsers()->with('users')->get();
        return response()->json(UserResource::collection($listOfBlockedUsers));
    }

    /**
     * Block a user.
     *
     * @param  \App\Models\User  $blockedUser
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(User $blockedUser): JsonResponse
    {
        // Authorize the block action using the BlockPolicy
        if (Gate::denies('block', $blockedUser)) {
            return response()->json(['message' => 'You cannot block this user.'], 403);
        }

        /** @var User $blocker */
        $blocker = Auth::user();

        // Block the user via BlockService
        $this->blockService->block($blocker, $blockedUser);

        return response()->json(['message' => 'User has been blocked successfully.'], 200);
    }


    /**
     * Unblock a user.
     *
     * @param  \App\Models\User  $blockedUser
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $blockedUser): JsonResponse
    {

        // Authorize the unblock action using the BlockPolicy
        if (Gate::denies('unblock', $blockedUser)) {
            return response()->json(['message' => 'You cannot unblock this user.'], 403);
        }

        /** @var User $blocker */
        $blocker = Auth::user();

        // Unblock the user via BlockService
        $this->blockService->unblock($blocker, $blockedUser);

        return response()->json(['message' => 'User has been unblocked successfully.'], 200);
    }
}
