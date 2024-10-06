<?php

namespace App\Models;

use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, PivotEventTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'birth_date',
        'bio',
        'profile_photo_path',
        'phone',
        'email',
        'password',
        'is_private',
        'tweets_count',
        'following_count',
        'followers_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [ // Fixed method signature
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Listen for pivot attachment and detachment events on the 'following' relationship.
     * When a user follows/unfollows another user, increment/decrement the 'following_count' and 'followers_count' fields.
     */
    protected static function booted()
    {

        // When a user unfollows another user
        static::pivotDetached(function ($relation, $parent, $pivotIds) {
            if ($relation === 'following') {
                $parent->decrement('following_count');
                User::find($pivotIds)->first()->decrement('followers_count');
            }
        });
    }

    /**
     * Get the tweets written by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Tweet>
     */
    public function tweets(): HasMany
    {
        return $this->hasMany(Tweet::class);
    }

    /**
     * Get the users that follow the user.
     *
     * @return BelongsToMany<User>
     */
    public function followers(): HasMany
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    /**
     * Get the users that are followed by the user.
     *
     * @return BelongsToMany<User>
     */
    public function following(): HasMany
    {
        return $this->hasMany(Follow::class, 'followed_id');
    }

    /**
     * Check if the current user is following the given user.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function isFollowing(User $user): bool
    {
        return $this->following()->where('followed_id', $user->id)->exists();
    }

    /**
     * Get the sent follow requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<FollowRequest>
     */
    public function sentFollowRequests(): HasMany
    {
        return $this->hasMany(FollowRequest::class, 'sender_id');
    }

    /**
     * Get the received follow requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<FollowRequest>
     */
    public function receivedFollowRequests(): HasMany
    {
        return $this->hasMany(FollowRequest::class, 'receiver_id');
    }

    /**
     * Check if the user has sent a follow request to the given user.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function hasSentFollowRequestTo(User $user)
    {
        return $this->sentFollowRequests()->where('receiver_id', $user->id)->exists();
    }

    /**
     * Get the users that the user is blocking.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User>
     */
    public function blockedUsers(): HasMany
    {
        return $this->hasMany(Block::class, 'blocked_id');
    }

    /**
     * Check if the current user is blocking the given user.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function isBlocking(User $user): bool
    {
        return $this->blockedUsers()->where('blocked_id', $user->id)->exists();
    }

    /**
     * Get the tweets that the user has liked.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Tweet>
     */
    public function likedTweets(): BelongsToMany
    {
        return $this->belongsToMany(Tweet::class, 'likes')->withTimestamps();
    }

}