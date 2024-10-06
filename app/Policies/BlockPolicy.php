<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BlockPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the current user can block the given user.
     *
     * A user can block another user if:
     * - They are not the same user
     * - They have not already blocked the user
     * - The other user has not blocked them
     *
     * @param  \App\Models\User  $currentUser
     * @param  \App\Models\User  $otherUser
     * @return bool
     */
    public function block(User $currentUser, User $otherUser): bool
    {
        if ($this->isSameUser($currentUser, $otherUser)) {
            return false;
        }

        if ($currentUser->isBlocking($otherUser) || $otherUser->isBlocking($currentUser)) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the current user can unblock the given user.
     *
     * A user can unblock another user if they are not the same user and
     * they have previously blocked the user.
     *
     * @param  \App\Models\User  $currentUser
     * @param  \App\Models\User  $otherUser
     * @return bool
     */
    public function unblock(User $currentUser, User $otherUser): bool
    {
        if ($this->isSameUser($currentUser, $otherUser)) {
            return false;
        }

        return $currentUser->isBlocking($otherUser);
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
