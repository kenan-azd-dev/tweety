<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    public function view(User $user)
    {
        if ($user->is_private) {
            return false;
        }
        return true;
    }

    public function create(User $user, Tweet $tweet)
    {
        if ($tweet->user->is_private) {
            return $tweet->user->isFollowedBy($user);
        }
        return true;
    }

    public function update(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id;
    }

    public function delete(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id || $user->id === $comment->tweet->user_id;
    }
}
