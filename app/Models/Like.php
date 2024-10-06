<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'tweet_id'];

    protected static function booted()
    {
        static::created(function ($like) {
            $like->tweet->increment('likes_count');
        });

        static::deleted(function ($like) {
            $like->tweet->decrement('likes_count');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tweet()
    {
        return $this->belongsTo(Tweet::class);
    }
}

