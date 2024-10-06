<?php

namespace App\Policies;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LikePolicy
{
    use HandlesAuthorization;

    // Users cannot like their own tweet
    public function like(User $user, Tweet $tweet): bool
    {
        return !$tweet->user()->isBlocking($user) && !$tweet->likes()->where('user_id', $user->id)->exists();
    }

    // Users can unlike only if they have already liked the tweet
    public function unlike(User $user, Tweet $tweet): bool
    {
        return $tweet->likes()->where('user_id', $user->id)->exists();
    }

    // Users can view the likers if they can view the tweet
    public function viewLikers(User $user, Tweet $tweet): bool
    {
        if ($tweet->user->is_private) {
            return $tweet->user->isFollowedBy($user);
        }

        return true;
    }
}
