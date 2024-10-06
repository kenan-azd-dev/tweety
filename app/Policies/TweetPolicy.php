<?php

namespace App\Policies;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TweetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given tweet can be updated by the user.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tweet  $tweet
     * @return bool
     */
    public function update(User $user, Tweet $tweet)
    {
        // A user can update a tweet only if they own it
        return $user->id === $tweet->user_id;
    }

    /**
     * Determine if the given tweet can be deleted by the user.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tweet  $tweet
     * @return bool
     */
    public function delete(User $user, Tweet $tweet)
    {
        // A user can delete a tweet only if they own it
        return $user->id === $tweet->user_id;
    }
}
