<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Follow extends Model
{
    use HasFactory;
    protected $fillable = [
        'follower_id',
        'followed_id',
    ];

    protected static function booted(): void
    {
        static::created(function ($follow): void {
            $follow->follower->increment('followers_count');
            $follow->followed->increment('following_count');
        });

        static::deleted(function ($follow): void {
            $follow->follower->decrement('followers_count');
            $follow->followed->decrement('following_count');
        });
    }

    /**
     * The user who is following another user.
     *
     * @return BelongsTo
     */
    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    /**
     * The user who is followed by another user.
     *
     * @return BelongsTo
     */
    public function followed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'followed_id');
    }
}
