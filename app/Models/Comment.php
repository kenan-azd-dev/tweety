<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tweet_id',
        'body',
    ];

    protected static function booted(): void
    {
        static::created(function ($comment): void {
            $comment->tweet->increment('comments_count');
        });

        static::deleted(function ($comment): void {
            $comment->tweet->decrement('comments_count');
        });
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tweet(): BelongsTo
    {
        return $this->belongsTo(Tweet::class);
    }
}
