<?php

namespace App\Services;

use App\Models\Block;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BlockService
{

    /**
     * Blocks a user, removing any follow or like relationships.
     *
     * @param User $blocker The user doing the blocking.
     * @param User $blocked The user being blocked.
     */
    public function block(User $blocker, User $blocked)
    {
        DB::transaction(function () use ($blocker, $blocked) {
            // 1. Unfollow each other if following
            $this->unfollowEachOther($blocker, $blocked);

            // 2. Remove likes between each other's tweets
            $this->removeMutualLikes($blocker, $blocked);

            // 3. Block the user
            Block::create([
                "blocker_id"=> $blocker->id,
                "blocked_id"=> $blocked->id,
            ]);
        });
    }


    /**
     * Unblocks a user, allowing them to interact with each other again.
     *
     * @param User $blocker The user who is unblocking.
     * @param User $blocked The user who was blocked.
     */
    public function unblock(User $blocker, User $blocked)
    {
        DB::transaction(function () use ($blocker, $blocked) {
            // Unblock the user
            $blocker->blockedUsers()->detach($blocked);
        });
    }


    /**
     * Checks if the blocker and blocked user are following each other and unfollows
     * them if they are.
     *
     * @param User $blocker The user doing the blocking.
     * @param User $blocked The user being blocked.
     */
    private function unfollowEachOther(User $blocker, User $blocked)
    {
        if ($blocker->isFollowing($blocked)) {
            $blocker->unfollow($blocked);
        }

        if ($blocked->isFollowing($blocker)) {
            $blocked->unfollow($blocker);
        }
    }


    /**
     * Removes any likes that the blocker and blocked user have on each other's
     * tweets.
     *
     * @param User $blocker The user doing the blocking.
     * @param User $blocked The user being blocked.
     */
    private function removeMutualLikes(User $blocker, User $blocked)
    {
        // Eager load tweets to avoid N+1 problem
        $blockerTweets = $blocker->tweets()->with('likes')->get();
        $blockedTweets = $blocked->tweets()->with('likes')->get();

        // Remove likes on blocked user's tweets
        DB::table('likes')
            ->where('user_id', $blocker->id)
            ->whereIn('tweet_id', $blockedTweets->pluck('id'))
            ->delete();

        // Remove likes on blocker's tweets
        DB::table('likes')
            ->where('user_id', $blocked->id)
            ->whereIn('tweet_id', $blockerTweets->pluck('id'))
            ->delete();
    }

}
