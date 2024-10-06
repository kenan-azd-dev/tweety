<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FollowRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class FollowRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can send a follow request to the receiver.
     *
     * @param  \App\Models\User  $sender
     * @param  \App\Models\User  $receiver
     * @return bool
     */
    public function send(User $sender, User $receiver): bool
    {
        if ($this->isSameUser($sender, $receiver)) {
            return false;
        }

        if ($sender->isFollowing($receiver) || $sender->hasSentFollowRequestTo($receiver)) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the given user can cancel a follow request they have sent.
     *
     * @param  \App\Models\User  $sender
     * @param  \App\Models\FollowRequest  $followRequest
     * @return bool
     */
    public function cancel(User $sender, FollowRequest $followRequest): bool
    {
        return $sender->id === $followRequest->sender_id;
    }

    /**
     * Determine if the given user can accept a follow request.
     *
     * @param  \App\Models\User  $receiver
     * @param  \App\Models\FollowRequest  $followRequest
     * @return bool
     */
    public function accept(User $receiver, FollowRequest $followRequest): bool
    {
        return $this->isReceiver($receiver, $followRequest);
    }

    /**
     * Determine if the given user can decline a follow request.
     *
     * @param  \App\Models\User  $receiver
     * @param  \App\Models\FollowRequest  $followRequest
     * @return bool
     */
    public function decline(User $receiver, FollowRequest $followRequest): bool
    {
        return $this->isReceiver($receiver, $followRequest);
    }

    /**
     * Check if the given user is the receiver of the follow request.
     *
     * @param  \App\Models\User  $receiver
     * @param  \App\Models\FollowRequest  $followRequest
     * @return bool
     */
    private function isReceiver(User $receiver, FollowRequest $followRequest): bool
    {
        return $receiver->id === $followRequest->receiver_id;
    }

    /**
     * Check if the current user is the same as the other user.
     *
     * @param  \App\Models\User  $userA
     * @param  \App\Models\User  $userB
     * @return bool
     */
    private function isSameUser(User $userA, User $userB): bool
    {
        return $userA->id === $userB->id;
    }
}
