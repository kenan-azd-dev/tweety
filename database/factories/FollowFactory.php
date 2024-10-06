<?php

// database/factories/FollowFactory.php

namespace Database\Factories;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FollowFactory extends Factory
{
    protected $model = Follow::class;

    // We will keep track of unique follower-followed pairs
    protected static $existingFollows = [];

    public function definition()
    {
        $follower = User::inRandomOrder()->first();
        $followed = User::inRandomOrder()->where('id', '!=', $follower->id)->first();

        // Create a unique key for the follower-followed pair
        $followKey = $follower->id . '-' . $followed->id;

        // Check if the pair already exists
        if (in_array($followKey, self::$existingFollows)) {
            return $this->definition(); // Retry with another pair
        }

        // Store this pair to prevent future duplicates
        self::$existingFollows[] = $followKey;

        return [
            'follower_id' => $follower->id,
            'followed_id' => $followed->id,
        ];
    }
}
