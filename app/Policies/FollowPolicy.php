<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FollowPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the current user can follow the given profile user.
     *
     * @param  \App\Models\User  $currentUser
     * @param  \App\Models\User  $otherUser
     * @return bool
     */
    public function follow(User $currentUser, User $otherUser): bool
    {
        if ($this->isSameUser($currentUser, $otherUser)) {
            return false;
        }

        if ($otherUser->isBlocking($currentUser) || $currentUser->isBlocking($otherUser)) {
            return false;
        }

        if ($currentUser->isFollowing($otherUser) || $otherUser->is_private) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the current user can unfollow the given profile user.
     *
     * @param  \App\Models\User  $currentUser
     * @param  \App\Models\User  $otherUser
     * @return bool
     */
    public function unfollow(User $currentUser, User $otherUser): bool
    {
        if ($this->isSameUser($currentUser, $otherUser)) {
            return false;
        }

        return $currentUser->isFollowing($otherUser) && !$otherUser->isBlocking($currentUser);
    }

    /**
     * Check if the current user is the same as the other user.
     *
     * @param  \App\Models\User  $currentUser
     * @param  \App\Models\User  $otherUser
     * @return bool
     */
    private function isSameUser(User $currentUser, User $otherUser): bool
    {
        return $currentUser->id === $otherUser->id;
    }
}
