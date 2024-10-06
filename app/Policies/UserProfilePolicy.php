<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserProfilePolicy
{
    use HandlesAuthorization;

    public function before(User $currentUser, User $otherUser)
    {
        if ($otherUser->isBlocking($currentUser)) {
            return false;
        }
    }

    /**
     * Determine if the current user can view the other given user's profile.
     *
     * @param  \App\Models\User  $currentUser
     * @param  \App\Models\User  $otherUser
     * @return bool
     */
    public function viewProfile(User $currentUser, User $otherUser): bool
    {
        return !$otherUser->is_private || $currentUser->id === $otherUser->id || $currentUser->isFollowing($otherUser);
    }

}
