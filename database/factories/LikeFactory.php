<?php
// database/factories/LikeFactory.php

namespace Database\Factories;

use App\Models\Like;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    protected $model = Like::class;

    // We will keep track of unique combinations
    protected static $existingLikes = [];

    public function definition()
    {
        $user = User::inRandomOrder()->first();
        $tweet = Tweet::inRandomOrder()->first();

        // Create a unique key for the user-tweet pair
        $likeKey = $user->id . '-' . $tweet->id;

        // Check if the pair already exists
        if (in_array($likeKey, self::$existingLikes)) {
            return $this->definition(); // Retry with another pair
        }

        // Store this pair to prevent future duplicates
        self::$existingLikes[] = $likeKey;

        return [
            'user_id' => $user->id,
            'tweet_id' => $tweet->id,
        ];
    }
}

