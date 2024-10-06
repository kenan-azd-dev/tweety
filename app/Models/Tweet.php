<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tweet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'body',
        'image_path',
    ];

    protected static function booted()
    {
        static::created(function ($tweet) {
            $tweet->user->increment('tweets_count');
        });

        static::deleted(function ($tweet) {
            $tweet->user->decrement('tweets_count');
        });
    }

    /**
     * The user who posted the tweet.
     *
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The users who have liked the tweet.
     *
     * @return BelongsToMany<User>
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

}
